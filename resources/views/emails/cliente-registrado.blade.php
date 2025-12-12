<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido a Provexo+</title>
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
        .password-box {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .password-box strong {
            color: #856404;
        }
        .password-value {
            font-family: 'Courier New', monospace;
            font-size: 16px;
            font-weight: bold;
            color: #856404;
            background-color: #fff;
            padding: 8px 12px;
            border-radius: 4px;
            display: inline-block;
            margin-top: 5px;
        }
        .warning-box {
            background-color: #f8d7da;
            border-left: 4px solid #dc3545;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .warning-box strong {
            color: #721c24;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background-color: #28a745;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            text-align: center;
        }
        .footer {
            text-align: center;
            padding: 20px;
            background-color: #f8f9fa;
            color: #666;
            font-size: 12px;
        }
        .instruction {
            margin-top: 20px;
            padding: 15px;
            background-color: #d1ecf1;
            border-left: 4px solid #0c5460;
            border-radius: 5px;
        }
        .instruction strong {
            color: #0c5460;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>¡Bienvenido a Provexo+!</h1>
        </div>

        <div class="content">
            <p>Hola <strong>{{ $user->name }}</strong>,</p>

            <p>Nos complace darte la bienvenida a <strong>Provexo+</strong>. Tu cuenta ha sido creada exitosamente y ya puedes acceder a todos nuestros servicios.</p>

            <div class="details-box">
                <p><strong>Información de tu cuenta:</strong></p>
                <p>Email: <strong>{{ $user->email }}</strong></p>
                <p>Rol: <strong>Cliente</strong></p>
                <p>Fecha de registro: <strong>{{ $user->created_at->format('d/m/Y H:i') }}</strong></p>
            </div>

            <div class="password-box">
                <p><strong>🔑 Credenciales de acceso:</strong></p>
                <p>Contraseña temporal: <span class="password-value">{{ $password }}</span></p>
            </div>

            <div class="warning-box">
                <p><strong>⚠️ IMPORTANTE:</strong></p>
                <p>Por seguridad, te recomendamos <strong>cambiar tu contraseña</strong> después de iniciar sesión por primera vez. Puedes hacerlo desde tu perfil en la plataforma.</p>
            </div>

            <div class="instruction">
                <p><strong>📝 Instrucciones:</strong></p>
                <p>1. Inicia sesión con tu email y la contraseña proporcionada arriba.</p>
                <p>2. Ve a tu perfil y cambia tu contraseña por una más segura.</p>
                <p>3. Explora todas las funcionalidades disponibles en tu panel de cliente.</p>
            </div>

            <p style="text-align: center;">
                <a href="{{ route('login') }}" class="button">Iniciar Sesión</a>
            </p>
        </div>

        <div class="footer">
            <p>Este es un email automático del sistema Provexo+</p>
            <p>&copy; {{ date('Y') }} Provexo+. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>

