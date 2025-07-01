<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
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

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="max-w-md mx-auto p-6 bg-base-100 rounded-box">
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="login" class="space-y-4">
        <!-- Email Address -->
        <div class="form-control">
            <label for="email" class="label">
                <span class="label-text">Email / Username</span>
            </label>
            <input wire:model="form.login" id="login" name="login" type="text"
                required autofocus autocomplete="username"
                class="input input-bordered w-full" />
            <x-input-error :messages="$errors->get('form.login')" class="mt-1 text-error text-sm" />
        </div>


        <!-- Password -->
        <div class="form-control">
            <div class="flex justify-between items-center mb-1">
                <label for="password" class="label p-0">
                    <span class="label-text">Password</span>
                </label>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" wire:navigate
                    class="text-sm text-gray-600 hover:underline focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-neutral">
                        Forgot password?
                    </a>
                @endif
            </div>

            <input wire:model="form.password" id="password" name="password" type="password"
                required autocomplete="current-password"
                class="input input-bordered w-full" />

            <x-input-error :messages="$errors->get('form.password')" class="mt-1 text-error text-sm" />
        </div>

        <!-- Remember Me -->
        <div class="form-control">
            <label class="label cursor-pointer justify-start space-x-2">
                <input wire:model="form.remember" type="checkbox" class="checkbox checkbox-xs checkbox-success" id="remember" name="remember" />
                <span class="label-text">Remember me</span>
            </label>
        </div>

        <!-- Actions -->
        <div class="flex flex-col sm:flex-row justify-between items-center gap-2 mt-4">
            {{-- @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" wire:navigate
                   class="text-sm text-primary hover:underline focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                    Forgot your password?
                </a>
            @endif --}}

            <button type="submit" class="btn btn-accent w-full sm:w-auto">
                Log in
            </button>
        </div>
    </form>
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
