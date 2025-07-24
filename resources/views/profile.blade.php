<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-base-content">
            {{ __('Profil Pengguna') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 space-y-6">
            <!-- Update Profile Info -->
            <div class="p-6 bg-base-100 rounded-box shadow">
                <div class="max-w-full">
                    <livewire:profile.update-profile-information-form />
                </div>
            </div>

            <div class="p-6 bg-base-100 rounded-box shadow">
                <div class="max-w-full">
                    @if (auth()->check() && auth()->user()->role->nama_role === 'dokter')
                        <livewire:biodata.dokter />
                    @else
                        <livewire:biodata.manage-biodata />
                    @endif
                </div>
            </div>

            <!-- Update Password -->
            <div class="p-6 bg-base-100 rounded-box shadow">
                <div class="max-w-full">
                    <livewire:profile.update-password-form />
                </div>
            </div>

            <!-- Delete Account -->
            <div class="p-6 bg-base-100 rounded-box shadow">
                <div class="max-w-2xl">
                    <livewire:profile.delete-user-form />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
