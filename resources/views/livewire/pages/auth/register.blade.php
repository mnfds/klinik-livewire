<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered($user = User::create($validated)));

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="space-y-4">
    <form wire:submit="register" class="space-y-4">
        <!-- Name -->
        <div class="form-control">
            <label for="name" class="label">
                <span class="label-text">Name</span>
            </label>
            <input wire:model="name" id="name" name="name" type="text" autocomplete="name" required
                   class="input input-bordered w-full" />
            <x-input-error :messages="$errors->get('name')" class="mt-1 text-error text-sm" />
        </div>

        <!-- Email -->
        <div class="form-control">
            <label for="email" class="label">
                <span class="label-text">Email</span>
            </label>
            <input wire:model="email" id="email" name="email" type="email" autocomplete="username" required
                   class="input input-bordered w-full" />
            <x-input-error :messages="$errors->get('email')" class="mt-1 text-error text-sm" />
        </div>

        <!-- Password -->
        <div class="form-control">
            <label for="password" class="label">
                <span class="label-text">Password</span>
            </label>
            <input wire:model="password" id="password" name="password" type="password" autocomplete="new-password" required
                   class="input input-bordered w-full" />
            <x-input-error :messages="$errors->get('password')" class="mt-1 text-error text-sm" />
        </div>

        <!-- Confirm Password -->
        <div class="form-control">
            <label for="password_confirmation" class="label">
                <span class="label-text">Confirm Password</span>
            </label>
            <input wire:model="password_confirmation" id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required
                   class="input input-bordered w-full" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-error text-sm" />
        </div>

        <!-- Actions -->
        <div class="flex flex-col sm:flex-row justify-between items-center gap-2 pt-2">
            <a href="{{ route('login') }}" wire:navigate
               class="text-sm text-neutral hover:underline focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-neutral">
                {{ __('Already registered?') }}
            </a>

            <button type="submit" class="btn btn-accent w-full sm:w-auto">
                {{ __('Register') }}
            </button>
        </div>
    </form>
</div>

{{-- <div>
    <form wire:submit="register">
        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input wire:model="name" id="name" class="block mt-1 w-full" type="text" name="name" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input wire:model="email" id="email" class="block mt-1 w-full" type="email" name="email" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input wire:model="password" id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input wire:model="password_confirmation" id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-hidden focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}" wire:navigate>
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</div> --}}
