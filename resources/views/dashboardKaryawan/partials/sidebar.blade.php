<aside class="fixed left-0 hidden md:block h-[calc(100vh-4rem)] bg-white border-r border-gray-300 transition-all duration-300 z-30 shadow-sm" :class="isSidebarCollapsed ? 'w-20' : 'w-64'">
    <nav class="p-4 h-full overflow-y-auto custom-scrollbar-green">
        <ul class="space-y-2">
            @php
                $menus = [
                    ['name' => 'Home', 'route' => 'karyawan.home', 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
                    ['name' => 'Address', 'route' => 'karyawan.alamat.index', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                ];
            @endphp

            @foreach ($menus as $menu)
                @php
                    $isActive = Route::is($menu['route']);
                @endphp
                <li>
                    <a href="{{ route($menu['route']) }}"
                        @class([
                            'flex items-center gap-3 px-3 py-2 rounded-md border shadow-sm transition-colors',
                            'bg-[#39AA80] text-white border-[#39AA80] shadow-blue-100' => $isActive,
                            'hover:bg-green-50 text-[#39AA80] border-transparent' => !$isActive,
                        ])
                    >
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="transition-all"
                            :class="(isSidebarCollapsed ? 'h-7 w-7' : 'h-5 w-5')"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="{{ $isActive ? 'white' : '#39AA80' }}"
                            stroke-width="2"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $menu['icon'] }}" />
                        </svg>
                        <span class="font-medium whitespace-nowrap transition-opacity"
                            :class="isSidebarCollapsed ? 'opacity-0 hidden' : 'opacity-100'">
                            {{ $menu['name'] }}
                        </span>
                    </a>
                </li>
            @endforeach
        </ul>
    </nav>
</aside>
