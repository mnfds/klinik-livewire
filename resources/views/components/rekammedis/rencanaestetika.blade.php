<div class="bg-base-200 p-4 rounded border border-base-200"
    x-data="treatmentForm()"
    x-init="
        $watch('treatmentItems', () => {
            const total = calcTotal();
            window.dispatchEvent(
                new CustomEvent('total-treatment-updated', { detail: total })
            );
        })
    "
>
    @props([
        'rencanaEstetika' => [
            'treatments_id' => [],
            'jumlah_treatment' => [],
            'potongan' => [],
            'diskon' => [],
            'subtotal' => [],
        ],
        'layanandanbundling' => [
            'layanan' => [],
            'bundling' => [],
            'treatment' => [],
        ]
    ])

    <div class="divider">Rencana Tindakan Estetika</div>

    <!-- Tombol tambah -->
    <div class="mb-6">
        <button type="button"
            class="btn btn-primary btn-sm"
            @click="addTreatment">
            + Tambah Treatment
        </button>
    </div>

    <!-- List Treatment -->
    <div class="space-y-4">
        <template x-for="(item, index) in treatmentItems" :key="index">
            <div class="p-4 border rounded-lg bg-base-100 space-y-3">
                
                <!-- Baris 1: Treatment + Jumlah -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-end">
                    <div>
                        <label class="block text-sm font-semibold mb-1">Treatment</label>
                        <select class="select select-bordered w-full"
                            :name="`rencanaEstetika[treatments_id][${index}]`"
                            x-model="item.treatments_id"
                            @change="syncItemTreatment(index)"
                        >
                            <option value="">-- Pilih Treatment --</option>
                            @foreach($layanandanbundling['treatment'] as $Treatment)
                                <option value="{{ $Treatment['id'] }}">
                                    {{ $Treatment['nama_treatment'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">Jumlah</label>
                        <input type="number" min="1"
                            class="input input-bordered w-full"
                            x-model.number="item.jumlah_treatment"
                            @input="syncItemTreatment(index)"
                        >
                    </div>
                </div>

                <!-- Baris 2: Harga Asli + Potongan + Diskon + Subtotal -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">

                    <!-- Harga Asli -->
                    <div>
                        <label class="block text-sm font-semibold mb-1">Harga Asli</label>
                        <input type="text"
                            class="input input-bordered w-full bg-base-200"
                            :value="formatCurrency(calcHargaAsli(item))"
                            readonly
                        >
                    </div>

                    <!-- Potongan Harga -->
                    <div>
                        <label class="block text-sm font-semibold mb-1">Potongan (Rp)</label>
                        <input type="text"
                            class="input input-bordered w-full"
                            :value="formatCurrency(item.potongan)"
                            @input="e => updatePotongan(index, e.target.value)"
                        >
                    </div>

                    <!-- Diskon -->
                    <div>
                        <label class="block text-sm font-semibold mb-1">Diskon</label>
                        <div class="flex items-center">
                            <input type="number" min="0" max="100"
                                class="input input-bordered w-full"
                                x-model.number="item.diskon"
                                @input="updateDiskon(index, item.diskon)"
                            >
                            <span class="ml-2">%</span>
                        </div>
                    </div>

                    <!-- Subtotal -->
                    <div>
                        <label class="block text-sm font-semibold mb-1">Subtotal</label>
                        <input type="text"
                            class="input input-bordered w-full bg-base-200"
                            :value="formatCurrency(calcSubtotal(item))"
                            readonly
                        >
                    </div>
                </div>

                <!-- Tombol Hapus -->
                <div class="flex justify-end">
                    <button type="button"
                        class="btn btn-error btn-sm"
                        @click="removeTreatment(index)"
                        x-show="treatmentItems.length > 1">
                        Hapus
                    </button>
                </div>
            </div>
        </template>

        <!-- Footer Total -->
        <div class="flex justify-end mt-4 font-bold text-lg">
            <div class="mr-4">Total:</div>
            <div x-text="formatCurrency(calcTotal())"></div>
        </div>
    </div>
</div>

<script>
function treatmentForm() {
    return {
        // state
        treatmentItems: [{ treatments_id: '', jumlah_treatment: 1, potongan: 0, diskon: 0, subtotal: 0 }],
        treatments: @json($layanandanbundling['treatment']),

        // helpers
        getTreatment(id) {
            return this.treatments.find(t => t.id == id);
        },

        formatCurrency(value) {
            if (!value) return 'Rp 0';
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(value);
        },

        // update potongan
        updatePotongan(i, val) {
            let number = parseInt(val.replace(/[^\d]/g, '')) || 0;
            this.treatmentItems[i].potongan = number;
            this.syncItemTreatment(i);
        },

        // update diskon
        updateDiskon(i, val) {
            let number = parseInt(val) || 0;
            if (number < 0) number = 0;
            if (number > 100) number = 100;
            this.treatmentItems[i].diskon = number;
            this.syncItemTreatment(i);
        },

        // kalkulasi
        calcHargaAsli(item) {
            let treatment = this.getTreatment(item.treatments_id);
            let harga = treatment ? Number(treatment.harga_treatment) : 0;
            let jumlah = Number(item.jumlah_treatment) || 1;
            return harga * jumlah;
        },

        calcSubtotal(item) {
            let hargaAsli = this.calcHargaAsli(item);
            let potongan = Number(item.potongan) || 0;
            let diskon = Number(item.diskon) || 0;
            let afterPotongan = hargaAsli - potongan;
            if (afterPotongan < 0) afterPotongan = 0;
            return afterPotongan - (afterPotongan * (diskon / 100));
        },

        calcTotal() {
            return this.treatmentItems.reduce((total, item) => total + this.calcSubtotal(item), 0);
        },

        // aksi
        addTreatment() {
            this.treatmentItems.push({ treatments_id: '', jumlah_treatment: 1, potongan: 0, diskon: 0, subtotal: 0 });
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
            let subtotal = this.calcSubtotal(item);
            item.subtotal = subtotal;

            // Ganti $wire.set dengan @this.set
            @this.set(`rencana_estetika.treatments_id.${i}`, item.treatments_id);
            @this.set(`rencana_estetika.jumlah_treatment.${i}`, item.jumlah_treatment);
            @this.set(`rencana_estetika.potongan.${i}`, item.potongan);
            @this.set(`rencana_estetika.diskon.${i}`, item.diskon);
            @this.set(`rencana_estetika.subtotal.${i}`, item.subtotal);
        },

        syncTreatmentToLivewire() {
            this.treatmentItems.forEach((item, i) => this.syncItemTreatment(i));
        },

        cleanupTreatmentLivewire() {
            let length = this.treatmentItems.length;
            for (let i = length; i < 100; i++) {
                @this.set(`rencana_estetika.treatments_id.${i}`, null);
                @this.set(`rencana_estetika.jumlah_treatment.${i}`, null);
                @this.set(`rencana_estetika.potongan.${i}`, null);
                @this.set(`rencana_estetika.diskon.${i}`, null);
                @this.set(`rencana_estetika.subtotal.${i}`, null);
            }
        }
    }
}
</script>
