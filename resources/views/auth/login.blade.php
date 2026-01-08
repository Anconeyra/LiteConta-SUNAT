<x-guest-layout>
    <div class="mb-8 text-center">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-slate-900 rounded-2xl mb-4 shadow-lg border-b-4 border-green-500">
            <i class="fas fa-shield-alt text-white text-2xl"></i>
        </div>
        <h2 class="text-2xl font-black text-slate-800 tracking-tight">¡ Bienvenido a <span class="italic text-slate-900">Lite<span class="text-green-500">Conta</span></span> !</h2>
        <p class="text-slate-500 text-sm mt-1 font-medium">Ingresa tus credenciales para gestionar tu MYPE</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Correo Electrónico')" class="text-xs font-bold uppercase text-slate-500 ml-1" />
            <div class="relative mt-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <i class="fas fa-envelope text-sm"></i>
                </div>
                <x-text-input id="email" 
                    class="block w-full pl-10 border-slate-200 rounded-xl focus:ring-green-500 focus:border-green-500 transition-all duration-200" 
                    type="email" name="email" :value="old('email')" 
                    required autofocus autocomplete="username" 
                    placeholder="correo@ejemplo.com" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <div class="flex justify-between items-center ml-1">
                <x-input-label for="password" :value="__('Contraseña')" class="text-xs font-bold uppercase text-slate-500" />
                @if (Route::has('password.request'))
                    <a class="text-xs font-bold text-blue-600 hover:text-blue-800 transition" href="{{ route('password.request') }}">
                        {{ __('¿Olvidaste tu clave?') }}
                    </a>
                @endif
            </div>
            <div class="relative mt-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <i class="fas fa-lock text-sm"></i>
                </div>
                <x-text-input id="password" 
                    class="block w-full pl-10 border-slate-200 rounded-xl focus:ring-green-500 focus:border-green-500 transition-all duration-200"
                    type="password"
                    name="password"
                    required autocomplete="current-password"
                    placeholder="••••••••" />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center cursor-pointer group">
                <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-green-500 shadow-sm focus:ring-green-500 transition" name="remember">
                <span class="ms-2 text-sm text-slate-600 group-hover:text-slate-900 transition">{{ __('Recordarme en este equipo') }}</span>
            </label>
        </div>

        <div class="pt-2">
            <x-primary-button class="w-full justify-center py-3 bg-slate-900 hover:bg-slate-800 text-white rounded-xl font-bold text-sm shadow-xl shadow-slate-200 transition-all active:scale-95 uppercase tracking-widest border-none">
                {{ __('Iniciar Sesión') }}
            </x-primary-button>
        </div>

        <div class="text-center pt-4 border-t border-slate-100">
            <p class="text-sm text-slate-500">
                ¿Aún no tienes cuenta? 
                <a href="{{ route('register') }}" class="font-bold text-green-600 hover:text-green-700 transition">Regístrate aquí</a>
            </p>
        </div>
    </form>
</x-guest-layout>