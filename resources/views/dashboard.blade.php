<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="pt-1 pb-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Breadcrumbs (hanya muncul di layar lg ke atas) -->
            <div class="hidden lg:flex justify-end px-4">
                <div class="breadcrumbs text-sm">
                    <ul>
                        <li>
                            <a class="inline-flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 stroke-current" fill="none" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                                </svg>
                                Home
                            </a>
                        </li>
                        <li>
                            <a class="inline-flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 stroke-current" fill="none" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                                </svg>
                                Documents
                            </a>
                        </li>
                        <li>
                            <span class="inline-flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 stroke-current" fill="none" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Add Document
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
            <!-- Page Title -->
            <div class="max-w-full mx-auto sm:px-6 lg:px-8">
                <h1 class="text-2xl font-bold text-base-content">
                    <i class="fa-solid fa-layer-group"></i>
                    Dashboard
                </h1>
            </div>

            <!-- Main Content -->
            <div class="max-w-full mx-auto sm:px-6 lg:px-8">
                <div class="bg-base-100 overflow-hidden shadow-xs rounded-sm sm:rounded-lg">
                    <div class="p-6 text-base-content space-y-4">
                        <p>{{ __("You're logged in!") }}</p>
                        <div class="flex flex-wrap gap-2">
                            <button class="btn btn-neutral">Neutral</button>
                            <button class="btn btn-primary">Primary</button>
                            <button class="btn btn-secondary">Secondary</button>
                            <button class="btn btn-accent">Accent</button>
                            <button class="btn btn-info">Info</button>
                            <button class="btn btn-success">Success</button>
                            <button class="btn btn-warning">Warning</button>
                            <button class="btn btn-error">Error</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
