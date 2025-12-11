@extends('adminlte::auth.auth-page', ['auth_type' => 'register'])

@php( $login_url = View::getSection('login_url') ?? config('adminlte.login_url', 'login') )
@php( $register_url = View::getSection('register_url') ?? config('adminlte.register_url', 'register') )

@if (config('adminlte.use_route_url', false))
    @php( $login_url = $login_url ? route($login_url) : '' )
    @php( $register_url = $register_url ? route($register_url) : '' )
@else
    @php( $login_url = $login_url ? url($login_url) : '' )
    @php( $register_url = $register_url ? url($register_url) : '' )
@endif

@section('auth_header', __('Registrarse'))

@section('auth_body')
    <form action="{{ route('register') }}" method="post">
        {{ csrf_field() }}

        {{-- Rol selection --}}
        <div class="input-group mb-3">
            <select name="rol" class="form-control {{ $errors->has('rol') ? 'is-invalid' : '' }}" required>
                <option value="">Seleccione un rol</option>
                <option value="Proveedor" {{ old('rol') == 'Proveedor' ? 'selected' : '' }}>Proveedor</option>
                <option value="Cliente" {{ old('rol') == 'Cliente' ? 'selected' : '' }}>Cliente</option>
            </select>
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-user-tag {{ config('adminlte.classes_auth_icon', '') }}"></span>
                </div>
            </div>
            @if($errors->has('rol'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('rol') }}</strong>
                </span>
            @endif
        </div>

        {{-- Name field --}}
        <div class="input-group mb-3">
            <input type="text" name="name" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                   value="{{ old('name') }}" placeholder="{{ __('Name') }}" autofocus>
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-user {{ config('adminlte.classes_auth_icon', '') }}"></span>
                </div>
            </div>
            @if($errors->has('name'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('name') }}</strong>
                </span>
            @endif
        </div>

        {{-- Email field --}}
        <div class="input-group mb-3">
            <input type="email" name="email" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                   value="{{ old('email') }}" placeholder="{{ __('Email') }}">
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-envelope {{ config('adminlte.classes_auth_icon', '') }}"></span>
                </div>
            </div>
            @if($errors->has('email'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('email') }}</strong>
                </span>
            @endif
        </div>

        {{-- Password field --}}
        <div class="input-group mb-3">
            <input type="password" name="password" class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
                   placeholder="{{ __('Password') }}">
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock {{ config('adminlte.classes_auth_icon', '') }}"></span>
                </div>
            </div>
            @if($errors->has('password'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('password') }}</strong>
                </span>
            @endif
        </div>

        {{-- Password confirmation field --}}
        <div class="input-group mb-3">
            <input type="password" name="password_confirmation"
                   class="form-control {{ $errors->has('password_confirmation') ? 'is-invalid' : '' }}"
                   placeholder="{{ __('Confirm Password') }}">
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock {{ config('adminlte.classes_auth_icon', '') }}"></span>
                </div>
            </div>
        </div>

        {{-- Celular field --}}
        <div class="input-group mb-3">
            <input type="text" name="celular" class="form-control {{ $errors->has('celular') ? 'is-invalid' : '' }}"
                   value="{{ old('celular') }}" placeholder="Celular" maxlength="20">
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-phone {{ config('adminlte.classes_auth_icon', '') }}"></span>
                </div>
            </div>
            @if($errors->has('celular'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('celular') }}</strong>
                </span>
            @endif
        </div>

        {{-- Empresa field --}}
        <div class="input-group mb-3">
            <input type="text" name="empresa" class="form-control {{ $errors->has('empresa') ? 'is-invalid' : '' }}"
                   value="{{ old('empresa') }}" placeholder="Empresa" maxlength="255">
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-building {{ config('adminlte.classes_auth_icon', '') }}"></span>
                </div>
            </div>
            @if($errors->has('empresa'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('empresa') }}</strong>
                </span>
            @endif
        </div>

        {{-- RUC field --}}
        <div class="input-group mb-3">
            <input type="text" name="ruc" class="form-control {{ $errors->has('ruc') ? 'is-invalid' : '' }}"
                   value="{{ old('ruc') }}" placeholder="RUC" maxlength="100">
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-id-card {{ config('adminlte.classes_auth_icon', '') }}"></span>
                </div>
            </div>
            @if($errors->has('ruc'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('ruc') }}</strong>
                </span>
            @endif
        </div>

        {{-- Register button --}}
        <button type="submit" class="btn btn-block {{ config('adminlte.classes_auth_btn', 'btn-flat btn-primary') }}">
            <span class="fas fa-user-plus"></span>
            {{ __('Register') }}
        </button>

    </form>
@stop

@section('auth_footer')
    <p class="my-0">
        <a href="{{ $login_url }}">
            {{ __('I already have a membership') }}
        </a>
    </p>
@stop
