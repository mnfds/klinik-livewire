<div class="pt-1 pb-12">
    <div class="max-w-full mx-auto sm:px-6 lg:px-8 space-y-6">
        <!-- Breadcrumbs (hanya muncul di layar lg ke atas) -->
        <div class="hidden lg:flex justify-end px-4">
            <div class="breadcrumbs text-sm">
                <ul>
                    <li>
                        <a href="{{  route('dashboard') }}" class="inline-flex items-center gap-1">
                            <i class="fa-regular fa-folder"></i>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('pelayanan.data') }}" class="inline-flex items-center gap-1">
                            <i class="fa-regular fa-folder-open"></i>
                            Pelayanan
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <!-- Page Title -->
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <h1 class="text-2xl font-bold text-base-content">
                <i class="fa-solid fa-layer-group"></i>
                Pelayanan Medis & Estetika
            </h1>
        </div>

        <!-- Main Content -->
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <!-- TABS -->
            <div class="tabs tabs-lift">

                <input type="radio" name="my_tabs_3" class="tab bg-transparent text-base-content" aria-label="Medis" style="background-image: none;" checked/>
                <div class="tab-content bg-base-100 border-base-300 p-6">
                    
                    <div class="bg-base-100 overflow-hidden shadow-xs rounded-sm sm:rounded-lg">
                        <div class="p-6 text-base-content space-y-4">
                            <div class="flex justify-between items-center mb-4">
                                <button onclick="document.getElementById('storeModalPelayanan').showModal()" class="btn btn-success"><i class="fa-solid fa-plus"></i>Layanan Medis</button>
                            </div>
                            <livewire:pelayanan-table/>
                        </div>
                    </div>

                </div>
                
                <input type="radio" name="my_tabs_3" class="tab bg-transparent text-base-content" aria-label="Estetika" style="background-image: none;"/>
                <div class="tab-content bg-base-100 border-base-300 p-6">

                    <div class="bg-base-100 overflow-hidden shadow-xs rounded-sm sm:rounded-lg">
                        <div class="p-6 text-base-content space-y-4">
                            <div class="flex justify-between items-center mb-4">
                                <button onclick="document.getElementById('storeModalPelayananEstetika').showModal()" class="btn btn-success"><i class="fa-solid fa-plus"></i>Layanan Estetika</button>
                            </div>
                            <livewire:treatment-table/>
                        </div>
                    </div>

                </div>
                
            </div>

        </div>
    </div>
</div>