<!-- Navigation -->
<nav class="bg-white shadow-sm sticky top-0 z-50">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="{{ url('/') }}" class="flex items-center">
                    <div class="h-8 w-8 rounded-md flex items-center justify-center mr-2">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-full h-full object-cover">
                    </div>
                    <span class="font-bold text-xl text-[#39AA80]">CompCarbon</span>
                </a>
            </div>

            <!-- Desktop Navigation -->
            <div class="hidden md:flex items-center space-x-4">
                <div class="hidden md:flex space-x-1">
                    @php
                    $menuItems = [
                        ['name' => 'Home', 'href' => url('/')],
                        ['name' => 'Services', 'href' => url('/#services')],
                        ['name' => 'About', 'href' => url('/#about')],
                        // ['name' => 'Contact', 'href' => url('/#contact')]
                    ];
                    @endphp

                    @foreach($menuItems as $item)
                    <a href="{{ $item['href'] }}" class="px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-[#39AA80] hover:bg-gray-50 transition-colors duration-200">
                        {{ $item['name'] }}
                    </a>
                    @endforeach
                </div>

                <div class="hidden md:flex items-center ml-4 space-x-2">
                    <a href="{{ route('login') }}" class="px-4 py-2 rounded-md text-[#39AA80] border border-[#39AA80] hover:bg-[#39AA80]/10 transition-colors">
                        Sign In
                    </a>
                    <a href="{{ route('register') }}" class="px-4 py-2 rounded-md bg-[#39AA80] text-white hover:bg-[#39AA80]/90 transition-colors">
                        Sign Up
                    </a>
                </div>
            </div>

            <!-- Mobile menu button -->
            <div class="md:hidden flex items-center">
                <button id="mobile-menu-button" class="inline-flex items-center justify-center p-2 rounded-md text-gray-700 hover:text-[#39AA80] hover:bg-gray-100 focus:outline-none" aria-expanded="false">
                    <span class="sr-only">Open main menu</span>
                    <!-- Icon when menu is closed -->
                    <svg id="menu-icon" class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <!-- Icon when menu is open -->
                    <svg id="close-icon" class="hidden h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div id="mobile-menu" class="hidden md:hidden">
        <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3 bg-white shadow-lg">
            @foreach($menuItems as $item)
            <a href="{{ $item['href'] }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-[#39AA80] hover:bg-gray-50">
                {{ $item['name'] }}
            </a>
            @endforeach
            <div class="pt-4 pb-3 border-t border-gray-200">
                <div class="flex items-center px-3 space-x-2">
                    <a href="{{ route('login') }}" class="w-full px-4 py-2 rounded-md text-center text-[#39AA80] border border-[#39AA80] hover:bg-[#39AA80]/10 transition-colors">
                        Sign In
                    </a>
                    <a href="{{ route('register') }}" class="w-full px-4 py-2 rounded-md text-center bg-[#39AA80] text-white hover:bg-[#39AA80]/90 transition-colors">
                        Sign Up
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        const menuIcon = document.getElementById('menu-icon');
        const closeIcon = document.getElementById('close-icon');
        
        mobileMenuButton.addEventListener('click', function() {
            mobileMenu.classList.toggle('hidden');
            menuIcon.classList.toggle('hidden');
            closeIcon.classList.toggle('hidden');
        });
        
        // Close menu when clicking on a link
        const mobileLinks = mobileMenu.querySelectorAll('a');
        mobileLinks.forEach(link => {
            link.addEventListener('click', function() {
                mobileMenu.classList.add('hidden');
                menuIcon.classList.remove('hidden');
                closeIcon.classList.add('hidden');
            });
        });
    });
</script>
