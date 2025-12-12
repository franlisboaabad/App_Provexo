<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Cliente;
use App\Providers\RouteServiceProvider;
use App\Mail\ClienteRegistradoMail;
use App\Mail\NuevoClienteAdminMail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Exception;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Validar datos
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'celular' => ['nullable', 'string', 'max:20'],
            'empresa' => ['nullable', 'string', 'max:255'],
            'ruc' => ['nullable', 'string', 'max:100'],
        ]);

        Log::info('Iniciando registro de usuario', [
            'email' => $request->email,
            'rol' => $request->rol,
            'ip' => $request->ip(),
        ]);

        try {
            // Usar transacción para asegurar consistencia
            $result = DB::transaction(function () use ($validated, $request) {
                // Crear usuario
                $user = User::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                    'activo' => true,
                ]);

                Log::info('Usuario creado exitosamente', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                ]);

                // Asignar rol Cliente automáticamente
                $user->assignRole('Cliente');

                Log::info('Rol asignado al usuario', [
                    'user_id' => $user->id,
                    'rol' => 'Cliente',
                ]);

                // Crear perfil de Cliente
                $cliente = Cliente::create([
                    'user_id' => $user->id,
                    'celular' => $validated['celular'] ?? null,
                    'empresa' => $validated['empresa'] ?? null,
                    'ruc' => $validated['ruc'] ?? null,
                ]);

                Log::info('Perfil de cliente creado', [
                    'user_id' => $user->id,
                    'cliente_id' => $cliente->id,
                ]);

                return ['user' => $user, 'cliente' => $cliente];
            });

            $user = $result['user'];
            $cliente = $result['cliente'];

            // Disparar evento de registro
            event(new Registered($user));

            Log::info('Usuario registrado y evento disparado', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);

            // Enviar email de bienvenida al cliente
            try {
                Mail::to($user->email)->send(new ClienteRegistradoMail($user, $validated['password']));
                Log::info('Email de bienvenida enviado al cliente', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                ]);
            } catch (\Exception $e) {
                Log::error('Error al enviar email de bienvenida al cliente', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
            }

            // Enviar notificación al administrador
            try {
                $adminEmail = env('MAIL_FROM_ADDRESS', 'info@ideassoftperu.com');
                Mail::to($adminEmail)->send(new NuevoClienteAdminMail($user, $cliente));
                Log::info('Email de notificación enviado al administrador', [
                    'user_id' => $user->id,
                    'admin_email' => $adminEmail,
                ]);
            } catch (\Exception $e) {
                Log::error('Error al enviar email de notificación al administrador', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
            }

            // Iniciar sesión automáticamente
            Auth::login($user);

            Log::info('Sesión iniciada para usuario registrado', [
                'user_id' => $user->id,
            ]);

            return redirect(RouteServiceProvider::HOME)
                ->with('success', '¡Registro exitoso! Bienvenido a Provexo+');

        } catch (ValidationException $e) {
            // Re-lanzar excepciones de validación (ya se manejan automáticamente)
            throw $e;
        } catch (Exception $e) {
            // Log del error
            Log::error('Error al registrar usuario', [
                'email' => $request->email,
                'rol' => $request->rol,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'ip' => $request->ip(),
            ]);

            // Retornar con error
            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withErrors([
                    'email' => 'Hubo un error al registrar tu cuenta. Por favor, intenta nuevamente.',
                ]);
        }
    }
}
