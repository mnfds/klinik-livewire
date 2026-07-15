<dialog id="modalEditShift" class="modal">
    <div class="modal-box max-w-sm">
        <h3 class="font-bold text-lg" id="modalTitle">Ubah Shift</h3>
        <p class="text-sm text-gray-500 mb-4" id="modalSubtitle">Nama - Tanggal</p>

        <div class="space-y-2">
            <button class="btn btn-outline btn-success w-full justify-start preset-btn" data-value="07.00 - 15.00">
                <span class="w-3 h-3 rounded-sm bg-green-100 inline-block"></span> Shift Pagi (07.00 - 15.00)
            </button>
            <button class="btn btn-outline btn-success w-full justify-start preset-btn" data-value="15.00 - 22.00">
                <span class="w-3 h-3 rounded-sm bg-green-100 inline-block"></span> Shift Siang (15.00 - 22.00)
            </button>
            <button class="btn btn-outline btn-success w-full justify-start preset-btn" data-value="22.00 - 07.00">
                <span class="w-3 h-3 rounded-sm bg-green-100 inline-block"></span> Shift Malam (22.00 - 07.00)
            </button>
            <button class="btn btn-outline btn-error w-full justify-start preset-btn" data-value="Libur">
                <span class="w-3 h-3 rounded-sm bg-red-100 inline-block"></span> Libur
            </button>

            <div class="divider text-xs my-1">atau custom jam</div>
            <div class="flex gap-2 items-center">
                <input type="time" id="customJamMulai" class="input input-bordered input-sm w-full" />
                <span class="text-gray-400 text-xs">s/d</span>
                <input type="time" id="customJamSelesai" class="input input-bordered input-sm w-full" />
            </div>
            <button id="btnCustomApply" class="btn btn-sm btn-primary w-full mt-1">Terapkan Jam Custom</button>
        </div>

        <div class="modal-action mt-4">
            <button id="btnCloseModal" class="btn btn-sm">Batal</button>
        </div>
    </div>
    <form method="dialog" class="modal-backdrop"><button>close</button></form>
</dialog>