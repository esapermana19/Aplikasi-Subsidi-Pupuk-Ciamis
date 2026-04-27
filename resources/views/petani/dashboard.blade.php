@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        {{-- Welcome Header --}}
        <div class="mb-6 flex justify-between items-end">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Hallo!! {{ $petani->nama_petani }},</h1>
                <p class="text-sm text-gray-500 mt-1">Selamat datang kembali! Kelola kebutuhan pupuk subsidi Anda di sini.</p>
            </div>
            <div class="hidden md:block text-right">
                <p class="text-xs font-bold text-green-600 bg-green-50 px-3 py-1 rounded-full border border-green-100">
                    {{ now()->translatedFormat('l, d F Y') }}
                </p>
            </div>
        </div>

        {{-- Interactive Carousel --}}
        <div class="relative w-full h-[200px] md:h-[300px] rounded-2xl overflow-hidden shadow-lg group">
            <!-- Carousel Container -->
            <div id="carousel" class="flex transition-transform duration-700 ease-in-out h-full">
                @foreach (range(1, 5) as $i)
                    <div class="min-w-full h-full relative">
                        <img src="{{ asset('assets/images/sawah' . $i . '.jpg') }}" class="w-full h-full object-cover"
                            alt="Sawah {{ $i }}">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>
                        <div class="absolute bottom-4 left-6 right-6 md:bottom-8 md:left-8 md:right-8 text-white">
                            <h2 class="text-lg md:text-3xl font-bold mb-1 md:mb-2 leading-tight">
                                @if ($i == 1)
                                    Penuhi Kebutuhan Pangan Bangsa
                                @elseif($i == 2)
                                    Gunakan Pupuk Subsidi Secara Bijak
                                @elseif($i == 3)
                                    Wujudkan Swasembada Pangan
                                @elseif($i == 4)
                                    Petani Sejahtera, Negara Kuat
                                @else
                                    Modernisasi Pertanian Ciamis
                                @endif
                            </h2>
                            <p class="text-[10px] md:text-sm text-gray-200 line-clamp-2 max-w-xl opacity-90">
                                @if ($i == 1)
                                    Mari bersama-sama menjaga ketahanan pangan nasional dengan meningkatkan produktivitas hasil tani di setiap musim.
                                @elseif($i == 2)
                                    Pastikan penggunaan pupuk sesuai dengan anjuran teknis untuk menjaga kesuburan tanah dan hasil panen yang optimal.
                                @elseif($i == 3)
                                    Dengan dukungan sistem digital, distribusi pupuk menjadi lebih transparan dan tepat sasaran bagi petani yang membutuhkan.
                                @elseif($i == 4)
                                    Dukungan penuh dari pemerintah daerah Kabupaten Ciamis untuk kemajuan dan kesejahteraan seluruh kelompok tani.
                                @else
                                    Aplikasi ASUP hadir untuk memudahkan petani Ciamis dalam mengakses informasi dan layanan pupuk subsidi secara praktis.
                                @endif
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Navigation Dots -->
            <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex space-x-2">
                @foreach (range(0, 4) as $i)
                    <button onclick="goToSlide({{ $i }})"
                        class="carousel-dot w-2 h-2 rounded-full bg-white/40 hover:bg-white transition-all"
                        id="dot-{{ $i }}"></button>
                @endforeach
            </div>

            <!-- Arrow Buttons -->
            <button onclick="prevSlide()"
                class="absolute left-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-black/20 hover:bg-black/40 text-white flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                <i data-lucide="chevron-left" class="w-6 h-6"></i>
            </button>
            <button onclick="nextSlide()"
                class="absolute right-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-black/20 hover:bg-black/40 text-white flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                <i data-lucide="chevron-right" class="w-6 h-6"></i>
            </button>
        </div>

        {{-- Kuota Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            {{-- Kuota Urea --}}
            <div class="group relative bg-gradient-to-br from-green-600 to-green-700 rounded-xl shadow-sm p-5 text-white hover:shadow-md transition-all duration-300 overflow-hidden">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <p class="text-green-100 text-xs font-semibold mb-0.5">Kuota Urea Tersisa</p>
                    </div>
                    <i data-lucide="leaf" class="h-5 w-5 text-green-200 opacity-60"></i>
                </div>
                <p class="text-2xl font-bold mb-3">150 <span class="text-sm text-green-100">Kg</span></p>
                <div class="w-full bg-green-500 bg-opacity-30 rounded-full h-1.5 overflow-hidden">
                    <div class="bg-green-100 h-full rounded-full" style="width: 75%"></div>
                </div>
                <p class="text-xs text-green-100 mt-2">Periode 2026</p>
            </div>

            {{-- Kuota NPK --}}
            <div class="group relative bg-gradient-to-br from-violet-500 to-violet-600 rounded-xl shadow-sm p-5 text-white hover:shadow-md transition-all duration-300 overflow-hidden">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <p class="text-violet-100 text-xs font-semibold mb-0.5">Kuota NPK Tersisa</p>
                    </div>
                    <i data-lucide="package" class="h-5 w-5 text-violet-200 opacity-60"></i>
                </div>
                <p class="text-2xl font-bold mb-3">75 <span class="text-sm text-violet-100">Kg</span></p>
                <div class="w-full bg-violet-400 bg-opacity-30 rounded-full h-1.5 overflow-hidden">
                    <div class="bg-violet-100 h-full rounded-full" style="width: 60%"></div>
                </div>
                <p class="text-xs text-violet-100 mt-2">Periode 2026</p>
            </div>

            {{-- Transaksi Terakhir --}}
            <div class="group relative bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-all duration-300 overflow-hidden">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <p class="text-gray-600 text-xs font-semibold mb-0.5">Transaksi Terakhir</p>
                    </div>
                    <i data-lucide="clock" class="h-5 w-5 text-orange-500 opacity-60"></i>
                </div>
                <p class="text-2xl font-bold text-gray-900 mb-1">Rp 450.000</p>
                <p class="text-xs text-gray-500">10 Apr 2026</p>
            </div>
        </div>

        {{-- Quick Actions & Info --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            {{-- Aksi Cepat --}}
            <div class="lg:col-span-1 space-y-3">
                <div class="flex flex-col">
                    <h3 class="text-sm font-bold text-gray-900">Aksi Cepat</h3>
                    <p class="text-[11px] text-gray-500 mt-0.5">Pilih aksi yang ingin Anda lakukan</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 gap-3">
                    <a href="{{ route('petani.beli_pupuk') }}"
                        class="bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-bold py-3 px-4 rounded-xl shadow-sm hover:shadow-md transition-all duration-300 flex items-center justify-center gap-2 group text-sm">
                        <i data-lucide="shopping-bag" class="h-5 w-5"></i>
                        <span>Beli Pupuk Sekarang</span>
                    </a>

                    <a href="{{ route('petani.riwayat_transaksi') }}"
                        class="bg-white border border-gray-200 text-green-700 hover:bg-green-50 font-bold py-3 px-4 rounded-xl transition-all duration-300 flex items-center justify-center gap-2 group text-sm">
                        <i data-lucide="history" class="h-5 w-5"></i>
                        <span>Lihat Riwayat</span>
                    </a>
                </div>
            </div>

            {{-- Informasi Penting --}}
            <div class="lg:col-span-2">
                <div class="bg-orange-50 border-l-4 border-orange-400 rounded-lg p-4 shadow-sm">
                    <div class="flex gap-3">
                        <i data-lucide="alert-circle" class="h-5 w-5 text-orange-600 flex-shrink-0 mt-0.5"></i>
                        <div>
                            <h3 class="font-semibold text-gray-900 text-sm mb-1">Informasi Penting</h3>
                            <p class="text-xs text-gray-700 mb-2">
                                <strong>Persiapan Musim Tanam:</strong> Pastikan Anda telah mengambil jatah pupuk subsidi sebelum tanggal 30 April 2026 untuk Musim Tanam I.
                            </p>
                            <p class="text-xs text-gray-600">
                                Pembelian pupuk kini dapat dibayar langsung dengan <strong class="text-green-700">QRIS</strong> untuk kemudahan transaksi di Mitra.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Transaksi Terbaru --}}
        <div>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-bold text-gray-900">Transaksi Terbaru</h2>
                <a href="{{ route('petani.riwayat_transaksi') }}" class="text-green-600 hover:text-green-700 font-semibold text-xs">
                    Lihat Semua →
                </a>
            </div>

            @if ($transaksiTerbaru->count() > 0)
                <div class="space-y-2">
                    @foreach ($transaksiTerbaru as $transaksi)
                        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4 hover:shadow-md hover:border-green-200 transition-all duration-300 group">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3">
                                        <div class="h-9 w-9 rounded-full bg-gradient-to-br from-green-400 to-green-600 flex items-center justify-center border border-green-200 shadow-sm flex-shrink-0">
                                            <i data-lucide="package" class="h-4 w-4 text-white"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h3 class="font-semibold text-gray-900 text-sm">{{ $transaksi->mitra->nama_mitra ?? 'Mitra' }}</h3>
                                            <p class="text-xs text-gray-500">
                                                {{ $transaksi->created_at->format('d M Y, H:i') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2">
                                    {{-- Status Badge --}}
                                    @if ($transaksi->status_pembayaran === 'pending')
                                        <span class="px-2 py-1 text-xs font-semibold bg-yellow-100 text-yellow-800 rounded-full border border-yellow-200">
                                            Pending
                                        </span>
                                    @elseif($transaksi->status_pembayaran === 'success')
                                        <span class="px-2 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded-full border border-green-200">
                                            Berhasil
                                        </span>
                                    @elseif($transaksi->status_pembayaran === 'failed')
                                        <span class="px-2 py-1 text-xs font-semibold bg-red-100 text-red-800 rounded-full border border-red-200">
                                            Gagal
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-semibold bg-gray-100 text-gray-800 rounded-full border border-gray-200">
                                            {{ ucfirst($transaksi->status_pembayaran) }}
                                        </span>
                                    @endif

                                    {{-- Detail Link --}}
                                    <a href="{{ route('petani.detail_transaksi', $transaksi->id_transaksi) }}"
                                        class="p-2 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded transition-all duration-300">
                                        <i data-lucide="arrow-right" class="h-4 w-4"></i>
                                    </a>
                                </div>
                            </div>
                            <p class="text-xs text-gray-600 mt-2">
                                {{ $transaksi->rincian->count() }} item •
                                <span class="font-semibold text-gray-900">Rp {{ number_format($transaksi->total, 0, ',', '.') }}</span>
                            </p>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-8 text-center">
                    <i data-lucide="inbox" class="h-12 w-12 text-gray-300 mx-auto mb-3"></i>
                    <p class="text-gray-500 text-sm font-semibold mb-1">Belum ada transaksi</p>
                    <p class="text-gray-400 text-xs mb-4">Mulai dengan membeli pupuk sekarang</p>
                    <a href="{{ route('petani.beli_pupuk') }}"
                        class="inline-block px-6 py-2 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-300 text-sm">
                        Beli Pupuk Sekarang
                    </a>
                </div>
            @endif
        </div>

        {{-- Profil Card --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
            <h3 class="text-sm font-bold text-gray-900 mb-4">Informasi Akun</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                <div>
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wide">Nama Lengkap</label>
                    <p class="text-gray-900 font-semibold text-sm mt-1">{{ $petani->nama_petani }}</p>
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wide">NIK</label>
                    <p class="text-gray-900 font-semibold text-sm mt-1">{{ $petani->nik }}</p>
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wide">No. KK</label>
                    <p class="text-gray-900 font-semibold text-sm mt-1">{{ $petani->no_kk }}</p>
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wide">Email</label>
                    <p class="text-gray-900 font-semibold text-sm mt-1">{{ Auth::user()->email }}</p>
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wide">Kecamatan</label>
                    <p class="text-gray-900 font-semibold text-sm mt-1">{{ $petani->kecamatan->nama_kecamatan ?? '-' }}</p>
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wide">Desa</label>
                    <p class="text-gray-900 font-semibold text-sm mt-1">{{ $petani->desa->nama_desa ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentSlide = 0;
        const totalSlides = 5;
        const carousel = document.getElementById('carousel');
        const dots = document.querySelectorAll('.carousel-dot');

        function updateCarousel() {
            carousel.style.transform = `translateX(-${currentSlide * 100}%)`;
            dots.forEach((dot, index) => {
                if (index === currentSlide) {
                    dot.classList.add('bg-white', 'w-6');
                    dot.classList.remove('bg-white/40', 'w-2');
                } else {
                    dot.classList.add('bg-white/40', 'w-2');
                    dot.classList.remove('bg-white', 'w-6');
                }
            });
        }

        function nextSlide() {
            currentSlide = (currentSlide + 1) % totalSlides;
            updateCarousel();
        }

        function prevSlide() {
            currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
            updateCarousel();
        }

        function goToSlide(index) {
            currentSlide = index;
            updateCarousel();
        }

        // Auto play
        let autoPlay = setInterval(nextSlide, 5000);

        // Pause auto play on hover
        const carouselContainer = carousel.parentElement;
        carouselContainer.addEventListener('mouseenter', () => clearInterval(autoPlay));
        carouselContainer.addEventListener('mouseleave', () => autoPlay = setInterval(nextSlide, 5000));

        document.addEventListener('DOMContentLoaded', function() {
            updateCarousel();
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
    </script>
@endsection
