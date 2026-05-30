<div class="mb-6">
     <h4 class="font-semibold mb-2 text-base-content">Surat Keterangan</h4>

     <div class="space-y-3">
         <div class="bg-base-100 border border-base-300 border-t-3 border-t-primary rounded-lg p-3 shadow-sm hover:shadow transition">
             {{-- Nama pelayanan --}}
             <div class="font-semibold text-base-content mb-1">
                Surat Keterangan {{ ucfirst($item->suratKeterangan->sakit ?? 'Sehat') }}
             </div>
    
             {{-- Harga --}}
             {{-- <div class="text-sm text-base-content/70">
                 {{ $item->jumlah_pelayanan ?? 0 }} x 
                 Rp {{ number_format($item->pelayanan->harga_pelayanan ?? 0, 0, ',', '.') }}
             </div> --}}
         </div>
     </div>
 </div>