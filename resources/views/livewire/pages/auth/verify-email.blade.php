<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    /**
     * Send an email verification notification to the user.
     */
    public function sendVerification(): void
    {
        if (Auth::user()->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);

            return;
        }

        Auth::user()->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<div class="flex items-center justify-center py-8">
    <div class="w-full max-w-md">
        <div class="bg-base-100 rounded-box shadow-sm border border-base-300 p-6 sm:p-8 space-y-5">

            <div class="flex justify-center">
                <div class="rounded-full bg-primary/10 p-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                    </svg>
                </div>
            </div>

            <div class="text-center space-y-1">
                <h2 class="text-lg font-semibold text-base-content">
                    {{ __('Verifikasi Alamat Email') }}
                </h2>
                <p class="text-sm text-base-content/70">
                    {{ __('Terima kasih telah mendaftar! Sebelum memulai, mohon verifikasi alamat email kamu dengan mengklik tautan yang baru saja kami kirimkan. Jika kamu belum menerima emailnya, kami akan dengan senang hati mengirimkannya lagi.') }}
                </p>
            </div>

            @if (session('status') == 'verification-link-sent')
                <div class="alert alert-success text-sm font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ __('Tautan verifikasi baru telah dikirim ke alamat email yang kamu daftarkan.') }}</span>
                </div>
            @endif

            <div class="flex flex-col sm:flex-row justify-between items-center gap-3 pt-2">
                <button wire:click="sendVerification" class="btn btn-primary w-full sm:w-auto">
                    {{ __('Kirim Ulang Email Verifikasi') }}
                </button>

                <button wire:click="logout"
                        type="submit"
                        class="text-sm text-neutral underline hover:text-neutral-focus focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-neutral">
                    {{ __('Keluar') }}
                </button>
            </div>

        </div>
    </div>
</div>

{{-- <div>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">
        <x-primary-button wire:click="sendVerification">
            {{ __('Resend Verification Email') }}
        </x-primary-button>

        <button wire:click="logout" type="submit" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-hidden focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            {{ __('Log Out') }}
        </button>
    </div>
</div> --}}
