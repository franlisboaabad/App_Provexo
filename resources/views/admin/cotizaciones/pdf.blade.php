<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cotización {{ $cotizacione->numero_cotizacion }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.4;
        }
        .container {
            width: 100%;
            max-width: 210mm;
            margin: 0 auto;
            padding: 15mm;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #0066cc;
            padding-bottom: 15px;
        }
        .header h1 {
            font-size: 32px;
            color: #0066cc;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .header .subtitle {
            font-size: 12px;
            color: #666;
            margin-bottom: 10px;
        }
        .header .contact {
            font-size: 10px;
            color: #333;
        }
        .cotizacion-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .cotizacion-left {
            flex: 1;
        }
        .cotizacion-number {
            font-size: 24px;
            font-weight: bold;
            color: #0066cc;
            margin-bottom: 5px;
        }
        .cotizacion-right {
            text-align: right;
            flex: 1;
        }
        .cliente-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .cliente-info {
            font-size: 11px;
            line-height: 1.6;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #0066cc;
            margin-bottom: 8px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 3px;
        }
        .billing-data {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            font-size: 10px;
        }
        .billing-item {
            margin-bottom: 5px;
        }
        .billing-label {
            font-weight: bold;
            display: inline-block;
            min-width: 80px;
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
            padding: 8px;
            text-align: left;
            font-size: 10px;
            font-weight: bold;
        }
        table tbody td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
            font-size: 10px;
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
        .totals {
            margin-top: 15px;
            margin-left: auto;
            width: 300px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            font-size: 11px;
        }
        .total-row.grand-total {
            font-size: 16px;
            font-weight: bold;
            border-top: 2px solid #0066cc;
            padding-top: 8px;
            margin-top: 8px;
        }
        .notes {
            margin-top: 20px;
            font-size: 10px;
            line-height: 1.6;
        }
        .notes-title {
            font-weight: bold;
            margin-bottom: 5px;
            color: #0066cc;
        }
        .terms {
            margin-top: 20px;
            font-size: 9px;
            line-height: 1.5;
        }
        .terms-title {
            font-weight: bold;
            margin-bottom: 8px;
            color: #0066cc;
        }
        .terms ul {
            list-style: none;
            padding-left: 0;
        }
        .terms li {
            margin-bottom: 5px;
            padding-left: 15px;
            position: relative;
        }
        .terms li:before {
            content: "(*)";
            position: absolute;
            left: 0;
        }
        .bank-accounts {
            margin-top: 20px;
            font-size: 9px;
        }
        .bank-title {
            font-weight: bold;
            margin-bottom: 8px;
            color: #0066cc;
        }
        .bank-item {
            margin-bottom: 10px;
            padding: 8px;
            background-color: #f5f5f5;
            border-left: 3px solid #0066cc;
        }
        .bank-name {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .bank-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-top: 5px;
        }
        .currency-section {
            margin-bottom: 8px;
        }
        .currency-label {
            font-weight: bold;
            color: #0066cc;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 9px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="container">
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
                @if(($empresa->id && $empresa->email) && ($empresa->id && $empresa->telefono)) | @endif
                @if($empresa->id && $empresa->telefono)
                    Teléfono: {{ $empresa->telefono }}
                @else
                    Teléfono: +1 234 567 890
                @endif
            </div>
        </div>

        <!-- Información de Cotización y Cliente -->
        <div class="cotizacion-info">
            <div class="cotizacion-left">
                <div class="cotizacion-number">COTIZACIÓN #{{ $cotizacione->numero_cotizacion }}</div>
                <div style="font-size: 11px;">
                    <strong>Fecha:</strong> {{ $cotizacione->fecha_emision->format('d/m/Y') }}<br>
                    <strong>Estado:</strong> {{ strtoupper($cotizacione->estado) }}
                </div>
            </div>
            <div class="cotizacion-right">
                <div class="cliente-title">CLIENTE</div>
                <div class="cliente-info">
                    <strong>{{ $cotizacione->cliente->user->name }}</strong><br>
                    @if($cotizacione->cliente->user->email)
                        {{ $cotizacione->cliente->user->email }}
                    @endif
                </div>
            </div>
        </div>

        <!-- Datos de Facturación -->
        <div class="section">
            <div class="section-title">DATOS DE FACTURACIÓN</div>
            <div class="billing-data">
                <div>
                    <div class="billing-item">
                        <span class="billing-label">Razón Social:</span>
                        <span>{{ $cotizacione->cliente->empresa ?? $cotizacione->cliente->user->name }}</span>
                    </div>
                    @if($cotizacione->cliente->ruc)
                    <div class="billing-item">
                        <span class="billing-label">RUC:</span>
                        <span>{{ $cotizacione->cliente->ruc }}</span>
                    </div>
                    @endif
                    @if($cotizacione->cliente->user->email)
                    <div class="billing-item">
                        <span class="billing-label">Email:</span>
                        <span>{{ $cotizacione->cliente->user->email }}</span>
                    </div>
                    @endif
                    @if($cotizacione->cliente->celular)
                    <div class="billing-item">
                        <span class="billing-label">Teléfono:</span>
                        <span>{{ $cotizacione->cliente->celular }}</span>
                    </div>
                    @endif
                </div>
                <div>
                    @if($cotizacione->cliente->empresa)
                    <div class="billing-item">
                        <span class="billing-label">Empresa:</span>
                        <span>{{ strtoupper($cotizacione->cliente->empresa) }}</span>
                    </div>
                    @endif
                    @if($cotizacione->cliente->ruc)
                    <div class="billing-item">
                        <span class="billing-label">RUC:</span>
                        <span>{{ $cotizacione->cliente->ruc }}</span>
                    </div>
                    @endif
                </div>
            </div>
            <div style="margin-top: 10px; font-size: 10px;">
                <strong>Dirección de Envío:</strong> Misma dirección de facturación
            </div>
        </div>

        <!-- Notas del Pedido -->
        @if($cotizacione->observaciones)
        <div class="section">
            <div class="notes">
                <div class="notes-title">Notas del Pedido:</div>
                <div>{{ $cotizacione->observaciones }}</div>
            </div>
        </div>
        @endif

        <!-- Tabla de Productos -->
        <div class="section">
            <table>
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th style="width: 80px;">SKU</th>
                        <th class="text-center" style="width: 60px;">Cantidad</th>
                        <th class="text-right" style="width: 90px;">Precio Unitario</th>
                        <th class="text-right" style="width: 90px;">Total</th>
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
            <div class="totals">
                <div class="total-row">
                    <span>Subtotal:</span>
                    <span>S/ {{ number_format($cotizacione->subtotal, 2) }}</span>
                </div>
                @if($cotizacione->impuesto_total > 0)
                <div class="total-row">
                    <span>Impuesto:</span>
                    <span>S/ {{ number_format($cotizacione->impuesto_total, 2) }}</span>
                </div>
                @endif
                @if($cotizacione->descuento > 0)
                <div class="total-row">
                    <span>Descuento:</span>
                    <span>-S/ {{ number_format($cotizacione->descuento, 2) }}</span>
                </div>
                @endif
                <div class="total-row grand-total">
                    <span>TOTAL:</span>
                    <span>S/ {{ number_format($cotizacione->total, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Notas del Cliente -->
        @if($cotizacione->observaciones)
        <div class="section">
            <div class="notes">
                <div class="notes-title">Notas del Cliente:</div>
                <div>{{ $cotizacione->observaciones }}</div>
            </div>
        </div>
        @endif

        <!-- Observaciones -->
        <div class="section">
            <div class="terms">
                <div class="terms-title">OBSERVACIONES:</div>
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
        <div class="section">
            <div class="terms">
                <div class="terms-title">CONDICIONES DE PAGO:</div>
                <ul>
                    <li>50% de adelanto por transferencia bancaria para confirmar el pedido.</li>
                    <li>50% restante al momento de confirmar el despacho.</li>
                </ul>
            </div>
        </div>

        <!-- Cuentas Bancarias -->
        @if($empresa && $cuentasBancarias->count() > 0)
        <div class="section">
            <div class="bank-accounts">
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
                    <div class="currency-section">
                        <div class="currency-label">
                            {{ $moneda == 'PEN' ? 'SOLES (Peruvian Soles)' : ($moneda == 'USD' ? 'DÓLARES (US Dollars)' : $moneda) }}
                        </div>
                        @foreach($cuentasMoneda as $cuenta)
                        <div class="bank-details">
                            <div>
                                <strong>CTA:</strong> {{ $cuenta->numero_cuenta }}
                                @if($cuenta->tipo_cuenta)
                                    ({{ $cuenta->tipo_cuenta }})
                                @endif
                            </div>
                            @if($cuenta->numero_cuenta_interbancario)
                            <div>
                                <strong>CCI:</strong> {{ $cuenta->cci_formateado ?? $cuenta->numero_cuenta_interbancario }}
                            </div>
                            @endif
                        </div>
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
    </div>
</body>
</html>

