<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $email = '';

    /**
     * Kirim tautan reset password ke alamat email yang diberikan.
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        // Kita akan mengirim tautan reset password ke user ini. Setelah kita mencoba
        // mengirim tautannya, kita akan memeriksa responsnya lalu melihat pesan yang
        // perlu kita tampilkan ke user. Terakhir, kita akan mengirimkan respons yang sesuai.
        $status = Password::sendResetLink(
            $this->only('email')
        );

        if ($status != Password::RESET_LINK_SENT) {
            $this->addError('email', __($status));

            return;
        }

        $this->reset('email');

        session()->flash('status', __($status));
    }
}; ?>

<div class="flex items-center justify-center py-8">
    <div class="w-full max-w-md">
        <div class="bg-base-100 rounded-box shadow-sm border border-base-300 p-6 sm:p-8 space-y-5">

            <div class="flex justify-center">
                <div class="rounded-full bg-primary/10 p-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" />
                    </svg>
                </div>
            </div>

            <div class="text-center space-y-1">
                <h2 class="text-lg font-semibold text-base-content">
                    {{ __('Lupa Password') }}
                </h2>
                <p class="text-sm text-base-content/70">
                    {{ __('Lupa password kamu? Tidak masalah. Cukup beri tahu kami alamat email kamu dan kami akan mengirimkan tautan reset password yang memungkinkan kamu memilih password baru.') }}
                </p>
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="text-sm" :status="session('status')" />

            <!-- Form -->
            <form wire:submit="sendPasswordResetLink" class="space-y-4">
                <!-- Email -->
                <div class="form-control">
                    <label for="email" class="label">
                        <span class="label-text">Email</span>
                    </label>
                    <input wire:model="email" id="email" name="email" type="email" required autofocus
                           class="input input-bordered w-full" />
                    <x-input-error :messages="$errors->get('email')" class="mt-1 text-error text-sm" />
                </div>

                <!-- Submit -->
                <div class="flex justify-end pt-2">
                    <button type="submit" class="btn btn-accent w-full sm:w-auto">
                        {{ __('Kirim Tautan Reset Password') }}
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

{{-- <div>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="sendPasswordResetLink">
        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input wire:model="email" id="email" class="block mt-1 w-full" type="email" name="email" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Email Password Reset Link') }}
            </x-primary-button>
        </div>
    </form>
</div> --}}
