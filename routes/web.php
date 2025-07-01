<?php

use App\Livewire\Users\DataUsers;
use App\Livewire\Users\StoreUsers;
use App\Livewire\Users\UpdateUsers;
use Illuminate\Support\Facades\Route;

// Route::view('/', 'welcome');

Route::get('/', function () {
    return redirect()->route('login');
});

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::middleware(['auth'])->group(function () {
    Route::get('/users', DataUsers::class)->name('users.data');
    Route::get('/users/create', StoreUsers::class)->name('users.create');
    Route::get('/users/{user}/edit', UpdateUsers::class)->name('users.edit');
});


require __DIR__.'/auth.php';
