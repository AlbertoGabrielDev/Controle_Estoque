<button 
    @click="isOpen = !isOpen" 
    class="p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500"
    x-cloak
>
    <span class="sr-only">{{ $label }}</span>
    <svg 
        class="h-6 w-6 transition-transform duration-200" 
        :class="{ 'hidden': isOpen, 'block': !isOpen }" 
        fill="none" 
        viewBox="0 0 24 24" 
        stroke="currentColor"
    >
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
    </svg>
    <svg 
        class="h-6 w-6 transition-transform duration-200" 
        :class="{ 'hidden': !isOpen, 'block': isOpen }" 
        fill="none" 
        viewBox="0 0 24 24" 
        stroke="currentColor"
    >
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
    </svg>
</button>