<div class="p-4 h-screen overflow-hidden flex flex-col">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 flex-1 overflow-hidden">
        {{-- Kolom Antrian (2/3 lebar) --}}
        <div class="lg:col-span-2 overflow-y-auto grid grid-cols-1 md:grid-cols-2 gap-4 content-start">
            @foreach ($poli as $item)
                <div class="bg-white rounded-xl shadow p-4 border border-gray-100">
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="text-lg font-bold text-gray-800 truncate">{{ $item->nama_poli }}</h2>
                        <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full flex-shrink-0">Aktif</span>
                    </div>
                    @if ($item->antrians->isNotEmpty())
                        <div class="text-center bg-blue-50 rounded-lg py-4 mb-3">
                            <p class="text-xl font-bold text-blue-600 truncate px-2">
                                {{ $item->antrians->first()->nama_pengantri }}
                            </p>
                            <p class="text-xs text-gray-500 mb-1">Sedang Dilayani</p>
                        </div>
                        @if ($item->antrians->count() > 1)
                            <p class="text-xs text-gray-400 mb-1">Berikutnya:</p>
                            <div class="flex gap-2 flex-wrap">
                                @php $i = 2; @endphp
                                @foreach ($item->antrians->skip(1) as $antrian)
                                    <span class="bg-gray-100 text-gray-600 text-sm px-3 py-1 rounded-full font-medium">
                                        {{ $i++ }}. {{ $antrian->nama_pengantri }} ({{ $antrian->kode }}-{{ $antrian->nomor_antrian }})
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    @else
                        <div class="text-center bg-blue-50 rounded-lg py-4 mb-3">
                            <div class="text-center py-6 text-gray-400">
                                <p class="text-sm">Belum ada antrian dipanggil</p>
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
        {{-- Kolom Promosi (1/3 lebar) --}}
        <div class="lg:col-span-1 relative overflow-hidden rounded-xl h-full min-h-64">
            @php
                $promosi = [
                    'https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?w=800',
                    'https://images.unsplash.com/photo-1631248207065-771ae9ac32f0?q=80&w=387&auto=format&fit=crop',
                    'https://plus.unsplash.com/premium_photo-1661963997077-3c3529aa407f?q=80&w=774&auto=format&fit=crop',
                    'https://images.unsplash.com/photo-1505751172876-fa1923c5c528?w=800',
                ];
            @endphp
            @foreach ($promosi as $index => $gambar)
                <div class="promosi-slide absolute inset-0 transition-opacity duration-1000 opacity-0"
                    data-index="{{ $index }}">
                    <img src="{{ $gambar }}" class="w-full h-full object-cover" />
                </div>
            @endforeach
            <div class="absolute bottom-3 left-1/2 -translate-x-1/2 flex gap-2 z-10">
                @foreach ($promosi as $index => $gambar)
                    <div class="promosi-dot w-2 h-2 rounded-full bg-white/40 transition-all duration-300" data-index="{{ $index }}"></div>
                @endforeach
            </div>
        </div>
    </div>
    @script
    <script>
        const slides = document.querySelectorAll('.promosi-slide');
        const dots = document.querySelectorAll('.promosi-dot');
        let current = 0;

        function showSlide(index) {
            slides.forEach(s => s.classList.replace('opacity-100', 'opacity-0'));
            dots.forEach(d => {
                d.classList.remove('bg-white', 'scale-125');
                d.classList.add('bg-white/40');
            });
            slides[index].classList.replace('opacity-0', 'opacity-100');
            dots[index].classList.remove('bg-white/40');
            dots[index].classList.add('bg-white', 'scale-125');
        }
        showSlide(0);
        // Slideshow timer
        setInterval(() => {
            current = (current + 1) % slides.length;
            showSlide(current);
        }, 5000);

        // Refresh antrian
        setInterval(() => {
            $wire.loadAntrian();
        }, 10000);
    </script>
    @endscript

</div>