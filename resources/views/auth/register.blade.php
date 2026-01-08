<x-guest-layout>
    <div class="mb-8 text-center">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-slate-900 rounded-2xl mb-4 shadow-lg border-b-4 border-green-500">
            <i class="fas fa-building text-white text-2xl"></i>
        </div>
        <h2 class="text-2xl font-black text-slate-800 tracking-tight">Registro de Empresa</h2>
        <p class="text-slate-500 text-sm mt-1 font-medium">Configura tu cuenta de <span class="italic">Lite<span class="text-green-500 font-bold">Conta</span></span></p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <div class="relative">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3 flex items-center">
                <span class="mr-2">01</span> Información Personal
                <span class="flex-1 h-px bg-slate-100 ml-2"></span>
            </p>
            
            <div class="mb-3">
                <x-input-label for="name" :value="__('Tu Nombre')" class="text-xs font-bold uppercase text-slate-500 ml-1" />
                <div class="relative mt-1">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i class="fas fa-user text-sm"></i>
                    </div>
                    <x-text-input id="name" class="block w-full pl-10 border-slate-200 rounded-xl focus:ring-green-500 focus:border-green-500 transition-all" type="text" name="name" :value="old('name')" required autofocus placeholder="Nombre completo" />
                </div>
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div class="mb-3">
                <x-input-label for="email" :value="__('Email')" class="text-xs font-bold uppercase text-slate-500 ml-1" />
                <div class="relative mt-1">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i class="fas fa-envelope text-sm"></i>
                    </div>
                    <x-text-input id="email" class="block w-full pl-10 border-slate-200 rounded-xl focus:ring-green-500 focus:border-green-500 transition-all" type="email" name="email" :value="old('email')" required placeholder="correo@ejemplo.com" />
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>
        </div>

        <div class="relative mt-6">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3 flex items-center">
                <span class="mr-2">02</span> Información de la Empresa
                <span class="flex-1 h-px bg-slate-100 ml-2"></span>
            </p>

            <div class="mb-3">
                <x-input-label for="ruc" :value="__('RUC de la Empresa')" class="text-xs font-bold uppercase text-slate-500 ml-1" />
                <div class="relative mt-1">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i class="fas fa-id-card text-sm"></i>
                    </div>
                    <x-text-input id="ruc" class="block w-full pl-10 border-slate-200 rounded-xl focus:ring-green-500 focus:border-green-500 transition-all font-mono" type="text" name="ruc" :value="old('ruc')" required placeholder="11 dígitos" maxlength="11" />
                </div>
                <x-input-error :messages="$errors->get('ruc')" class="mt-2" />
            </div>

            <div class="mb-3">
                <x-input-label for="razon_social" :value="__('Razón Social')" class="text-xs font-bold uppercase text-slate-500 ml-1" />
                <div class="relative mt-1">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i class="fas fa-briefcase text-sm"></i>
                    </div>
                    <x-text-input id="razon_social" class="block w-full pl-10 border-slate-200 rounded-xl focus:ring-green-500 focus:border-green-500 transition-all" type="text" name="razon_social" :value="old('razon_social')" required placeholder="Nombre legal de la empresa" />
                </div>
                <x-input-error :messages="$errors->get('razon_social')" class="mt-2" />
            </div>
        </div>

        <div class="relative mt-6">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3 flex items-center">
                <span class="mr-2">03</span> Seguridad
                <span class="flex-1 h-px bg-slate-100 ml-2"></span>
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="password" :value="__('Contraseña')" class="text-xs font-bold uppercase text-slate-500 ml-1" />
                    <div class="relative mt-1">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <i class="fas fa-lock text-sm"></i>
                        </div>
                        <x-text-input id="password" class="block w-full pl-10 border-slate-200 rounded-xl focus:ring-green-500 focus:border-green-500 transition-all" type="password" name="password" required placeholder="••••••••" />
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="password_confirmation" :value="__('Confirmar')" class="text-xs font-bold uppercase text-slate-500 ml-1" />
                    <div class="relative mt-1">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <i class="fas fa-check-double text-sm"></i>
                        </div>
                        <x-text-input id="password_confirmation" class="block w-full pl-10 border-slate-200 rounded-xl focus:ring-green-500 focus:border-green-500 transition-all" type="password" name="password_confirmation" required placeholder="••••••••" />
                    </div>
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between mt-8 pt-4 border-t border-slate-100">
            <a class="text-sm font-bold text-slate-500 hover:text-slate-800 transition" href="{{ route('login') }}">
                {{ __('¿Ya tienes cuenta?') }}
            </a>

            <x-primary-button class="px-8 py-3 bg-slate-900 hover:bg-slate-800 text-white rounded-xl font-bold text-sm shadow-xl shadow-slate-200 transition-all active:scale-95 border-none">
                {{ __('Finalizar Registro') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>