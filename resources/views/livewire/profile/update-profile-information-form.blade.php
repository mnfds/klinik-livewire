<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new class extends Component
{
    public string $name = '';
    public string $email = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('profile-updated', name: $user->name);
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function sendVerification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    public function kirimUlangVerifikasi()
    {
        $user = Auth::user(); // pastikan user ada

        if ($user && !$user->hasVerifiedEmail()) {
            event(new Registered($user)); // kirim ulang email verifikasi
            $this->dispatch('toast', ['type' => 'success', 'message' => 'Email verifikasi dikirim ulang.']);
        } else {
            $this->dispatch('toast', ['type' => 'info', 'message' => 'Email sudah terverifikasi.']);
        }
    }

    public function kirimResetPassword()
    {
        $user = Auth::user(); // atau berdasarkan email

        if ($user) {
            Password::sendResetLink(['email' => $user->email]);
            $this->dispatch('toast', ['type' => 'success', 'message' => 'Link reset password dikirim.']);
        } else {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'User tidak ditemukan.']);
        }
    }
}; ?>

<section>
    <header class="mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <!-- Judul dan Deskripsi -->
            <div>
                <h2 class="text-xl font-bold text-base-content">
                    {{ __('Informasi Akun') }}
                </h2>
                <p class="mt-1 text-sm text-base-content/70">
                    {{ __('Perbarui Username dan alamat email akun Anda.') }}
                </p>
            </div>

            <!-- Tombol Aksi -->
            <div class="flex flex-wrap gap-2">
                <button type="button" wire:click="kirimUlangVerifikasi" class="btn btn-info">
                    Verifikasi Email
                </button>
                <button type="button" wire:click="kirimResetPassword" class="btn btn-warning">
                    Kirim Reset Password
                </button>
            </div>
        </div>
    </header>

    <form wire:submit="updateProfileInformation" class="space-y-5">
        <!-- Username -->
        <div class="form-control">
            <label for="name" class="label">
                <span class="label-text">{{ __('Username') }} <span class="text-error">*</span></span>
            </label>
            <input wire:model="name" id="name" name="name" type="text"
                    autofocus autocomplete="name"
                    class="input input-bordered w-full @error('name') input-error @enderror" />
            @error('name')
            <span class="mt-1 text-error text-sm">
                Mohon mengisi Username
            </span>   
            @enderror
            {{-- <x-input-error :messages="$errors->get('name')" class="mt-1 text-error text-sm" /> --}}
        </div>

        <!-- Email -->
        <div class="form-control">
            <label for="email" class="label">
                <span class="label-text">{{ __('Email') }} <span class="text-error">*</span></span>
            </label>
            <input wire:model="email" id="email" name="email" type="email"
                    autocomplete="username"
                    class="input input-bordered w-full @error('email') input-error @enderror" />
                @error('email')
                    <span class="text-error text-sm mt-1">
                        Mohon Mengisi Email Dengan Benar
                    </span>
                @enderror
            {{-- <x-input-error :messages="$errors->get('email')" class="mt-1 text-error text-sm" /> --}}

            @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !auth()->user()->hasVerifiedEmail())
                <div class="mt-2 text-sm">
                    <p class="text-base-content">
                        {{ __('Alamat email Anda belum diverifikasi.') }}
                        <button wire:click.prevent="sendVerification"
                                class="underline text-primary hover:text-primary-focus focus:outline-none ml-1">
                            {{ __('Kirim ulang email verifikasi.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-1 font-medium text-success">
                            {{ __('Link verifikasi baru telah dikirim.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <!-- Tombol -->
        <div class="flex items-center gap-4 mt-2">
            <x-primary-button>
                {{ __('Simpan') }}
            </x-primary-button>

            <x-action-message class="text-success text-sm" on="profile-updated">
                {{ __('Tersimpan.') }}
            </x-action-message>
        </div>
    </form>
</section>

{{-- <section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form wire:submit="updateProfileInformation" class="mt-6 space-y-6">
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input wire:model="name" id="name" name="name" type="text" class="mt-1 block w-full" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input wire:model="email" id="email" name="email" type="email" class="mt-1 block w-full" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button wire:click.prevent="sendVerification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-hidden focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            <x-action-message class="me-3" on="profile-updated">
                {{ __('Saved.') }}
            </x-action-message>
        </div>
    </form>
</section> --}}
