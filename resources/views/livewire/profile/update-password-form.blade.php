<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Component;

new class extends Component
{
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Update the password for the currently authenticated user.
     */
    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => ['required', 'string', 'current_password'],
                'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');

            throw $e;
        }

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        $this->dispatch('password-updated');
    }
}; ?>

<section>
    <header class="mb-4">
        <h2 class="text-xl font-bold text-base-content">
            {{ __('Ubah Password') }}
        </h2>
        <p class="mt-1 text-sm text-base-content/70">
            {{ __('Pastikan akun Anda menggunakan kata sandi panjang dan acak agar tetap aman.') }}
        </p>
    </header>

    <form wire:submit="updatePassword" class="space-y-5">
        <div class="form-control">
            <label class="label" for="update_password_current_password">
                <span class="label-text">{{ __('Password Lama') }}</span>
            </label>
            <input wire:model="current_password" id="update_password_current_password" name="current_password"
                   type="password" autocomplete="current-password"
                   class="input input-bordered w-full" />
            <x-input-error :messages="$errors->get('current_password')" class="mt-1 text-error text-sm" />
        </div>

        <div class="form-control">
            <label class="label" for="update_password_password">
                <span class="label-text">{{ __('Password Baru') }}</span>
            </label>
            <input wire:model="password" id="update_password_password" name="password"
                   type="password" autocomplete="new-password"
                   class="input input-bordered w-full" />
            <x-input-error :messages="$errors->get('password')" class="mt-1 text-error text-sm" />
        </div>

        <div class="form-control">
            <label class="label" for="update_password_password_confirmation">
                <span class="label-text">{{ __('Konfirmasi Password') }}</span>
            </label>
            <input wire:model="password_confirmation" id="update_password_password_confirmation" name="password_confirmation"
                   type="password" autocomplete="new-password"
                   class="input input-bordered w-full" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-error text-sm" />
        </div>

        <div class="flex items-center gap-4 mt-2">
            <x-primary-button>
                {{ __('Simpan') }}
            </x-primary-button>

            <x-action-message class="text-success text-sm" on="password-updated">
                {{ __('Tersimpan.') }}
            </x-action-message>
        </div>
    </form>
</section>

{{-- <section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Update Password') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form wire:submit="updatePassword" class="mt-6 space-y-6">
        <div>
            <x-input-label for="update_password_current_password" :value="__('Current Password')" />
            <x-text-input wire:model="current_password" id="update_password_current_password" name="current_password" type="password" class="mt-1 block w-full" autocomplete="current-password" />
            <x-input-error :messages="$errors->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password" :value="__('New Password')" />
            <x-text-input wire:model="password" id="update_password_password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" :value="__('Confirm Password')" />
            <x-text-input wire:model="password_confirmation" id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            <x-action-message class="me-3" on="password-updated">
                {{ __('Saved.') }}
            </x-action-message>
        </div>
    </form>
</section> --}}
