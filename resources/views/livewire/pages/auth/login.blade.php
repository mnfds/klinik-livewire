<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        // Cek status aktif setelah berhasil login
        if (Auth::user()->status != 1) {
            Auth::logout();

            // Session::invalidate();
            // Session::regenerateToken();

            throw ValidationException::withMessages([
                'form.login' => 'Akun kamu tidak aktif. Silakan hubungi admin.',
            ]);
        }

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="flex items-center justify-center py-8 lg:py-12">
    <div class="w-full max-w-4xl">
        <div class="grid lg:grid-cols-2 bg-base-100 rounded-box shadow-sm border border-base-300 overflow-hidden">

            <!-- Panel Kiri: Branding -->
            <div class="hidden lg:flex flex-col justify-between bg-gradient-to-br from-secondary to-primary-focus text-primary-content p-10">
                <div class="flex items-center gap-2">
                    <div class="bg-primary-content/10 rounded-lg p-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-pink-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                        </svg>
                    </div>
                    <span class="text-xl font-bold tracking-tight text-primary-content">
                        SIKLINIK
                    </span>
                </div>

                <div class="space-y-3">
                    <h2 class="text-2xl font-bold leading-snug">
                        {{ __('Kelola klinik kamu dengan lebih mudah.') }}
                    </h2>
                    <p class="text-primary-content/80 text-sm leading-relaxed">
                        {{ __('Rekam medis, jadwal staf, inventori, hingga transaksi — semua dalam satu sistem terintegrasi.') }}
                    </p>
                </div>

                <div class="text-xs text-primary-content/80">
                    &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                </div>
            </div>

            <!-- Panel Kanan: Form -->
            <div class="p-6 sm:p-10 flex flex-col justify-center">
                <div class="mb-6 text-center lg:text-left">
                    <h1 class="text-xl font-semibold text-base-content">
                        {{ __('Selamat Datang Kembali') }}
                    </h1>
                    <p class="text-sm text-base-content/60 mt-1">
                        {{ __('Masuk untuk melanjutkan ke akun kamu.') }}
                    </p>
                </div>

                <!-- Session Status -->
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form wire:submit="login" class="space-y-4">
                    <!-- Email Address -->
                    <div class="form-control">
                        <label for="login" class="label">
                            <span class="label-text">Email / Username</span>
                        </label>
                        <input wire:model="form.login" id="login" name="login" type="text"
                            required autofocus autocomplete="username"
                            class="input input-bordered w-full" />
                        <x-input-error :messages="$errors->get('form.login')" class="mt-1 text-error text-sm" />
                    </div>

                    <!-- Password -->
                    <div class="form-control" x-data="{ showPassword: false }">
                        <div class="flex justify-between items-center mb-1">
                            <label for="password" class="label p-0">
                                <span class="label-text">Password</span>
                            </label>

                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" wire:navigate
                                   class="text-xs text-primary hover:underline focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                                    {{ __('Lupa Password?') }}
                                </a>
                            @endif
                        </div>

                        <div class="relative">
                            <input wire:model="form.password" id="password" name="password"
                                :type="showPassword ? 'text' : 'password'"
                                required autocomplete="current-password"
                                class="input input-bordered w-full pr-10" />
                            <button type="button"
                                    @click="showPassword = !showPassword"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-base-content/50 hover:text-base-content"
                                    tabindex="-1">
                                <svg x-show="!showPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <svg x-show="showPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                </svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('form.password')" class="mt-1 text-error text-sm" />
                    </div>

                    <!-- Remember Me -->
                    <div class="form-control">
                        <label class="label cursor-pointer justify-start space-x-2 p-0">
                            <input wire:model="form.remember" type="checkbox" class="checkbox checkbox-xs checkbox-success" id="remember" name="remember" />
                            <span class="label-text text-sm">{{ __('Ingat Saya') }}</span>
                        </label>
                    </div>

                    <!-- Actions -->
                    <button type="submit" class="btn btn-secondary w-full mt-2">
                        {{ __('Masuk') }}
                    </button>
                </form>

            </div>

        </div>
    </div>
</div>

{{-- <div>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="login">
        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input wire:model="form.email" id="email" class="block mt-1 w-full" type="email" name="email" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input wire:model="form.password" id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember" class="inline-flex items-center">
                <input wire:model="form.remember" id="remember" type="checkbox" class="rounded-sm border-gray-300 text-indigo-600 shadow-xs focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-hidden focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}" wire:navigate>
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</div> --}}
