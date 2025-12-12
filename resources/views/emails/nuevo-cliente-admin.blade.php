<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Cliente Registrado</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 40px 20px;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .header {
            background-color: #007bff;
            color: #ffffff;
            padding: 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }
        .content {
            padding: 20px;
        }
        .content p {
            margin-bottom: 15px;
        }
        .details-box {
            background-color: #e7f3ff;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .details-box p {
            margin: 8px 0;
            font-size: 14px;
        }
        .details-box strong {
            color: #000;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background-color: #007bff;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            padding: 20px;
            background-color: #f8f9fa;
            color: #666;
            font-size: 12px;
        }
        .alert {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔔 Nuevo Cliente Registrado</h1>
        </div>

        <div class="content">
            <p>Se ha registrado un nuevo cliente en el sistema <strong>Provexo+</strong>.</p>

            <div class="alert">
                <strong>⚠️ Acción requerida:</strong> Revisa la información del nuevo cliente y verifica su cuenta si es necesario.
            </div>

            <div class="details-box">
                <p><strong>Cliente:</strong> {{ $user->name }}</p>
                @if($cliente->empresa)
                <p><strong>Empresa:</strong> {{ $cliente->empresa }}</p>
                @endif
                <p><strong>Email:</strong> <a href="mailto:{{ $user->email }}" style="color: #007bff; text-decoration: underline;">{{ $user->email }}</a></p>
                @if($cliente->celular)
                <p><strong>Teléfono:</strong> {{ $cliente->celular }}</p>
                @endif
                @if($cliente->ruc)
                <p><strong>RUC:</strong> {{ $cliente->ruc }}</p>
                @endif
                <p><strong>Fecha:</strong> {{ $user->created_at->format('d/m/Y H:i') }}</p>
            </div>

            <p>Por favor revisa la información del cliente en el panel de administración.</p>

            <p style="text-align: center;">
                <a href="{{ route('admin.clientes.show', $cliente->id) }}" class="button">Ver Detalles del Cliente</a>
            </p>
        </div>

        <div class="footer">
            <p>Este es un email automático del sistema Provexo+.</p>
            <p>&copy; {{ date('Y') }} Provexo+. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>

