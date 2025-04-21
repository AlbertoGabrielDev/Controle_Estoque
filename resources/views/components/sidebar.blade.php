<nav class="list-group list-group-horizontal">
    <!-- Botão Mobile -->
    <div class="md:hidden text-right p-2">
        <button id="toggleMenu" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-bars fa-lg"></i>
        </button>
    </div>

    <!-- Itens do Menu -->
    @foreach($menus as $menu)
        @if($menu->children->count())
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="w-full text-left block text-gray-500 py-2.5 px-4 my-1 rounded transition duration-200 hover:bg-gradient-to-r hover:from-cyan-400 hover:to-cyan-300 hover:text-white">
                    <i class="{{ $menu->icon }}"></i>
                    {{ $menu->name }}
                    <i class="fas fa-chevron-down float-right mt-1 text-xs transition-transform" :class="{'rotate-180': open}"></i>
                </button>
                
                <!-- Submenu -->
                <div x-show="open" @click.outside="open = false" class="ml-4">
                    @foreach($menu->children as $child)
                        @if(auth()->user()->hasPermission($child->slug, 'view_post'))
                        <a href="{{ route($child->route) }}" class="block text-gray-500 py-2 px-4 my-1 rounded transition duration-200 hover:bg-gradient-to-r hover:from-cyan-400 hover:to-cyan-300 hover:text-white {{ request()->routeIs($child->route) ? 'bg-cyan-100 text-cyan-600' : '' }}">
                            <i class="{{ $child->icon }}"></i>
                            {{ $child->name }}
                        </a>
                        @endif
                    @endforeach
                </div>
            </div>
        @else
            @if(auth()->user()->hasPermission($menu->slug, 'view_post'))
            <a href="{{ route($menu->route) }}" class="block text-gray-500 py-2.5 px-4 my-1 rounded transition duration-200 hover:bg-gradient-to-r hover:from-cyan-400 hover:to-cyan-300 hover:text-white {{ request()->routeIs($menu->route) ? 'bg-cyan-100 text-cyan-600' : '' }}">
                <i class="{{ $menu->icon }}"></i>
                {{ $menu->name }}
            </a>
            @endif
        @endif
    @endforeach
</nav>