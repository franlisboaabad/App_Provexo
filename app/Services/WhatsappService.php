<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappService
{
    /**
     * Envía un mensaje de WhatsApp
     *
     * @param string $message Mensaje a enviar
     * @param string|null $number Número de teléfono (con código de país, ej: 51987654321). Si es null, usa el de configuración
     * @return bool
     */
    public static function send(string $message, ?string $number = null): bool
    {
        try {
            // Validar configuración
            $instance = config('whatsapp.instance');
            $apiToken = config('whatsapp.api_token');

            if (empty($instance)) {
                Log::error('WhatsApp: instance no configurada. Agrega WHATSAPP_INSTANCE en tu archivo .env');
                return false;
            }

            if (empty($apiToken)) {
                Log::error('WhatsApp: api_token no configurado. Agrega WHATSAPP_API_TOKEN en tu archivo .env');
                return false;
            }

            // Construir URL
            $url = 'https://apiwsp.factiliza.com/v1/message/sendtext/' . $instance;

            // Validar número
            $numeroEnviar = $number ?? config('whatsapp.number_send');
            if (empty($numeroEnviar)) {
                Log::error('WhatsApp: número de teléfono no proporcionado');
                return false;
            }

            $payload = [
                'number' => $numeroEnviar,
                'text' => $message
            ];

            // Log de debug (solo en desarrollo)
            if (config('app.debug')) {
                Log::debug('WhatsApp: Enviando mensaje', [
                    'url' => $url,
                    'number' => $numeroEnviar,
                    'message_length' => strlen($message)
                ]);
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiToken,
                'Content-Type' => 'application/json',
            ])->withoutVerifying() // Ignorar verificación SSL en desarrollo
                ->timeout(10) // Timeout de 10 segundos
                ->retry(2, 100) // Solo 2 reintentos con 100ms de espera
                ->post($url, $payload);

            if ($response->successful()) {
                $data = $response->json();
                // La API devuelve "succes" (con una 's') en lugar de "success"
                if (($data['success'] ?? $data['succes'] ?? false) === true) {
                    Log::info('WhatsApp enviado exitosamente', [
                        'number' => $numeroEnviar,
                        'response' => $data['message'] ?? 'OK'
                    ]);
                    return true;
                } else {
                    Log::error('Error en respuesta de WhatsApp', [
                        'response' => $data,
                        'message' => $data['message'] ?? 'Error desconocido'
                    ]);
                    return false;
                }
            } else {
                // Intentar obtener el mensaje de error de la respuesta
                $errorData = $response->json();
                $errorMessage = $errorData['message'] ?? $response->body();

                Log::error('Error HTTP en WhatsApp', [
                    'status' => $response->status(),
                    'url' => $url,
                    'number' => $numeroEnviar,
                    'error_message' => $errorMessage,
                    'full_response' => $errorData
                ]);

                // Si el error es 400 y menciona que el número no existe, loguear más información
                if ($response->status() === 400 && strpos($errorMessage, 'exists') !== false) {
                    Log::warning('WhatsApp: El número no existe en WhatsApp o no tiene cuenta activa', [
                        'number' => $numeroEnviar,
                        'suggestion' => 'Verifica que el número tenga código de país (ej: 51922852443) y que tenga WhatsApp activo'
                    ]);
                }

                return false;
            }
        } catch (Exception $e) {
            Log::error('Error enviando WhatsApp', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }


    /**
     * Envía notificación al cliente sobre el cambio de estado de entrega
     *
     * @param \App\Models\Venta $venta
     * @param string $nuevoEstado
     * @param string|null $observaciones
     * @return bool
     */
    public static function notificarCambioEstadoEntrega($venta, string $nuevoEstado, ?string $observaciones = null): bool
    {
        try {
            // Cargar relaciones necesarias
            $venta->load([
                'cotizacion.cliente.user',
                'cotizacion.productos.producto',
                'cotizacion'
            ]);

            // Obtener número del cliente
            $numeroCliente = $venta->cotizacion->cliente->celular ?? null;

            if (!$numeroCliente) {
                Log::warning('No se puede enviar WhatsApp: cliente sin número de celular', [
                    'venta_id' => $venta->id,
                    'cliente_id' => $venta->cotizacion->cliente_id ?? null
                ]);
                return false;
            }

            // Limpiar número (remover espacios, guiones, etc.)
            $numeroCliente = preg_replace('/[^0-9]/', '', $numeroCliente);

            // Asegurar que el número tenga código de país (si no lo tiene, agregar 51 para Perú)
            // Si el número tiene menos de 10 dígitos, probablemente le falta el código de país
            if (strlen($numeroCliente) < 10) {
                Log::warning('WhatsApp: Número muy corto, podría faltar código de país', [
                    'numero_original' => $venta->cotizacion->cliente->celular ?? null,
                    'numero_limpio' => $numeroCliente
                ]);
            }

            // Si el número no empieza con código de país (51 para Perú), agregarlo
            // Asumimos que números peruanos sin código de país empiezan con 9
            if (substr($numeroCliente, 0, 2) !== '51' && substr($numeroCliente, 0, 1) === '9') {
                $numeroCliente = '51' . $numeroCliente;
                Log::info('WhatsApp: Se agregó código de país al número', [
                    'numero_original' => $venta->cotizacion->cliente->celular ?? null,
                    'numero_final' => $numeroCliente
                ]);
            }

            // Obtener texto del estado
            $textoEstado = \App\Models\Venta::getTextoEstadoEntregaCliente($nuevoEstado);

            // Obtener información del cliente
            $cliente = $venta->cotizacion->cliente ?? null;
            $nombreCliente = $cliente->user->name ?? $cliente->empresa ?? 'Cliente';

            // Construir mensaje
            $message = "*Provexo+*\n\n";
            $message .= "📦 *Actualización de tu Pedido*\n\n";
            $message .= "Hola " . $nombreCliente . ", te informamos sobre el estado de tu pedido:\n\n";

            // Información básica
            $message .= "🆔 *Código de Seguimiento:* " . ($venta->codigo_seguimiento ?? 'N/A') . "\n";
            $message .= "📄 *Cotización:* " . ($venta->cotizacion->numero_cotizacion ?? 'N/A') . "\n";
            $message .= "📋 *Estado Actual:* " . $textoEstado . "\n";
            $message .= "📅 *Fecha de Actualización:* " . now()->format('d/m/Y H:i') . "\n\n";

            // Dirección de entrega (si está disponible)
            if ($venta->direccion_entrega) {
                $message .= "📍 *Dirección de Entrega:*\n";
                $direccionCompleta = array_filter([
                    $venta->direccion_entrega,
                    $venta->distrito,
                    $venta->provincia,
                    $venta->ciudad
                ]);
                $message .= implode(', ', $direccionCompleta) . "\n";
                if ($venta->referencia) {
                    $message .= "🔖 *Referencia:* " . $venta->referencia . "\n";
                }
                $message .= "\n";
            }

            $message .= "Puedes revisar el estado de tu pedido en la plataforma: ".env('APP_URL')."\n\n";

            // Observaciones
            if ($observaciones) {
                $message .= "💬 *Observaciones:*\n" . $observaciones . "\n\n";
            }

            // Mensaje de cierre
            $message .= "Gracias por tu preferencia. 😊\n\n";
            $message .= "_Si tienes alguna consulta, no dudes en contactarnos._";

            // Enviar mensaje
            return self::send($message, $numeroCliente);

        } catch (Exception $e) {
            Log::error('Error al enviar notificación de cambio de estado por WhatsApp', [
                'venta_id' => $venta->id ?? null,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

}
