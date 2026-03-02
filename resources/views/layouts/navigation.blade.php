<aside class="w-64 bg-slate-900 text-white flex-shrink-0 hidden md:flex flex-col shadow-2xl h-screen sticky top-0 border-r border-slate-800">
    <div class="p-6 flex items-center gap-3 border-b border-slate-800/50">
        <div class="bg-slate-800 p-2 rounded-xl shadow-inner border-b-2 border-green-500">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
            </svg>
        </div>
        <span class="text-xl font-black tracking-tighter uppercase italic">
            Lite<span class="text-green-500">Conta</span>
        </span>
    </div>

    <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto custom-scrollbar">

        <p class="text-[10px] font-bold text-slate-500 uppercase px-3 mb-2 tracking-widest">Resumen</p>

        <a href="{{ route('dashboard') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('dashboard') ? 'bg-green-500 text-slate-900 font-bold shadow-lg shadow-green-500/20' : 'hover:bg-slate-800 text-slate-400 hover:text-white group' }}">
            <i class="fas fa-th-large w-5 text-center text-lg {{ request()->routeIs('dashboard') ? '' : 'group-hover:text-green-500 transition-colors' }}"></i>
            <span class="text-sm tracking-wide">Dashboard</span>
        </a>

        <p class="text-[10px] font-bold text-slate-500 uppercase px-3 mt-6 mb-2 tracking-widest">Operaciones SUNAT</p>

        <a href="{{ route('sales.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('sales.*') ? 'bg-green-500 text-slate-900 font-bold shadow-lg' : 'hover:bg-slate-800 text-slate-400 hover:text-white group' }}">
            <i class="fas fa-file-invoice-dollar w-5 text-center text-lg {{ request()->routeIs('sales.*') ? '' : 'group-hover:text-green-500' }}"></i>
            <span class="text-sm tracking-wide">Mis Ventas</span>
        </a>

        <a href="{{ route('purchases.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('purchases.*') ? 'bg-green-500 text-slate-900 font-bold shadow-lg' : 'hover:bg-slate-800 text-slate-400 hover:text-white group' }}">
            <i class="fas fa-shopping-bag w-5 text-center text-lg {{ request()->routeIs('purchases.*') ? '' : 'group-hover:text-green-500' }}"></i>
            <span class="text-sm tracking-wide">Mis Compras</span>
        </a>

        <a href="{{ route('partners.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('partners.*') ? 'bg-green-500 text-slate-900 font-bold shadow-lg' : 'hover:bg-slate-800 text-slate-400 hover:text-white group' }}">
            <i class="fas fa-address-book w-5 text-center text-lg {{ request()->routeIs('partners.*') ? '' : 'group-hover:text-green-500' }}"></i>
            <span class="text-sm tracking-wide">Clientes / Prov.</span>
        </a>

        <p class="text-[10px] font-bold text-slate-500 uppercase px-3 mt-6 mb-2 tracking-widest">Automatización</p>

        <a href="{{ route('accounting.categories.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('accounting.categories.*') ? 'bg-green-500 text-slate-900 font-bold' : 'hover:bg-slate-800 text-slate-400 hover:text-white group' }}">
            <i class="fas fa-tags w-5 text-center text-lg {{ request()->routeIs('accounting.categories.*') ? '' : 'group-hover:text-green-500' }}"></i>
            <span class="text-sm tracking-wide">Categorías</span>
        </a>

        <a href="{{ route('accounting.rules.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('accounting.rules.*') ? 'bg-green-500 text-slate-900 font-bold' : 'hover:bg-slate-800 text-slate-400 hover:text-white group' }}">
            <i class="fas fa-robot w-5 text-center text-lg {{ request()->routeIs('accounting.rules.*') ? '' : 'group-hover:text-green-500' }}"></i>
            <span class="text-sm tracking-wide">Reglas Auto.</span>
        </a>

        <p class="text-[10px] font-bold text-slate-500 uppercase px-3 mt-6 mb-2 tracking-widest">Sistema</p>

        <a href="{{ route('settings.company.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('settings.company.*') ? 'bg-green-500 text-slate-900 font-bold' : 'hover:bg-slate-800 text-slate-400 hover:text-white group' }}">
            <i class="fas fa-cog w-5 text-center text-lg {{ request()->routeIs('settings.company.*') ? '' : 'group-hover:text-green-500' }}"></i>
            <span class="text-sm tracking-wide">Mi Empresa</span>
        </a>

        <a href="{{ route('settings.users.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('settings.users.*') ? 'bg-green-500 text-slate-900 font-bold' : 'hover:bg-slate-800 text-slate-400 hover:text-white group' }}">
            <i class="fas fa-users-cog w-5 text-center text-lg {{ request()->routeIs('settings.users.*') ? '' : 'group-hover:text-green-500' }}"></i>
            <span class="text-sm tracking-wide">Equipo</span>
        </a>

        <p class="text-[10px] font-bold text-slate-500 uppercase px-3 mt-6 mb-2 tracking-widest">Reportes</p>

        <a href="{{ route('reports.accountant.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('reports.accountant.*') ? 'bg-green-500 text-slate-900 font-bold' : 'hover:bg-slate-800 text-slate-400 hover:text-white group' }}">
            <i class="fas fa-file-contract w-5 text-center text-lg {{ request()->routeIs('reports.accountant.*') ? '' : 'group-hover:text-green-500' }}"></i>
            <span class="text-sm tracking-wide">Reporte Contador</span>
        </a>

        <a href="{{ route('compliance-alerts.index') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('compliance-alerts.*') ? 'bg-green-500 text-slate-900 font-bold shadow-lg shadow-green-500/20' : 'hover:bg-slate-800 text-slate-400 hover:text-white group' }}">
            <i class="fas fa-bell w-5 text-center text-lg {{ request()->routeIs('compliance-alerts.*') ? '' : 'group-hover:text-green-500 transition-colors' }}"></i>
            <span class="text-sm tracking-wide">Alertas SUNAT</span>
        </a>
    </nav>

    <div class="p-4 bg-slate-950/40 m-4 rounded-2xl border border-slate-800">
        <div class="flex flex-col">
            <p class="text-[10px] font-bold text-slate-500 uppercase mb-1 flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span>
                MYPE Activa
            </p>
            <p class="text-xs font-bold text-white truncate" title="{{ Auth::user()->company->razon_social }}">
                {{ Auth::user()->company->razon_social }}
            </p>
            <p class="text-[10px] text-slate-500 font-mono mt-0.5 tracking-tighter">
                RUC: {{ Auth::user()->company->ruc }}
            </p>
        </div>
    </div>
</aside>