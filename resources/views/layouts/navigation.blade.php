<aside class="w-64 bg-slate-900 text-white flex-shrink-0 hidden md:flex flex-col shadow-2xl h-screen sticky top-0 border-r border-slate-800">
    <div class="p-6 flex items-center gap-3 border-b border-slate-800">
        <div class="bg-white p-1.5 rounded-lg shadow-sm">
             <i class="fas fa-shield-alt text-slate-900 text-xl"></i>
        </div>
        <span class="text-xl font-bold tracking-tighter uppercase italic">
            Lite<span class="text-green-500">Conta</span>
        </span>
    </div>

    <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto custom-scrollbar">
        
        <p class="text-[10px] font-bold text-slate-500 uppercase px-3 mb-2 tracking-widest">Resumen</p>
        
        <a href="{{ route('dashboard') }}" 
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('dashboard') ? 'bg-green-500 text-slate-900 font-bold shadow-lg shadow-green-900/20' : 'hover:bg-slate-800 text-slate-300 hover:text-white' }}">
            <i class="fas fa-th-large w-5 text-center text-lg"></i>
            <span class="text-sm tracking-wide">Dashboard</span>
        </a>

        <p class="text-[10px] font-bold text-slate-500 uppercase px-3 mt-6 mb-2 tracking-widest">Operaciones SUNAT</p>

        <a href="{{ route('sales.index') }}" 
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('sales.*') ? 'bg-green-500 text-slate-900 font-bold' : 'hover:bg-slate-800 text-slate-300 hover:text-white' }}">
            <i class="fas fa-file-invoice-dollar w-5 text-center text-lg"></i>
            <span class="text-sm tracking-wide">Mis Ventas</span>
        </a>
        <a href="{{ route('purchases.index') }}" 
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('purchases.*') ? 'bg-green-500 text-slate-900 font-bold shadow-lg shadow-green-900/20' : 'hover:bg-slate-800 text-slate-300 hover:text-white' }}">
            <i class="fas fa-shopping-bag w-5 text-center text-lg"></i>
            <span class="text-sm tracking-wide">Mis Compras</span>
        </a>

        <a href="{{ route('partners.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('partners.*') ? 'bg-green-500 text-slate-900 font-bold' : 'hover:bg-slate-800 text-slate-300 hover:text-white' }}">
            <i class="fas fa-address-book w-5 text-center text-lg"></i>
            <span class="text-sm tracking-wide">Clientes / Prov.</span>
        </a>

        <p class="text-[10px] font-bold text-slate-500 uppercase px-3 mt-6 mb-2 tracking-widest">Automatización</p>

        <a href="#" 
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('categories.*') ? 'bg-green-500 text-slate-900 font-bold' : 'hover:bg-slate-800 text-slate-300 hover:text-white' }}">
            <i class="fas fa-tags w-5 text-center text-lg"></i>
            <span class="text-sm tracking-wide">Categorías</span>
        </a>

        <a href="#" 
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('rules.*') ? 'bg-green-500 text-slate-900 font-bold' : 'hover:bg-slate-800 text-slate-300 hover:text-white' }}">
            <i class="fas fa-robot w-5 text-center text-lg"></i>
            <span class="text-sm tracking-wide">Reglas Auto.</span>
        </a>

        <p class="text-[10px] font-bold text-slate-500 uppercase px-3 mt-6 mb-2 tracking-widest">Sistema</p>

        <a href="#" 
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('settings.company') ? 'bg-green-500 text-slate-900 font-bold' : 'hover:bg-slate-800 text-slate-300 hover:text-white' }}">
            <i class="fas fa-cog w-5 text-center text-lg"></i>
            <span class="text-sm tracking-wide">Mi Empresa</span>
        </a>

        <a href="#" 
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('settings.users.*') ? 'bg-green-500 text-slate-900 font-bold' : 'hover:bg-slate-800 text-slate-300 hover:text-white' }}">
            <i class="fas fa-users-cog w-5 text-center text-lg"></i>
            <span class="text-sm tracking-wide">Equipo</span>
        </a>
    </nav>

    <div class="p-4 bg-slate-800/50 m-4 rounded-2xl border border-slate-700">
        <div class="flex flex-col">
            <p class="text-[10px] font-bold text-slate-400 uppercase mb-1 flex items-center gap-1">
                <i class="fas fa-university text-[8px]"></i> MYPE Activa
            </p>
            <p class="text-xs font-bold text-green-500 truncate" title="{{ Auth::user()->company->razon_social }}">
                {{ Auth::user()->company->razon_social }}
            </p>
            <p class="text-[10px] text-slate-400 font-mono mt-0.5 tracking-tighter">
                RUC: {{ Auth::user()->company->ruc }}
            </p>
        </div>
    </div>
</aside>