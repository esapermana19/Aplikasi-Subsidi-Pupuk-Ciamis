<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ASUP Kabupaten Ciamis</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Custom Animations -->
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* Animasi Muncul (Fade In) */
        .fade-in-up {
            animation: fadeInUp 1s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
            transform: translateY(20px);
        }

        .fade-in-left {
            animation: fadeInLeft 1s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
            transform: translateX(-20px);
        }

        .fade-in-right {
            animation: fadeInRight 1s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
            transform: translateX(20px);
        }

        /* Delay Utilities */
        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }
        .delay-400 { animation-delay: 0.4s; }

        @keyframes fadeInUp {
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeInLeft {
            to { opacity: 1; transform: translateX(0); }
        }
        @keyframes fadeInRight {
            to { opacity: 1; transform: translateX(0); }
        }
        
        /* Transisi Halus untuk Indikator Slider */
        .dot-transition {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
    </style>
</head>
<body class="relative overflow-hidden text-gray-800 antialiased h-screen">

    <!-- FULL BACKGROUND IMAGE -->
    <div class="absolute inset-0 z-[-2]">
        <img src="{{ asset('assets/images/sawah3.jpg') }}" alt="Background Sawah" class="w-full h-full object-cover">
        
        <!-- Overlay Gradasi Hitam -->
        <div class="absolute inset-0 bg-black/60 sm:bg-transparent sm:bg-gradient-to-r sm:from-black/60 sm:via-black/45 sm:to-black/10"></div>
    </div>

    <!-- NAVBAR (Diperkecil) -->
    <nav class="fixed w-full z-50 transition-all duration-300 bg-black/20 backdrop-blur-md border-b border-white/10">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center gap-2 fade-in-left">
                    <div class="w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center shadow-lg shadow-green-500/30">
                        <i data-lucide="leaf" class="text-white w-4 h-4"></i>
                    </div>
                    <div>
                        <h1 class="text-lg font-bold text-white leading-tight">ASUP</h1>
                        <p class="text-[10px] font-semibold text-green-400 tracking-wider">KAB. CIAMIS</p>
                    </div>
                </div>

                <!-- Menu Button Masuk -->
                <div class="fade-in-right">
                    <a href="/login" class="group inline-flex items-center justify-center gap-2 px-5 py-2 text-xs font-semibold text-white bg-green-600 rounded-full hover:bg-green-700 transition-all duration-300 shadow-md shadow-green-600/30 hover:shadow-lg hover:-translate-y-0.5">
                        <i data-lucide="log-in" class="w-3.5 h-3.5 transition-transform group-hover:translate-x-1"></i>
                        Masuk
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- HERO SECTION (Dua Kolom) -->
    <main class="relative h-full flex items-center">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 w-full mt-10">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                
                <!-- Konten Kiri (Teks & Tombol) -->
                <div class="max-w-lg relative z-10">
                    <!-- Badge -->
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/10 border border-white/20 text-white text-xs font-medium mb-4 fade-in-up backdrop-blur-md">
                        <span class="flex w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
                        Portal Resmi Pertanian Ciamis
                    </div>
                    
                    <!-- Judul Utama -->
                    <h1 class="text-4xl md:text-5xl lg:text-5xl font-extrabold text-white leading-[1.2] mb-4 fade-in-up delay-100">
                        Distribusi Pupuk <br/>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-green-400 to-emerald-300">Lebih Transparan</span>
                    </h1>
                    
                    <!-- Deskripsi Singkat -->
                    <p class="text-sm text-gray-300 mb-8 leading-relaxed fade-in-up delay-200">
                        Aplikasi Subsidi Pupuk Pemerintah Kabupaten Ciamis hadir untuk memastikan setiap petani di Kabupaten Ciamis mendapatkan hak pupuk bersubsidi dengan mudah, merata, dan tepat sasaran.
                    </p>

                    <!-- Tombol Masuk Utama -->
                    <div class="flex fade-in-up delay-300">
                        <a href="/login" class="inline-flex items-center justify-center gap-2 px-6 py-3 text-sm font-bold text-white bg-green-600 rounded-xl hover:bg-green-700 transition-all duration-300 shadow-xl shadow-green-900/50 hover:shadow-2xl hover:-translate-y-1">
                            Masuk ke Sistem
                            <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </a>
                    </div>
                    
                    <!-- Info Tambahan Kecil (Stats) -->
                    <div class="mt-10 grid grid-cols-2 gap-4 pt-6 border-t border-white/20 fade-in-up delay-400">
                        <div>
                            <p class="text-2xl font-black text-white">100%</p>
                            <p class="text-xs font-medium text-gray-400 mt-1">Transparan & Aman</p>
                        </div>
                        <div>
                            <p class="text-2xl font-black text-white">24/7</p>
                            <p class="text-xs font-medium text-gray-400 mt-1">Akses Sistem Online</p>
                        </div>
                    </div>
                </div>

                <!-- Konten Kanan (Image Carousel/Slider Landscape) -->
                <div class="hidden lg:flex items-center justify-center relative w-full fade-in-right delay-300">
                    
                    <!-- Slider Container (Menggunakan aspect-video untuk Landscape 16:9) -->
                    <div class="relative w-full aspect-video rounded-3xl overflow-hidden shadow-2xl border border-white/10 group backdrop-blur-sm">
                        
                        <!-- Slide 1 -->
                        <div id="slide-0" class="absolute inset-0 transition-opacity duration-1000 ease-in-out opacity-100 z-10">
                            <img src="{{ asset('assets/images/sawah1.jpg') }}" alt="Petani Ciamis" class="w-full h-full object-cover">
                            <!-- Overlay Text -->
                            <div class="absolute bottom-0 left-0 right-0 p-6 bg-gradient-to-t from-black/90 via-black/50 to-transparent">
                                <h3 class="text-xl font-bold text-white mb-1">Panen Melimpah</h3>
                                <p class="text-gray-300 text-xs sm:text-sm">Dukungan pupuk yang tepat memastikan hasil panen petani Ciamis tetap maksimal.</p>
                            </div>
                        </div>

                        <!-- Slide 2 -->
                        <div id="slide-1" class="absolute inset-0 transition-opacity duration-1000 ease-in-out opacity-0 z-0">
                            <img src="{{ asset('assets/images/sawah5.jpg') }}" alt="Distribusi Pupuk" class="w-full h-full object-cover">
                            <div class="absolute bottom-0 left-0 right-0 p-6 bg-gradient-to-t from-black/90 via-black/50 to-transparent">
                                <h3 class="text-xl font-bold text-white mb-1">Distribusi Merata</h3>
                                <p class="text-gray-300 text-xs sm:text-sm">Penyaluran pupuk bersubsidi yang terdata rapi hingga ke pelosok desa.</p>
                            </div>
                        </div>

                        <!-- Slide 3 -->
                        <div id="slide-2" class="absolute inset-0 transition-opacity duration-1000 ease-in-out opacity-0 z-0">
                            <img src="{{ asset('assets/images/sawah4.jpg') }}" alt="Kualitas Pupuk" class="w-full h-full object-cover">
                            <div class="absolute bottom-0 left-0 right-0 p-6 bg-gradient-to-t from-black/90 via-black/50 to-transparent">
                                <h3 class="text-xl font-bold text-white mb-1">Stok Terjamin</h3>
                                <p class="text-gray-300 text-xs sm:text-sm">Pemantauan ketersediaan pupuk secara real-time antar Kios dan Pusat.</p>
                            </div>
                        </div>

                        <!-- Navigation Arrows (Muncul saat di-hover) -->
                        <button onclick="prevSlide()" class="absolute left-4 top-1/2 -translate-y-1/2 w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-white/20 hover:bg-white/40 backdrop-blur-md flex items-center justify-center border border-white/30 text-white opacity-0 group-hover:opacity-100 transition-all duration-300 z-20 hover:scale-110">
                            <i data-lucide="chevron-left" class="w-4 h-4 sm:w-5 sm:h-5"></i>
                        </button>
                        <button onclick="nextSlide()" class="absolute right-4 top-1/2 -translate-y-1/2 w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-white/20 hover:bg-white/40 backdrop-blur-md flex items-center justify-center border border-white/30 text-white opacity-0 group-hover:opacity-100 transition-all duration-300 z-20 hover:scale-110">
                            <i data-lucide="chevron-right" class="w-4 h-4 sm:w-5 sm:h-5"></i>
                        </button>

                        <!-- Indicator Dots -->
                        <div class="absolute top-4 right-4 flex items-center gap-2 z-20 bg-black/30 backdrop-blur-md px-3 py-1.5 rounded-full border border-white/10">
                            <button onclick="goToSlide(0)" id="dot-0" class="dot-transition w-5 h-1.5 rounded-full bg-green-400"></button>
                            <button onclick="goToSlide(1)" id="dot-1" class="dot-transition w-1.5 h-1.5 rounded-full bg-white/40 hover:bg-white/70"></button>
                            <button onclick="goToSlide(2)" id="dot-2" class="dot-transition w-1.5 h-1.5 rounded-full bg-white/40 hover:bg-white/70"></button>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </main>

    <!-- FOOTER (Nempel di bawah) -->
    <footer class="absolute bottom-0 w-full bg-black/40 backdrop-blur-md py-3 text-center fade-in-up delay-400 z-10 border-t border-white/10">
        <p class="text-[11px] font-medium text-gray-400 tracking-wide">
            &copy; 2026 Aplikasi Subsidi Kabupaten Ciamis. Dikembangkan untuk Kesejahteraan Petani.
        </p>
    </footer>

    <!-- Scripts untuk Icons dan Slider -->
    <script>
        // Initialize Icons
        lucide.createIcons();

        // Slider Logic
        let currentSlide = 0;
        const totalSlides = 3;
        let slideInterval;

        function updateSlider() {
            for (let i = 0; i < totalSlides; i++) {
                const slide = document.getElementById(`slide-${i}`);
                const dot = document.getElementById(`dot-${i}`);
                
                if (i === currentSlide) {
                    // Tampilkan slide aktif
                    slide.classList.replace('opacity-0', 'opacity-100');
                    slide.classList.replace('z-0', 'z-10');
                    
                    // Update dot aktif (lebih lebar & warna hijau)
                    dot.classList.replace('w-1.5', 'w-5');
                    dot.classList.replace('bg-white/40', 'bg-green-400');
                    dot.classList.remove('hover:bg-white/70');
                } else {
                    // Sembunyikan slide lainnya
                    slide.classList.replace('opacity-100', 'opacity-0');
                    slide.classList.replace('z-10', 'z-0');
                    
                    // Update dot tidak aktif
                    dot.classList.replace('w-5', 'w-1.5');
                    dot.classList.replace('bg-green-400', 'bg-white/40');
                    dot.classList.add('hover:bg-white/70');
                }
            }
        }

        function nextSlide() {
            currentSlide = (currentSlide + 1) % totalSlides;
            updateSlider();
            resetInterval();
        }

        function prevSlide() {
            currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
            updateSlider();
            resetInterval();
        }

        function goToSlide(index) {
            currentSlide = index;
            updateSlider();
            resetInterval();
        }

        function startInterval() {
            slideInterval = setInterval(nextSlide, 5000); // Ganti slide setiap 5 detik
        }

        function resetInterval() {
            clearInterval(slideInterval);
            startInterval();
        }

        // Mulai autoplay saat halaman dimuat
        startInterval();
    </script>
</body>
</html>