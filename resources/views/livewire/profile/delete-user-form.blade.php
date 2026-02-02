<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component
{
    public string $password = '';

    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            'password' => ['required', 'string', 'current_password'],
        ]);

        tap(Auth::user(), $logout(...))->delete();

        $this->dispatch('account-deleted');
    }
};
?>

<section x-data="{ showModal: false }" x-init="
    Livewire.on('account-deleted', () => {
        showModal = false
        window.location.href = '/'
    });
" class="space-y-6">
    <header>
        <h2 class="text-xl font-bold text-base-content">
            {{ __('Hapus Akun') }}
        </h2>
        <p class="mt-1 text-sm text-base-content/70">
            {{ __('Setelah akun Anda dihapus, semua data Anda akan terhapus permanen.') }}
        </p>
    </header>

    <!-- Tombol Buka Modal -->
    <button class="btn btn-error" @click="showModal = true">
        {{ __('Hapus Akun') }}
    </button>

    <!-- Modal DaisyUI -->
    <div class="modal" :class="{ 'modal-open': showModal }">
        <div class="modal-box">
            <h3 class="font-bold text-lg">
                {{ __('Yakin ingin menghapus akun?') }}
            </h3>
            <p class="py-2 text-sm text-base-content/70">
                {{ __('Silakan masukkan password Anda untuk konfirmasi.') }}
            </p>

            <form wire:submit.prevent="deleteUser" class="space-y-4">
                <div class="form-control">
                    <label class="label" for="password">
                        <span class="label-text">{{ __('Password') }} <span class="text-error">*</span></span>
                    </label>
                    <input wire:model.defer="password" id="password" type="password"
                           class="input input-bordered w-full @error('password') input-error @enderror" placeholder="********" />
                        @error('password')
                            <span class="text-error text-sm mt-1">
                                Mohon Mengisi Password Dengan Benar, Apa Anda Yakin Ingin Menghapus Akun Anda ?
                            </span>
                        @enderror
                    {{-- <x-input-error :messages="$errors->get('password')" class="mt-1 text-error text-sm" /> --}}
                </div>

                <div class="modal-action">
                    <button type="button" class="btn btn-ghost" @click="showModal = false">
                        {{ __('Batal') }}
                    </button>
                    <button type="submit" class="btn btn-error">
                        {{ __('Hapus') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>





{{-- <section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Delete Account') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >{{ __('Delete Account') }}</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->isNotEmpty()" focusable>
        <form wire:submit="deleteUser" class="p-6">

            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Are you sure you want to delete your account?') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />

                <x-text-input
                    wire:model="password"
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-3/4"
                    placeholder="{{ __('Password') }}"
                />

                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-danger-button class="ms-3">
                    {{ __('Delete Account') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section> --}}
