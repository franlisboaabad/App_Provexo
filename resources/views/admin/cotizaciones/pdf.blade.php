<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cotización {{ $cotizacione->numero_cotizacion }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            color: #333;
            margin: 0;
            padding: 10mm;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #0066cc;
            padding-bottom: 15px;
        }
        .header h1 {
            font-size: 28px;
            color: #0066cc;
            font-weight: bold;
            margin: 0 0 5px 0;
        }
        .header .subtitle {
            font-size: 11px;
            color: #666;
            margin-bottom: 8px;
        }
        .header .contact {
            font-size: 9px;
            color: #333;
        }
        .info-section {
            margin-bottom: 15px;
        }
        .info-row {
            margin-bottom: 8px;
        }
        .section-title {
            font-size: 11px;
            font-weight: bold;
            color: #0066cc;
            margin-bottom: 8px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 3px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table thead {
            background-color: #0066cc;
            color: white;
        }
        table thead th {
            padding: 8px 5px;
            text-align: left;
            font-size: 9px;
            font-weight: bold;
        }
        table tbody td {
            padding: 8px 5px;
            border-bottom: 1px solid #ddd;
            font-size: 9px;
        }
        table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .totals-table {
            width: 280px;
            margin-left: auto;
            margin-right: 0;
            border-collapse: collapse;
        }
        .totals-table td {
            padding: 5px 8px;
            font-size: 10px;
        }
        .total-final {
            font-size: 14px;
            font-weight: bold;
            border-top: 2px solid #0066cc;
            padding-top: 8px;
        }
        .notes {
            font-size: 9px;
            line-height: 1.5;
            margin-bottom: 10px;
        }
        .notes-title {
            font-weight: bold;
            color: #0066cc;
            margin-bottom: 5px;
        }
        .terms {
            font-size: 8px;
            line-height: 1.4;
        }
        .terms-title {
            font-weight: bold;
            color: #0066cc;
            margin-bottom: 5px;
        }
        .terms ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .terms li {
            margin-bottom: 4px;
            padding-left: 15px;
        }
        .terms li:before {
            content: "(*) ";
        }
        .bank-section {
            font-size: 8px;
            margin-top: 15px;
        }
        .bank-title {
            font-weight: bold;
            color: #0066cc;
            margin-bottom: 8px;
        }
        .bank-item {
            margin-bottom: 10px;
            padding: 5px;
            background-color: #f5f5f5;
            border-left: 3px solid #0066cc;
        }
        .bank-name {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 8px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>PROVEXO+</h1>
        <div class="subtitle">Sistema de Cotizaciones</div>
        <div class="contact">
            @if($empresa->id && $empresa->email)
                Email: {{ $empresa->email }}
            @else
                Email: info@provexo.com
            @endif
            @if($empresa->id && $empresa->telefono)
                | Teléfono: {{ $empresa->telefono }}
            @else
                | Teléfono: +1 234 567 890
            @endif
        </div>
    </div>

    <!-- Información de Cotización y Cliente -->
    <table style="width: 100%; margin-bottom: 15px; border-collapse: collapse;">
        <tr>
            <td style="width: 55%; vertical-align: top;">
                <div style="font-size: 22px; font-weight: bold; color: #0066cc; margin-bottom: 5px;">
                    COTIZACIÓN #{{ $cotizacione->numero_cotizacion }}
                </div>
                <div style="font-size: 10px;">
                    <strong>Fecha:</strong> {{ $cotizacione->fecha_emision->format('d/m/Y') }}<br>
                    <strong>Estado:</strong> {{ strtoupper($cotizacione->estado) }}
                </div>
            </td>
            <td style="width: 45%; text-align: right; vertical-align: top;">
                <div style="font-size: 12px; font-weight: bold; margin-bottom: 5px;">CLIENTE</div>
                <div style="font-size: 10px;">
                    <strong>{{ $cotizacione->cliente->user->name }}</strong><br>
                    @if($cotizacione->cliente->user->email)
                        {{ $cotizacione->cliente->user->email }}
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <!-- Datos de Facturación -->
    <div class="info-section">
        <div class="section-title">DATOS DE FACTURACIÓN</div>
        <div class="info-row">
            <strong>Razón Social:</strong> {{ strtoupper($cotizacione->cliente->empresa ?? $cotizacione->cliente->user->name) }}
        </div>
        @if($cotizacione->cliente->ruc)
        <div class="info-row">
            <strong>RUC:</strong> {{ $cotizacione->cliente->ruc }}
        </div>
        @endif
        @if($cotizacione->cliente->user->email)
        <div class="info-row">
            <strong>Email:</strong> {{ $cotizacione->cliente->user->email }}
        </div>
        @endif
        @if($cotizacione->cliente->celular)
        <div class="info-row">
            <strong>Teléfono:</strong> {{ $cotizacione->cliente->celular }}
        </div>
        @endif
        <div class="info-row" style="margin-top: 8px;">
            <strong>Dirección de Envío:</strong> Misma dirección de facturación
        </div>
    </div>

    <!-- Notas del Pedido -->
    @if($cotizacione->observaciones)
    <div class="info-section">
        <div class="notes-title">Notas del Pedido:</div>
        <div class="notes">{{ $cotizacione->observaciones }}</div>
    </div>
    @endif

    <!-- Tabla de Productos -->
    <div class="info-section">
        <table>
            <thead>
                <tr>
                    <th style="width: 35%;">Producto</th>
                    <th style="width: 15%;">SKU</th>
                    <th class="text-center" style="width: 15%;">Cantidad</th>
                    <th class="text-right" style="width: 17.5%;">Precio Unitario</th>
                    <th class="text-right" style="width: 17.5%;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cotizacione->productos as $item)
                <tr>
                    <td>{{ $item->producto->descripcion }}</td>
                    <td>{{ $item->producto->codigo_producto }}</td>
                    <td class="text-center">{{ $item->cantidad }} {{ $item->producto->unidad_medida ?? 'unidad' }}</td>
                    <td class="text-right">S/ {{ number_format($item->precio_unitario, 2) }}</td>
                    <td class="text-right"><strong>S/ {{ number_format($item->subtotal, 2) }}</strong></td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totales -->
        <table class="totals-table">
            <tr>
                <td style="text-align: left;">Subtotal:</td>
                <td class="text-right">S/ {{ number_format($cotizacione->subtotal, 2) }}</td>
            </tr>
            @if($cotizacione->impuesto_total > 0)
            <tr>
                <td style="text-align: left;">Impuesto:</td>
                <td class="text-right">S/ {{ number_format($cotizacione->impuesto_total, 2) }}</td>
            </tr>
            @endif
            @if($cotizacione->descuento > 0)
            <tr>
                <td style="text-align: left;">Descuento:</td>
                <td class="text-right">-S/ {{ number_format($cotizacione->descuento, 2) }}</td>
            </tr>
            @endif
            <tr>
                <td colspan="2" style="padding: 0; height: 1px;">
                    <div style="border-top: 2px solid #0066cc; margin: 8px 0;"></div>
                </td>
            </tr>
            <tr class="total-final">
                <td style="text-align: left; padding-top: 8px;">TOTAL:</td>
                <td class="text-right" style="padding-top: 8px;">S/ {{ number_format($cotizacione->total, 2) }}</td>
            </tr>
        </table>
    </div>

    <!-- Notas del Cliente -->
    @if($cotizacione->observaciones)
    <div class="info-section">
        <div class="notes-title">Notas del Cliente:</div>
        <div class="notes">{{ $cotizacione->observaciones }}</div>
    </div>
    @endif

    <!-- Observaciones -->
    <div class="info-section">
        <div class="terms-title">OBSERVACIONES:</div>
        <div class="terms">
            <ul>
                <li>Esta cotización tiene una validez de 30 días calendario desde la fecha de emisión.</li>
                <li>Productos sujetos a stock, este documento solo fija los precios por treinta días, pero no garantiza la disponibilidad de lo cotizado.</li>
                <li>En caso de que el cliente tenga facturas vencidas, no se procederá con el despacho de la orden de compra originada por esta cotización.</li>
                <li>En caso de que el cliente tenga facturas vencidas, no aplican descuentos, promociones y precios especiales de buen pagador.</li>
                <li>El tiempo de entrega que prevalece es el de este documento y no el de la OC del cliente, las entregas se realizarán como mínimo a partir de 1 día hábil después de confirmada la recepción de la OC del cliente por correo electrónico.</li>
            </ul>
        </div>
    </div>

    <!-- Condiciones de Pago -->
    <div class="info-section">
        <div class="terms-title">CONDICIONES DE PAGO:</div>
        <div class="terms">
            <ul>
                <li>50% de adelanto por transferencia bancaria para confirmar el pedido.</li>
                <li>50% restante al momento de confirmar el despacho.</li>
            </ul>
        </div>
    </div>

    <!-- Cuentas Bancarias -->
    @if($empresa && $cuentasBancarias->count() > 0)
    <div class="info-section">
        <div class="bank-section">
            @if($empresa->nombre_comercial || $empresa->razon_social)
            <div class="bank-title">CUENTA CORRIENTE: {{ strtoupper($empresa->nombre_comercial ?? $empresa->razon_social) }}</div>
            @endif

            @php
                $bancos = $cuentasBancarias->groupBy('banco');
            @endphp

            @foreach($bancos as $banco => $cuentas)
            <div class="bank-item">
                <div class="bank-name">{{ strtoupper($banco) }}</div>

                @php
                    $porMoneda = $cuentas->groupBy('moneda_cuenta');
                @endphp

                @foreach($porMoneda as $moneda => $cuentasMoneda)
                <div style="margin-bottom: 5px;">
                    <div style="font-weight: bold; color: #0066cc; margin-bottom: 3px;">
                        {{ $moneda == 'PEN' ? 'SOLES (Peruvian Soles)' : ($moneda == 'USD' ? 'DÓLARES (US Dollars)' : $moneda) }}
                    </div>
                    @foreach($cuentasMoneda as $cuenta)
                    <div style="margin-bottom: 3px;">
                        <strong>CTA:</strong> {{ $cuenta->numero_cuenta }}
                        @if($cuenta->tipo_cuenta)
                            ({{ $cuenta->tipo_cuenta }})
                        @endif
                    </div>
                    @if($cuenta->numero_cuenta_interbancario)
                    <div style="margin-bottom: 3px;">
                        <strong>CCI:</strong> {{ $cuenta->cci_formateado ?? $cuenta->numero_cuenta_interbancario }}
                    </div>
                    @endif
                    @endforeach
                </div>
                @endforeach
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <div style="font-weight: bold; margin-bottom: 5px;">PROVEXO+</div>
        <div>Esta cotización es válida por 30 días desde la fecha de emisión.</div>
        <div style="margin-top: 5px;">Para consultas, contacte a nuestro equipo de ventas.</div>
    </div>
</body>
</html>

