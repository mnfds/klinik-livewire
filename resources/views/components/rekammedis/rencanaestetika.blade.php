<div class="bg-base-200 p-4 rounded border border-base-200"
    x-data="{
        treatmentItems: [{ treatments_id: '', jumlah_treatment: 1 }],

        addTreatment() {
            this.treatmentItems.push({ treatments_id: '', jumlah_treatment: 1 });
            this.syncTreatmentToLivewire();
        },
        removeTreatment(index) {
            this.treatmentItems.splice(index, 1);
            this.reindexTreatment();
            this.syncTreatmentToLivewire();
            this.cleanupTreatmentLivewire();
        },
        reindexTreatment() {
            this.treatmentItems = this.treatmentItems.map(item => ({ ...item }));
        },
        syncItemTreatment(i) {
            let item = this.treatmentItems[i];
            $wire.set(`rencana_estetika.treatments_id.${i}`, item.treatments_id);
            $wire.set(`rencana_estetika.jumlah_treatment.${i}`, item.jumlah_treatment);
        },
        syncTreatmentToLivewire() {
            this.treatmentItems.forEach((item, i) => this.syncItemTreatment(i));
        },
        cleanupTreatmentLivewire() {
            let length = this.treatmentItems.length;
            for (let i = length; i < 100; i++) {
                $wire.set(`rencana_estetika.treatments_id.${i}`, null);
                $wire.set(`rencana_estetika.jumlah_treatment.${i}`, null);
            }
        },
    }"
>
    @props([
        'rencanaEstetika' => [
            'treatments_id' => [],
            'jumlah_treatment' => [],
        ],
        'layanandanbundling' => [
            'layanan' => [],
            'bundling' => [],
        ]
    ])

    <div class="divider">Rencana Tindakan Estetika</div>

    {{-- SECTION LAYANAN --}}
    <div class="mb-6">
        <div class="flex items-center mb-2">
            <button type="button" class="btn btn-primary btn-sm mx-1" @click="addTreatment">
                + Tambah Treatment
            </button>
        </div>

        {{-- Section Layanan --}}
        <div class="mb-4 border p-4 rounded-lg bg-base-100">
            <!-- Header -->
            <div class="flex font-semibold mb-2">
                <div class="flex-1">Pilih Treatment</div>
                <div class="w-32">Jumlah</div>
                <div class="w-20"></div> {{-- untuk kolom tombol hapus --}}
            </div>

            <!-- Rows -->
            <template x-for="(item, index) in treatmentItems" :key="index">
                <div class="flex items-center gap-4 mb-2">
                    <select class="select select-bordered flex-1"
                        :name="`rencanaEstetika[treatments_id][${index}]`"
                        x-model="item.treatments_id"
                        @change="syncItemTreatment(index)"
                    >
                        <option value="">-- Pilih Treatment --</option>
                        @foreach($layanandanbundling['treatment'] as $Treatment)
                            <option value="{{ $Treatment['id'] }}">{{ $Treatment['nama_treatment'] }}</option>
                        @endforeach
                    </select>

                    <input type="number" min="1"
                        class="input input-bordered w-32"
                        :name="`rencanaEstetika[jumlah_treatment][${index}]`"
                        x-model.number="item.jumlah_treatment"
                        @input="syncItemTreatment(index)"
                    >

                    <button type="button"
                        class="btn btn-error btn-sm w-20"
                        @click="removeTreatment(index)"
                        x-show="treatmentItems.length > 1">
                        Hapus
                    </button>
                </div>
            </template>
        </div>
    </div>

</div>
