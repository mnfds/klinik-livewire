<?php

use App\Models\User;
use App\Models\Role;
use App\Models\Biodata;
use App\Models\Dokter;
use Illuminate\Auth\Events\Registered;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    use WithFileUploads;

    // Data Akun
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    // Data Biodata
    public string $nama_lengkap = '';
    public $role_id = '';
    public string $jenis_kelamin = '';
    public string $nik = '';
    public string $ihs = '';
    public string $alamat = '';
    public string $telepon = '';
    public string $tempat_lahir = '';
    public string $tanggal_lahir = '';
    public string $nama_kerabat = '';
    public string $telepon_kerabat = '';
    public string $status_kerabat = '';
    public $foto_wajah = null;

    public function with(): array
    {
        return [
            'roles' => Role::whereNotIn('nama_role', ['Super Admin', 'Dokter'])
                            ->orderBy('nama_role')
                            ->get(),
        ];
    }

    public function register(): void
    {
        $validated = $this->validate([
            // Data Akun
            'name'          => ['required', 'string', 'max:255', 'unique:users,name'],
            'email'         => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password'      => ['required', 'string', 'confirmed', Rules\Password::defaults()],

            // Data Biodata
            'nama_lengkap'    => ['required', 'string', 'max:255'],
            'role_id'         => ['required', 'exists:roles,id'],
            'jenis_kelamin'   => ['required', 'in:L,P'],
            'nik'             => ['nullable', 'string', 'max:20'],
            'ihs'             => ['nullable', 'string', 'max:50'],
            'alamat'          => ['nullable', 'string', 'max:255'],
            'telepon'         => ['nullable', 'string', 'max:20'],
            'tempat_lahir'    => ['nullable', 'string', 'max:100'],
            'tanggal_lahir'   => ['required', 'date'],
            'nama_kerabat'    => ['nullable', 'string', 'max:255'],
            'telepon_kerabat' => ['nullable', 'string', 'max:20'],
            'status_kerabat'  => ['nullable', 'string', 'max:50'],
            'foto_wajah'      => ['nullable', 'image', 'max:2048'],
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

            $fotoPath = $this->foto_wajah
                ? $this->foto_wajah->store('foto_wajah', 'public')
                : null;

            $biodataPayload = [
                'user_id'         => $user->id,
                'nama_lengkap'    => $validated['nama_lengkap'],
                'jenis_kelamin'   => $validated['jenis_kelamin'],
                'nik'             => $validated['nik'] ?? null,
                'ihs'             => $validated['ihs'] ?? null,
                'alamat'          => $validated['alamat'] ?? null,
                'telepon'         => $validated['telepon'] ?? null,
                'tempat_lahir'    => $validated['tempat_lahir'] ?? null,
                'tanggal_lahir'   => $validated['tanggal_lahir'] ?? null,
                'nama_kerabat'    => $validated['nama_kerabat'] ?? null,
                'telepon_kerabat' => $validated['telepon_kerabat'] ?? null,
                'status_kerabat'  => $validated['status_kerabat'] ?? null,
                'foto_wajah'      => $fotoPath,
                'user_code_qr'    => $qrCode,
            ];

            if ($isDokter) {
                Dokter::create([
                    'user_id'       => $user->id,
                    'nama_dokter'   => $validated['nama_lengkap'],
                    'jenis_kelamin' => $validated['jenis_kelamin'],
                    'user_code_qr'  => $qrCode,
                ]);
            } else {
                Biodata::create($biodataPayload);
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

<div class="pt-1 pb-12">
    <div class="max-w-full mx-auto sm:px-6 lg:px-8 space-y-6 bg-base-100 rounded-2xl">
        <form wire:submit="register" class="space-y-6">

            {{-- SECTION 1: DATA AKUN --}}
            <fieldset class="fieldset bg-base-300 border border-base-300 rounded-box p-4">
                <legend class="fieldset-legend text-base font-semibold px-2">Data Akun</legend>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="form-control">
                        <label for="name" class="label"><span class="label-text">Username <span class="text-error">*</span></span></label>
                        <input wire:model="name" id="name" name="name" type="text" autocomplete="name" required
                            class="input input-bordered w-full" />
                        <x-input-error :messages="$errors->get('name')" class="mt-1 text-error text-sm" />
                    </div>

                    <div class="form-control">
                        <label for="email" class="label"><span class="label-text">Email <span class="text-error">*</span></span></label>
                        <input wire:model="email" id="email" name="email" type="email" autocomplete="username" required
                            class="input input-bordered w-full" />
                        <x-input-error :messages="$errors->get('email')" class="mt-1 text-error text-sm" />
                    </div>

                    <div class="form-control" x-data="{ showPassword: false }">
                        <label for="password" class="label"><span class="label-text">Password <span class="text-error">*</span></span></label>
                        <div class="relative">
                            <input wire:model="password" id="password" name="password"
                                :type="showPassword ? 'text' : 'password'" autocomplete="new-password" required
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
                        <x-input-error :messages="$errors->get('password')" class="mt-1 text-error text-sm" />
                    </div>

                    <div class="form-control" x-data="{ showPasswordConfirmation: false }">
                        <label for="password_confirmation" class="label"><span class="label-text">Confirm Password <span class="text-error">*</span></span></label>
                        <div class="relative">
                            <input wire:model="password_confirmation" id="password_confirmation" name="password_confirmation"
                                :type="showPasswordConfirmation ? 'text' : 'password'" autocomplete="new-password" required
                                class="input input-bordered w-full pr-10" />
                            <button type="button"
                                    @click="showPasswordConfirmation = !showPasswordConfirmation"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-base-content/50 hover:text-base-content"
                                    tabindex="-1">
                                <svg x-show="!showPasswordConfirmation" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <svg x-show="showPasswordConfirmation" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                </svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-error text-sm" />
                    </div>
                </div>
            </fieldset>

            {{-- SECTION 2: DATA BIODATA --}}
            <fieldset class="fieldset bg-base-300 border border-base-300 rounded-box p-4">
                <legend class="fieldset-legend text-base font-semibold px-2">Data Biodata</legend>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="form-control sm:col-span-2 lg:col-span-3">
                        <label for="nama_lengkap" class="label"><span class="label-text">Nama Lengkap <span class="text-error">*</span></span></label>
                        <input wire:model="nama_lengkap" id="nama_lengkap" name="nama_lengkap" type="text"
                            class="input input-bordered w-full" />
                        <x-input-error :messages="$errors->get('nama_lengkap')" class="mt-1 text-error text-sm" />
                    </div>

                    <div class="form-control">
                        <label for="role_id" class="label"><span class="label-text">Role <span class="text-error">*</span></span></label>
                        <select wire:model="role_id" id="role_id" name="role_id" class="select select-bordered w-full">
                            <option value="">-- Pilih Role --</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->nama_role }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('role_id')" class="mt-1 text-error text-sm" />
                    </div>

                    <div class="form-control">
                        <label for="jenis_kelamin" class="label"><span class="label-text">Jenis Kelamin <span class="text-error">*</span></span></label>
                        <select wire:model="jenis_kelamin" id="jenis_kelamin" name="jenis_kelamin" class="select select-bordered w-full">
                            <option value="">-- Pilih --</option>
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                        <x-input-error :messages="$errors->get('jenis_kelamin')" class="mt-1 text-error text-sm" />
                    </div>

                    <div class="form-control">
                        <label for="nik" class="label"><span class="label-text">NIK</span></label>
                        <input wire:model="nik" id="nik" type="text" inputmode="numeric" pattern="[0-9]*" class="input input-bordered w-full" />
                        <x-input-error :messages="$errors->get('nik')" class="mt-1 text-error text-sm" />
                    </div>

                    <div class="form-control">
                        <label for="ihs" class="label"><span class="label-text">IHS</span></label>
                        <input wire:model="ihs" id="ihs" type="text" class="input input-bordered w-full" />
                        <x-input-error :messages="$errors->get('ihs')" class="mt-1 text-error text-sm" />
                    </div>

                    <div class="form-control sm:col-span-2">
                        <label for="alamat" class="label"><span class="label-text">Alamat</span></label>
                        <input wire:model="alamat" id="alamat" type="text" class="input input-bordered w-full" />
                        <x-input-error :messages="$errors->get('alamat')" class="mt-1 text-error text-sm" />
                    </div>

                    <div class="form-control">
                        <label for="telepon" class="label"><span class="label-text">Telepon</span></label>
                        <input wire:model="telepon" id="telepon" type="text" inputmode="numeric" pattern="[0-9]*" class="input input-bordered w-full" />
                        <x-input-error :messages="$errors->get('telepon')" class="mt-1 text-error text-sm" />
                    </div>

                    <div class="form-control">
                        <label for="tempat_lahir" class="label"><span class="label-text">Tempat Lahir</span></label>
                        <input wire:model="tempat_lahir" id="tempat_lahir" type="text" class="input input-bordered w-full" />
                        <x-input-error :messages="$errors->get('tempat_lahir')" class="mt-1 text-error text-sm" />
                    </div>

                    <div class="form-control">
                        <label for="tanggal_lahir" class="label"><span class="label-text">Tanggal Lahir <span class="text-error">*</span></span></label>
                        <input wire:model="tanggal_lahir" id="tanggal_lahir" type="date" class="input input-bordered w-full" />
                        <x-input-error :messages="$errors->get('tanggal_lahir')" class="mt-1 text-error text-sm" />
                    </div>

                    <div class="form-control">
                        <label for="nama_kerabat" class="label"><span class="label-text">Nama Kerabat</span></label>
                        <input wire:model="nama_kerabat" id="nama_kerabat" type="text" class="input input-bordered w-full" />
                        <x-input-error :messages="$errors->get('nama_kerabat')" class="mt-1 text-error text-sm" />
                    </div>

                    <div class="form-control">
                        <label for="telepon_kerabat" class="label"><span class="label-text">Telepon Kerabat</span></label>
                        <input wire:model="telepon_kerabat" id="telepon_kerabat" type="text" inputmode="numeric" pattern="[0-9]*" class="input input-bordered w-full" />
                        <x-input-error :messages="$errors->get('telepon_kerabat')" class="mt-1 text-error text-sm" />
                    </div>

                    <div class="form-control">
                        <label for="status_kerabat" class="label"><span class="label-text">Status Kerabat</span></label>
                        <select wire:model="status_kerabat" id="status_kerabat" class="select select-bordered w-full">
                            <option value="">-- Pilih Status Kerabat --</option>
                            <option value="ayah">Ayah</option>
                            <option value="ibu">Ibu</option>
                            <option value="kakak">Kakak</option>
                            <option value="adik">Adik</option>
                            <option value="suami">Suami</option>
                            <option value="istri">Istri</option>
                            <option value="paman">Paman</option>
                            <option value="bibi">Bibi</option>
                            <option value="kakek">Kakek</option>
                            <option value="nenek">Nenek</option>
                            <option value="teman">Teman</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                        <x-input-error :messages="$errors->get('status_kerabat')" class="mt-1 text-error text-sm" />
                    </div>

                    <div class="form-control sm:col-span-2 lg:col-span-3">
                        <label for="foto_wajah" class="label"><span class="label-text">Unggah Foto</span></label>
                        <input type="file" id="foto_wajah" wire:model="foto_wajah" class="file-input file-input-bordered w-full" />
                        <div class="mt-2 text-sm text-gray-500 flex items-center gap-2" wire:loading wire:target="foto_wajah">
                            <span class="loading loading-spinner loading-sm text-info"></span>
                            <span>Mengunggah foto...</span>
                        </div>
                        @if ($foto_wajah)
                            <img src="{{ $foto_wajah->temporaryUrl() }}" alt="Preview Foto" class="w-32 h-32 mt-2 rounded border object-cover" />
                        @endif
                        <x-input-error :messages="$errors->get('foto_wajah')" class="mt-1 text-error text-sm" />
                    </div>
                </div>
            </fieldset>

            <div class="flex flex-col sm:flex-row justify-between items-center gap-2 py-2">
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
