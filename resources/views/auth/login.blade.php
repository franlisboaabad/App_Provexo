<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Iniciar Sesión - {{ config('app.name', 'Provexo') }}</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #ffffff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .auth-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            padding: 40px;
            max-width: 500px;
            width: 100%;
        }

        .auth-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #1e3a8a;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }

        .auth-icon i {
            font-size: 36px;
            color: #ffffff;
        }

        .auth-title {
            font-size: 28px;
            font-weight: 700;
            color: #1e293b;
            text-align: center;
            margin-bottom: 8px;
        }

        .auth-subtitle {
            font-size: 14px;
            color: #64748b;
            text-align: center;
            margin-bottom: 30px;
        }

        .form-label {
            font-weight: 600;
            color: #1e3a8a;
            font-size: 14px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
        }

        .form-label i {
            margin-right: 8px;
            font-size: 16px;
        }

        .form-control {
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #1e3a8a;
            box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
        }

        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 16px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper .form-control {
            padding-left: 45px;
        }

        .btn-login {
            background: #1e3a8a;
            border: none;
            border-radius: 8px;
            padding: 12px 24px;
            font-weight: 600;
            font-size: 16px;
            color: #ffffff;
            width: 100%;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            background: #1e40af;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(30, 58, 138, 0.3);
        }

        .btn-register {
            background: #10b981;
            border: none;
            border-radius: 8px;
            padding: 12px 24px;
            font-weight: 600;
            font-size: 16px;
            color: #ffffff;
            width: 100%;
            transition: all 0.3s ease;
        }

        .btn-register:hover {
            background: #059669;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .forgot-password {
            color: #1e3a8a;
            text-decoration: none;
            font-size: 14px;
            display: flex;
            align-items: center;
            margin-top: 12px;
        }

        .forgot-password:hover {
            text-decoration: underline;
            color: #1e40af;
        }

        .forgot-password i {
            margin-right: 6px;
        }

        .invalid-feedback {
            display: block;
            font-size: 13px;
            margin-top: 5px;
        }

        @media (max-width: 768px) {
            .auth-card {
                padding: 30px 20px;
            }

            .auth-title {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="container">
            <div class="row justify-content-center">
                <!-- Formulario de Login -->
                <div class="col-lg-5 col-md-6 mb-4 mb-md-0">
                    <div class="auth-card">
                        <div class="auth-icon">
                            <i class="fas fa-lock"></i>
                        </div>
                        <h1 class="auth-title">Iniciar Sesión</h1>
                        <p class="auth-subtitle">Ingresa tus credenciales para acceder al sistema</p>

                        <form action="{{ route('login') }}" method="POST">
                            @csrf

                            <!-- Email -->
                            <div class="mb-3">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope"></i> Correo electrónico
                                </label>
                                <div class="input-wrapper">
                                    <i class="fas fa-at input-icon"></i>
                                    <input type="email"
                                           class="form-control @error('email') is-invalid @enderror"
                                           id="email"
                                           name="email"
                                           value="{{ old('email') }}"
                                           placeholder="tu@email.com"
                                           required
                                           autofocus>
                                </div>
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    <i class="fas fa-key"></i> Contraseña
                                </label>
                                <div class="input-wrapper">
                                    <i class="fas fa-lock input-icon"></i>
                                    <input type="password"
                                           class="form-control @error('password') is-invalid @enderror"
                                           id="password"
                                           name="password"
                                           placeholder="********"
                                           required>
                                </div>
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <!-- Forgot Password -->
                            <a href="{{ route('password.request') }}" class="forgot-password">
                                <i class="fas fa-question-circle"></i> ¿Olvidaste tu contraseña?
                            </a>

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-login mt-4">
                                <i class="fas fa-arrow-right"></i> Iniciar Sesión
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Formulario de Registro -->
                <div class="col-lg-5 col-md-6">
                    <div class="auth-card">
                        <div class="auth-icon">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <h1 class="auth-title">Crear Cuenta</h1>
                        <p class="auth-subtitle">Únete a la comunidad Provexo + y accede a todos nuestros servicios</p>

                        <form action="{{ route('register') }}" method="POST">
                            @csrf

                            <!-- Nombre -->
                            <div class="mb-3">
                                <label for="name" class="form-label">
                                    <i class="fas fa-user"></i> Nombres completos
                                </label>
                                <div class="input-wrapper">
                                    <i class="fas fa-id-card input-icon"></i>
                                    <input type="text"
                                           class="form-control @error('name') is-invalid @enderror"
                                           id="name"
                                           name="name"
                                           value="{{ old('name') }}"
                                           placeholder="Ej: Juan Pérez García"
                                           required
                                           autofocus>
                                </div>
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="mb-3">
                                <label for="register_email" class="form-label">
                                    <i class="fas fa-envelope"></i> Correo electrónico
                                </label>
                                <div class="input-wrapper">
                                    <i class="fas fa-at input-icon"></i>
                                    <input type="email"
                                           class="form-control @error('email') is-invalid @enderror"
                                           id="register_email"
                                           name="email"
                                           value="{{ old('email') }}"
                                           placeholder="tu@email.com"
                                           required>
                                </div>
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div class="mb-3">
                                <label for="register_password" class="form-label">
                                    <i class="fas fa-key"></i> Contraseña
                                </label>
                                <div class="input-wrapper">
                                    <i class="fas fa-lock input-icon"></i>
                                    <input type="password"
                                           class="form-control @error('password') is-invalid @enderror"
                                           id="register_password"
                                           name="password"
                                           placeholder="********"
                                           required>
                                </div>
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <!-- Confirm Password -->
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">
                                    <i class="fas fa-check-circle"></i> Confirmar contraseña
                                </label>
                                <div class="input-wrapper">
                                    <i class="fas fa-shield-alt input-icon"></i>
                                    <input type="password"
                                           class="form-control"
                                           id="password_confirmation"
                                           name="password_confirmation"
                                           placeholder="********"
                                           required>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-register mt-4">
                                <i class="fas fa-user-plus"></i> Crear mi cuenta
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
