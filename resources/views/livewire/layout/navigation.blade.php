<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<nav class="bg-base-100 border-b border-base-200 px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center h-[60px] max-w-7xl mx-auto">
        <!-- Sidebar Toggle (Mobile & Desktop) -->
        <div class="flex items-center">
            <button @click="sidebarOpen = !sidebarOpen" type="button"
                class="inline-flex items-center p-2 text-base-content rounded-lg hover:bg-base-200 focus:outline-none">
                <span class="sr-only">Toggle sidebar</span>
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M3 5h14a1 1 0 010 2H3a1 1 0 110-2zm0 5h14a1 1 0 010 2H3a1 1 0 110-2zm0 5h14a1 1 0 010 2H3a1 1 0 110-2z"
                        clip-rule="evenodd" />
                </svg>
            </button>
        </div>

        <!-- Right Section -->
        <div class="flex items-center gap-4">
            <!-- Theme Toggle (Desktop Only) -->
            <label class="hidden lg:flex items-center gap-2 cursor-pointer">
                <!-- Sun Icon -->
                <i class="fa-regular fa-sun text-lg text-yellow-500"></i>

                <!-- Toggle Switch -->
                <input type="checkbox" class="theme-controller toggle" value="night" />

                <!-- Moon Icon -->
                <i class="fa-regular fa-moon text-lg text-purple-500"></i>
            </label>

            <!-- User Dropdown -->
            <div class="dropdown dropdown-end">
                <label tabindex="0" class="btn btn-ghost px-3 py-2 text-sm flex items-center gap-2">
                    <span class="hidden sm:inline" 
                        x-data="{ name: '{{ auth()->user()->biodata->nama_lengkap ?? auth()->user()->name }}' }"
                        x-text="name" 
                        x-on:profile-updated.window="name = $event.detail.name">
                    </span>
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 9l-7 7-7-7" />
                    </svg>
                </label>

                <!-- Dropdown Menu -->
                <ul tabindex="0"
                    class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-52">
                    <!-- Theme Toggle (Mobile Only) -->
                    <li class="sm:hidden">
                        <label class="swap swap-rotate">
                            <input type="checkbox" class="theme-controller hidden" />
                            <i class="fa-regular fa-sun text-lg text-yellow-500 swap-off w-5 h-5 fill-current"></i>
                            <i class="fa-regular fa-moon text-lg text-purple-500 swap-on w-5 h-5 fill-current"></i>
                        </label>
                    </li>

                    <li><a href="{{ route('profile') }}" wire:navigate>Profile</a></li>
                    <li><button wire:click="logout" class="text-red-500 font-bold">Log Out</button></li>
                </ul>
            </div>
        </div>
    </div>
</nav>