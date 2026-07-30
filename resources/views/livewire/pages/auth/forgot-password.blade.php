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

<div class="space-y-4">
    <!-- Deskripsi -->
    <div class="text-sm text-base-content">
        {{ __('Lupa password kamu? Tidak masalah. Cukup beri tahu kami alamat email kamu dan kami akan mengirimkan tautan reset password yang memungkinkan kamu memilih password baru.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

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
        <div class="flex justify-end">
            <button type="submit" class="btn btn-accent">
                {{ __('Kirim Tautan Reset Password') }}
            </button>
        </div>
    </form>
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
