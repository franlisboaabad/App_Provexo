<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Proveedor;
use App\Models\Cliente;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
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
            'rol' => ['required', 'string', 'in:Proveedor,Cliente'],
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
            $user = DB::transaction(function () use ($validated, $request) {
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

                // Asignar rol
                $user->assignRole($validated['rol']);

                Log::info('Rol asignado al usuario', [
                    'user_id' => $user->id,
                    'rol' => $validated['rol'],
                ]);

                // Crear perfil según el rol
                if ($validated['rol'] === 'Proveedor') {
                    Proveedor::create([
                        'user_id' => $user->id,
                        'celular' => $validated['celular'] ?? null,
                        'empresa' => $validated['empresa'] ?? null,
                        'ruc' => $validated['ruc'] ?? null,
                    ]);

                    Log::info('Perfil de proveedor creado', [
                        'user_id' => $user->id,
                    ]);
                } elseif ($validated['rol'] === 'Cliente') {
                    Cliente::create([
                        'user_id' => $user->id,
                        'celular' => $validated['celular'] ?? null,
                        'empresa' => $validated['empresa'] ?? null,
                        'ruc' => $validated['ruc'] ?? null,
                    ]);

                    Log::info('Perfil de cliente creado', [
                        'user_id' => $user->id,
                    ]);
                }

                return $user;
            });

            // Disparar evento de registro
            event(new Registered($user));

            Log::info('Usuario registrado y evento disparado', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);

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
