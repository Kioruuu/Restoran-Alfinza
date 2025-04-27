<!-- Top header -->
<header class="flex justify-between items-center py-4 px-6 bg-white border-b-4 border-blue-600">
    <div class="flex items-center">
        <button class="text-gray-500 focus:outline-none md:hidden">
            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M4 6H20M4 12H20M4 18H20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
    </div>
    
    <div class="flex items-center gap-4">
        <div class="relative">
            <button class="flex items-center space-x-2 text-gray-700 focus:outline-none">
                <i class="fas fa-user-circle text-2xl"></i>
                <span class="text-sm font-medium"><?php echo $_SESSION['username']; ?></span>
            </button>
        </div>
    </div>
</header> 