<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Cliente;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user()->load('cliente');
        return view('profile.edit', [
            'user' => $user,
            'cliente' => $user->cliente,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'celular' => ['nullable', 'string', 'max:20'],
            'empresa' => ['nullable', 'string', 'max:255'],
            'ruc' => ['nullable', 'string', 'max:100'],
        ]);

        try {
            DB::transaction(function () use ($user, $validated) {
                // Actualizar información del usuario
                $user->name = $validated['name'];
                if ($user->email !== $validated['email']) {
                    $user->email = $validated['email'];
                    $user->email_verified_at = null;
                }
                $user->save();

                // Actualizar o crear información del cliente
                if ($user->cliente) {
                    $user->cliente->update([
                        'celular' => $validated['celular'] ?? null,
                        'empresa' => $validated['empresa'] ?? null,
                        'ruc' => $validated['ruc'] ?? null,
                    ]);
                } else {
                    Cliente::create([
                        'user_id' => $user->id,
                        'celular' => $validated['celular'] ?? null,
                        'empresa' => $validated['empresa'] ?? null,
                        'ruc' => $validated['ruc'] ?? null,
                    ]);
                }
            });

            return Redirect::route('profile.edit')->with('success', 'Perfil actualizado exitosamente.');
        } catch (\Exception $e) {
            return Redirect::route('profile.edit')
                ->withInput()
                ->withErrors(['error' => 'Error al actualizar el perfil. Intente nuevamente.']);
        }
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current-password'],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return Redirect::route('profile.edit')->with('success', 'Contraseña actualizada exitosamente.');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current-password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
