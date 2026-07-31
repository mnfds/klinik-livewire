<?php

use App\Models\User;
use App\Models\Role;
use App\Models\Biodata;
use App\Models\Dokter;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
    public string $nama_lengkap = '';
    public string $email = '';
    public $role_id = '';
    public string $jenis_kelamin = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function with(): array
    {
        return [
            'roles' => Role::whereNotIn('nama_role', ['Super Admin', 'Dokter'])
                            ->orderBy('nama_role')
                            ->get(),
        ];
    }

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name'          => ['required', 'string', 'max:255'],
            'nama_lengkap'  => ['required', 'string', 'max:255'],
            'role_id'       => ['required', 'exists:roles,id'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'email'         => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password'      => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $role = Role::findOrFail($validated['role_id']);
        $isDokter = strtolower($role->nama_role) === 'dokter';

        DB::transaction(function () use ($validated, $isDokter) {
            $user = User::create([
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role_id'  => $validated['role_id'],
            ]);

            $qrCode = $this->generateUserCodeQr($isDokter ? Dokter::class : Biodata::class);

            if ($isDokter) {
                Dokter::create([
                    'user_id'       => $user->id,
                    'nama_dokter'   => $validated['nama_lengkap'],
                    'jenis_kelamin' => $validated['jenis_kelamin'],
                    'user_code_qr'  => $qrCode,
                ]);
            } else {
                Biodata::create([
                    'user_id'       => $user->id,
                    'nama_lengkap'  => $validated['nama_lengkap'],
                    'jenis_kelamin' => $validated['jenis_kelamin'],
                    'user_code_qr'  => $qrCode,
                ]);
            }

            event(new Registered($user));

            Auth::login($user);
        });

        $this->redirect(route('verification.notice', absolute: false), navigate: true);
    }

    private function generateUserCodeQr(string $model): string
    {
        do {
            $letters = Str::upper(Str::random(6));
            $numbers = str_pad((string) rand(0, 999999), 6, '0', STR_PAD_LEFT);
            $code = $letters . $numbers;
        } while ($model::where('user_code_qr', $code)->exists());

        return $code;
    }
}; ?>

<div class="space-y-4">
    <form wire:submit="register" class="space-y-4">
        <!-- Name (username/login) -->
        <div class="form-control">
            <label for="name" class="label"><span class="label-text">Name</span></label>
            <input wire:model="name" id="name" name="name" type="text" autocomplete="name" required
                   class="input input-bordered w-full" />
            <x-input-error :messages="$errors->get('name')" class="mt-1 text-error text-sm" />
        </div>

        <!-- Nama Lengkap -->
        <div class="form-control">
            <label for="nama_lengkap" class="label"><span class="label-text">Nama Lengkap</span></label>
            <input wire:model="nama_lengkap" id="nama_lengkap" name="nama_lengkap" type="text"
                   class="input input-bordered w-full" />
            <x-input-error :messages="$errors->get('nama_lengkap')" class="mt-1 text-error text-sm" />
        </div>

        <!-- Role -->
        <div class="form-control">
            <label for="role_id" class="label"><span class="label-text">Role</span></label>
            <select wire:model="role_id" id="role_id" name="role_id" class="select select-bordered w-full">
                <option value="">-- Pilih Role --</option>
                @foreach ($roles as $role)
                    <option value="{{ $role->id }}">{{ $role->nama_role }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('role_id')" class="mt-1 text-error text-sm" />
        </div>

        <!-- Jenis Kelamin -->
        <div class="form-control">
            <label for="jenis_kelamin" class="label"><span class="label-text">Jenis Kelamin</span></label>
            <select wire:model="jenis_kelamin" id="jenis_kelamin" name="jenis_kelamin" class="select select-bordered w-full">
                <option value="">-- Pilih --</option>
                <option value="L">Laki-laki</option>
                <option value="P">Perempuan</option>
            </select>
            <x-input-error :messages="$errors->get('jenis_kelamin')" class="mt-1 text-error text-sm" />
        </div>

        <!-- Email -->
        <div class="form-control">
            <label for="email" class="label"><span class="label-text">Email</span></label>
            <input wire:model="email" id="email" name="email" type="email" autocomplete="username" required
                   class="input input-bordered w-full" />
            <x-input-error :messages="$errors->get('email')" class="mt-1 text-error text-sm" />
        </div>

        <!-- Password -->
        <div class="form-control">
            <label for="password" class="label"><span class="label-text">Password</span></label>
            <input wire:model="password" id="password" name="password" type="password" autocomplete="new-password" required
                   class="input input-bordered w-full" />
            <x-input-error :messages="$errors->get('password')" class="mt-1 text-error text-sm" />
        </div>

        <!-- Confirm Password -->
        <div class="form-control">
            <label for="password_confirmation" class="label"><span class="label-text">Confirm Password</span></label>
            <input wire:model="password_confirmation" id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required
                   class="input input-bordered w-full" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-error text-sm" />
        </div>

        <div class="flex flex-col sm:flex-row justify-between items-center gap-2 pt-2">
            <a href="{{ route('login') }}" wire:navigate
               class="text-sm text-neutral hover:underline focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-neutral">
                {{ __('Sudah Register?') }}
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
