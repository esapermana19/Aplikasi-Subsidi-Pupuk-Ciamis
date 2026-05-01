@extends('layouts.app')

@section('content')
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-green-700">Beli Pupuk</h2>
        <p class="text-sm text-gray-500 mt-1">Pilih mitra terdekat dan beli pupuk subsidi secara digital.</p>
    </div>

    <div class="flex items-center gap-3 mb-6">
        <div id="ind-1"
            class="w-8 h-8 rounded-full bg-green-600 text-white flex items-center justify-center font-bold text-sm">1</div>
        <i data-lucide="arrow-right" class="w-4 h-4 text-gray-400"></i>
        <div id="ind-2"
            class="w-8 h-8 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center font-bold text-sm transition-colors">
            2</div>
        <i data-lucide="arrow-right" class="w-4 h-4 text-gray-400"></i>
        <div id="ind-3"
            class="w-8 h-8 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center font-bold text-sm transition-colors">
            3</div>
        <i data-lucide="arrow-right" class="w-4 h-4 text-gray-400"></i>
        <div id="ind-4"
            class="w-8 h-8 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center font-bold text-sm transition-colors">
            4</div>
    </div>

    <div id="step-1" class="bg-white border border-green-500 rounded-xl p-5 shadow-sm block">
        <h3 class="font-bold text-gray-800 mb-1">Langkah 1: Pilih Mitra / Kios</h3>
        <p class="text-[13px] text-gray-500 mb-4">Filter berdasarkan wilayah Anda untuk menemukan kios terdekat.</p>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end mb-5">
            <div>
                <label class="text-xs font-semibold text-gray-700 mb-1 block">Kecamatan</label>
                <select id="kecamatan-filter"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-1 focus:ring-green-500 outline-none bg-gray-50">
                    <option value="">-- Semua Kecamatan --</option>
                    @foreach ($kecamatans as $kecamatan)
                        <option value="{{ $kecamatan->id_kecamatan }}">{{ $kecamatan->nama_kecamatan }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-700 mb-1 block">Desa / Kelurahan</label>
                <select id="desa-filter"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-1 focus:ring-green-500 outline-none bg-gray-50">
                    <option value="">-- Semua Desa --</option>
                    @foreach ($desas as $desa)
                        <option value="{{ $desa->id_desa }}" data-kecamatan="{{ $desa->id_kecamatan }}"
                            style="display: none;">{{ $desa->nama_desa }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <button id="btn-reset-filter" type="button" onclick="resetFilter()"
                    class="hidden bg-red-50 border border-red-200 hover:bg-red-100 text-red-600 font-medium py-2 px-4 rounded-lg transition-colors text-sm flex items-center justify-center gap-2">
                    <i data-lucide="x" class="w-4 h-4"></i> Reset
                </button>
            </div>
        </div>

        <p class="text-xs text-gray-500 mb-3">Mitra Tersedia di Wilayah Ini:</p>

        <div id="mitras-container" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
            @forelse($mitras as $mitra)
                <div class="mitra-card border-2 border-gray-300 bg-white rounded-lg p-4 cursor-pointer transition-all hover:shadow-md"
                    data-id="{{ $mitra->id_mitra }}" data-kecamatan="{{ $mitra->id_kecamatan }}"
                    data-desa="{{ $mitra->id_desa }}"
                    onclick="selectMitra({{ $mitra->id_mitra }}, '{{ addslashes($mitra->nama_mitra) }}', this)">
                    <div class="flex flex-col h-full">
                        <div class="flex items-start gap-2 mb-3">
                            <i data-lucide="store" class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5"></i>
                            <h4 class="font-bold text-gray-800 text-sm line-clamp-2">{{ $mitra->nama_mitra }}</h4>
                        </div>
                        <p class="text-[11px] text-gray-500 flex items-start gap-1 mb-2 flex-grow">
                            <i data-lucide="map-pin" class="w-3 h-3 flex-shrink-0 mt-0.5"></i>
                            <span class="line-clamp-2">{{ $mitra->alamat_mitra }}</span>
                        </p>
                        <div class="text-[10px] text-gray-600 border-t border-gray-100 pt-2 mt-auto">
                            <span class="font-semibold">{{ $mitra->kecamatan->nama_kecamatan ?? '-' }}</span> /
                            <span>{{ $mitra->desa->nama_desa ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-2 lg:col-span-3 text-center py-8">
                    <p class="text-gray-500 text-sm">Tidak ada mitra yang tersedia</p>
                </div>
            @endforelse
        </div>

        <div id="no-mitras-message" class="text-center py-8 hidden">
            <p class="text-gray-500 text-sm">Tidak ada mitra yang tersedia di wilayah yang dipilih</p>
        </div>

        <div class="flex justify-between border-t border-gray-100 pt-4">
            <a href="{{ route('petani.dashboard') }}"
                class="border border-gray-300 text-gray-600 hover:bg-gray-50 font-medium py-2 px-5 rounded-lg transition-colors text-sm flex items-center gap-2">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Batal
            </a>
            <button id="btn-lanjut-mitra" disabled onclick="lanjutPilihMitra()"
                class="bg-gray-300 text-gray-500 font-medium py-2 px-5 rounded-lg transition-colors text-sm flex items-center gap-2 cursor-not-allowed">
                Lanjut Pilih Pupuk <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </button>
        </div>
    </div>

    <div id="step-2" class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm hidden">
        <div class="flex justify-between items-start mb-6 border-b border-gray-100 pb-4">
            <div>
                <h3 class="font-bold text-gray-800">Langkah 2: Pilih Pupuk & Keranjang</h3>
                <p class="text-[13px] text-gray-500">Mitra: <span id="nama-mitra-terpilih"
                        class="font-semibold text-green-700">-</span></p>
            </div>
            <button onclick="goToStep(1)"
                class="border border-gray-300 text-gray-600 hover:bg-gray-50 font-medium py-1.5 px-4 rounded-lg transition-colors text-xs">
                Ganti Mitra
            </button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-6">
            <div class="border border-gray-200 rounded-lg p-4 bg-gray-50/50 h-fit">
                <h4 class="font-bold text-sm text-gray-800 mb-3">Tambah ke Keranjang</h4>
                <div class="space-y-3">
                    <div>
                        <label class="text-xs font-semibold text-gray-700 mb-1 block">Jenis Pupuk</label>
                        <select id="pupuk-select"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-1 focus:ring-green-500 outline-none bg-white">
                            <option value="">-- Memuat Data Pupuk... --</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-700 mb-1 block">Jumlah (Kg)</label>
                        <input type="number" id="jumlah-input" min="1" placeholder="Masukkan jumlah..."
                            class="w-full px-3 py-2 border border-green-500 rounded-lg text-sm focus:ring-1 focus:ring-green-500 outline-none bg-white">
                    </div>
                    <button onclick="tambahKeKeranjang()" type="button"
                        class="w-full bg-[#8b5cf6] hover:bg-purple-600 text-white font-medium py-2 px-4 rounded-lg transition-colors text-sm flex items-center justify-center gap-2 mt-2">
                        <i data-lucide="shopping-cart" class="w-4 h-4"></i> Tambah
                    </button>
                </div>
            </div>

            <div>
                <h4 class="font-bold text-sm text-gray-800 mb-3">Isi Keranjang</h4>

                <div id="keranjang-list" class="border-b border-gray-100 pb-2 mb-3 min-h-[100px]">
                    <div class="text-center text-xs text-gray-400 py-6">Keranjang masih kosong</div>
                </div>

                <div class="bg-green-50 rounded-lg p-4 flex justify-between items-center border border-green-100">
                    <span class="font-bold text-sm text-green-800">Total Pembayaran:</span>
                    <span id="total-pembayaran" class="font-bold text-xl text-green-700">Rp 0</span>
                </div>
            </div>
        </div>

        <div class="flex justify-between border-t border-gray-100 pt-4">
            <button onclick="goToStep(1)"
                class="border border-gray-300 text-gray-600 hover:bg-gray-50 font-medium py-2 px-5 rounded-lg transition-colors text-sm flex items-center gap-2">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
            </button>
            <button id="btn-lanjut-bayar" disabled onclick="goToStep(3)"
                class="bg-gray-300 text-gray-500 cursor-not-allowed font-medium py-2 px-5 rounded-lg transition-colors text-sm flex items-center gap-2">
                Lanjut ke Pembayaran <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </button>
        </div>
    </div>

    <div id="step-3"
        class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden hidden max-w-xl mx-auto p-6">
        <h3 class="font-bold text-gray-800 mb-2">Langkah 3: Ringkasan Belanja</h3>
        <p class="text-[13px] text-gray-500 mb-6">Pastikan pesanan Anda sudah benar sebelum melanjutkan pembayaran.</p>

        <div id="ringkasan-belanja" class="space-y-4 mb-8">
            <!-- Akan diisi via JS -->
        </div>

        <div class="flex gap-3 pt-4 border-t border-gray-100">
            <button onclick="goToStep(2)"
                class="w-1/3 border border-gray-300 text-gray-600 hover:bg-gray-50 font-medium py-3 px-4 rounded-lg transition-colors text-sm">
                Kembali
            </button>
            <button id="btn-final-pay" onclick="prosesPembayaranQRIS()"
                class="w-2/3 bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg transition-colors text-sm flex items-center justify-center gap-2">
                <i data-lucide="credit-card" class="w-5 h-5"></i> Bayar via QRIS / Midtrans
            </button>
        </div>
    </div>

    <div id="step-4"
        class="bg-white rounded-xl shadow-sm overflow-hidden hidden max-w-2xl mx-auto border border-gray-200">
        <div class="bg-[#16a34a] text-white text-center py-6 px-4">
            <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-3">
                <i data-lucide="check" class="w-6 h-6"></i>
            </div>
            <h3 class="font-bold text-xl mb-1">Pembayaran Berhasil!</h3>
            <p class="text-xs text-green-100">Tunjukkan Kode QR ini ke Kios Mitra untuk mengambil pupuk Anda.</p>
        </div>

        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4 text-sm">
                <div>
                    <p class="text-xs text-gray-500 mb-0.5">ID Transaksi</p>
                    <p id="struk-id" class="font-bold text-gray-800">-</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-0.5">Mitra Pengambilan</p>
                    <p id="struk-mitra" class="font-bold text-gray-800">-</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-0.5">Tanggal</p>
                    <p id="struk-tanggal" class="font-bold text-gray-800">-</p>
                </div>

                <div id="struk-items-container" class="border-t border-b border-gray-100 py-3 mt-4">
                </div>

                <div class="flex justify-between items-center">
                    <span class="font-bold text-gray-800">Total</span>
                    <span id="struk-total" class="font-bold text-green-700">-</span>
                </div>
            </div>

            <div class="flex flex-col items-center justify-center border-l border-gray-100 pl-6">
                <p class="text-xs font-bold text-gray-800 mb-3">Scan untuk Ambil Pupuk</p>
                <div class="border border-gray-200 p-2 rounded-xl bg-white mb-3 shadow-sm">
                    <img id="struk-qr" src="" alt="QR Ambil" class="w-32 h-32 rounded-lg">
                </div>
                <span class="bg-yellow-100 text-yellow-800 text-[11px] font-bold px-3 py-1 rounded-md">Menunggu
                    Pengambilan</span>
            </div>
        </div>

        <div class="bg-gray-50 p-4 border-t border-gray-100 flex justify-center gap-3">
            <button
                class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium py-2 px-5 rounded-lg transition-colors text-sm text-center">
                Cetak Struk
            </button>
            <a href="{{ route('petani.dashboard') }}"
                class="bg-[#16a34a] hover:bg-green-700 text-white font-medium py-2 px-5 rounded-lg transition-colors text-sm text-center">
                Selesai & Kembali
            </a>
        </div>
    </div>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();

        let selectedMitraId = null;
        let selectedMitraNama = null;

        const kecamatanFilter = document.getElementById('kecamatan-filter');
        const desaFilter = document.getElementById('desa-filter');
        const mitrasContainer = document.getElementById('mitras-container');
        const noMitrasMessage = document.getElementById('no-mitras-message');
        const btnLanjutMitra = document.getElementById('btn-lanjut-mitra');
        const mitrasCards = document.querySelectorAll('.mitra-card');

        // Handle Kecamatan Filter Change
        kecamatanFilter.addEventListener('change', function() {
            const selectedKecamatan = this.value;

            // Reset desa filter dan show/hide desa options
            desaFilter.value = '';
            const desaOptions = desaFilter.querySelectorAll('option');

            desaOptions.forEach(option => {
                if (option.value === '') {
                    option.style.display = 'block';
                } else if (selectedKecamatan === '') {
                    option.style.display = 'none';
                } else if (option.dataset.kecamatan === selectedKecamatan) {
                    option.style.display = 'block';
                } else {
                    option.style.display = 'none';
                }
            });

            filterMitras();
        });

        // Handle Desa Filter Change
        desaFilter.addEventListener('change', function() {
            filterMitras();
        });

        // Filter Mitras based on selected kecamatan and desa
        function filterMitras() {
            const selectedKecamatan = kecamatanFilter.value;
            const selectedDesa = desaFilter.value;
            let visibleMitras = 0;

            document.querySelectorAll('.mitra-card').forEach(card => {
                const mitraKecamatan = card.dataset.kecamatan;
                const mitraDesa = card.dataset.desa;
                let show = true;

                // Filter by kecamatan
                if (selectedKecamatan && mitraKecamatan !== selectedKecamatan) {
                    show = false;
                }

                // Filter by desa
                if (selectedDesa && mitraDesa !== selectedDesa) {
                    show = false;
                }

                if (show) {
                    card.style.display = 'block';
                    visibleMitras++;
                } else {
                    card.style.display = 'none';
                }
            });

            // Show "no mitras" message if no mitras are visible
            if (visibleMitras === 0) {
                noMitrasMessage.classList.remove('hidden');
                mitrasContainer.style.display = 'none';
            } else {
                noMitrasMessage.classList.add('hidden');
                mitrasContainer.style.display = 'grid';
            }

            // Show/hide reset button based on filter state
            const btnResetFilter = document.getElementById('btn-reset-filter');
            if (selectedKecamatan || selectedDesa) {
                btnResetFilter.classList.remove('hidden');
            } else {
                btnResetFilter.classList.add('hidden');
            }
        }

        // Select Mitra - only highlight, don't redirect
        function selectMitra(mitraId, mitraNama, element) {
            // Remove active state from all cards
            document.querySelectorAll('.mitra-card').forEach(card => {
                card.classList.remove('border-green-500', 'bg-green-50/30', 'ring-2', 'ring-green-500');
                card.classList.add('border-gray-300');
            });

            // Add active state to clicked card
            element.classList.remove('border-gray-300');
            element.classList.add('border-green-500', 'bg-green-50/30', 'ring-2', 'ring-green-500');

            // Store selected mitra info
            selectedMitraId = mitraId;
            selectedMitraNama = mitraNama;

            // Enable button
            btnLanjutMitra.disabled = false;
            btnLanjutMitra.classList.remove('bg-gray-300', 'text-gray-500', 'cursor-not-allowed');
            btnLanjutMitra.classList.add('bg-[#16a34a]', 'text-white', 'hover:bg-green-700', 'cursor-pointer');
        }

        // --- STATE KERANJANG ---
        let keranjang = [];
        let pupukTersedia = [];

        // Lanjut ke pemilihan pupuk
        async function lanjutPilihMitra() {
            // Cegah lanjut jika belum pilih mitra
            if (!selectedMitraId) return;

            // Tampilkan nama Kios yang dipilih di UI Langkah 2
            document.querySelector('#step-2 .font-semibold.text-green-700').textContent = selectedMitraNama;
            goToStep(2);

            const select = document.getElementById('pupuk-select');
            select.innerHTML = '<option value="">-- Memuat Data Pupuk... --</option>';

            try {
                // REQUEST DATA KE SERVER BERDASARKAN MITRA YANG DIPILIH
                const response = await fetch(`/api/mitra/${selectedMitraId}/pupuk`);
                const data = await response.json();

                // Simpan ke variabel global pupukTersedia
                pupukTersedia = data.map(item => ({
                    id: item.id_pupuk,
                    nama: item.nama_pupuk,
                    harga: parseInt(item.harga_subsidi),
                    stok_mitra: parseInt(item.stok_mitra),
                    sisa_jatah_petani: parseInt(item.sisa_jatah_petani)
                }));

                // Render ulang Dropdown
                renderDropdownPupuk();

                // Kosongkan keranjang setiap kali pindah kios
                keranjang = [];
                renderKeranjang();

            } catch (error) {
                console.error('Error:', error);
                select.innerHTML = '<option value="">-- Gagal memuat data dari Kios ini --</option>';
            }
        }

        function renderDropdownPupuk() {
            const select = document.getElementById('pupuk-select');
            select.innerHTML = '<option value="">-- Pilih Jenis Pupuk --</option>';

            pupukTersedia.forEach(pupuk => {
                // Sekarang cukup tampilkan stok yang ada di mitra/kios
                let stokTersedia = pupuk.stok_mitra;

                if (stokTersedia > 0) {
                    select.innerHTML += `<option value="${pupuk.id}" data-nama="${pupuk.nama}" data-harga="${pupuk.harga}" data-stok="${stokTersedia}">
                ${pupuk.nama} (Rp ${pupuk.harga.toLocaleString('id-ID')}/kg) - Stok: ${stokTersedia} Kg
            </option>`;
                } else {
                    select.innerHTML += `<option value="" disabled>
                ${pupuk.nama} - Stok Habis
            </option>`;
                }
            });
        }

        function tambahKeKeranjang() {
            const select = document.getElementById('pupuk-select');
            const inputJumlah = document.getElementById('jumlah-input');

            if (!select.value) return alert("Silakan pilih jenis pupuk terlebih dahulu!");
            if (!inputJumlah.value || inputJumlah.value <= 0) return alert("Masukkan jumlah dengan benar!");

            const option = select.options[select.selectedIndex];
            const idPupuk = option.value;
            const namaPupuk = option.dataset.nama;
            const harga = parseInt(option.dataset.harga);
            const stokTersedia = parseInt(option.dataset.stok);
            const jumlahMinta = parseInt(inputJumlah.value);

            if (jumlahMinta > stokTersedia) {
                return alert(`Jumlah melebihi stok! Stok yang tersedia hanya ${stokTersedia} Kg.`);
            }

            const indexAda = keranjang.findIndex(item => item.id === idPupuk);

            if (indexAda !== -1) {
                if (keranjang[indexAda].jumlah + jumlahMinta > stokTersedia) {
                    return alert(
                        `Gagal! Total pupuk ${namaPupuk} di keranjang melebihi stok tersedia (${stokTersedia} Kg).`);
                }
                keranjang[indexAda].jumlah += jumlahMinta;
                keranjang[indexAda].subtotal = keranjang[indexAda].jumlah * harga;
            } else {
                keranjang.push({
                    id: idPupuk,
                    nama: namaPupuk,
                    harga: harga,
                    jumlah: jumlahMinta,
                    subtotal: jumlahMinta * harga
                });
            }

            select.value = "";
            inputJumlah.value = "";
            renderKeranjang();
        }

        // Fungsi Merender UI Keranjang
        function renderKeranjang() {
            const container = document.getElementById('keranjang-list');
            let totalHarga = 0;

            let html = `
        <div class="grid grid-cols-12 text-xs font-semibold text-gray-500 mb-2 border-b border-gray-100 pb-2">
            <div class="col-span-5">Pupuk</div>
            <div class="col-span-3">Jumlah</div>
            <div class="col-span-3">Subtotal</div>
            <div class="col-span-1 text-right"></div>
        </div>
    `;

            if (keranjang.length === 0) {
                html += `<div class="text-center text-xs text-gray-400 py-4">Keranjang masih kosong</div>`;
            } else {
                keranjang.forEach((item, index) => {
                    totalHarga += item.subtotal;
                    html += `
                <div class="grid grid-cols-12 text-sm text-gray-800 items-center py-2 border-b border-gray-50">
                    <div class="col-span-5 font-medium">${item.nama}</div>
                    <div class="col-span-3">${item.jumlah} Kg</div>
                    <div class="col-span-3">Rp ${item.subtotal.toLocaleString('id-ID')}</div>
                    <div class="col-span-1 text-right">
                        <button onclick="hapusDariKeranjang(${index})" class="text-red-500 hover:text-red-700 p-1">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
            `;
                });
            }

            container.innerHTML = html;
            document.getElementById('total-pembayaran').textContent = `Rp ${totalHarga.toLocaleString('id-ID')}`;
            lucide.createIcons();

            // Atur tombol Lanjut Pembayaran
            const btnLanjutBayar = document.getElementById('btn-lanjut-bayar');
            if (keranjang.length > 0) {
                btnLanjutBayar.disabled = false;
                btnLanjutBayar.className =
                    "bg-[#16a34a] hover:bg-green-700 text-white font-medium py-2 px-5 rounded-lg transition-colors text-sm flex items-center gap-2";
            } else {
                btnLanjutBayar.disabled = true;
                btnLanjutBayar.className =
                    "bg-gray-300 text-gray-500 cursor-not-allowed font-medium py-2 px-5 rounded-lg transition-colors text-sm flex items-center gap-2";
            }
        }

        function hapusDariKeranjang(index) {
            keranjang.splice(index, 1);
            renderKeranjang();
        }

        // Reset Filter
        function resetFilter() {
            // Reset filter dropdowns
            kecamatanFilter.value = '';
            desaFilter.value = '';

            // Reset desa options visibility
            const desaOptions = desaFilter.querySelectorAll('option');
            desaOptions.forEach(option => {
                option.style.display = option.value === '' ? 'block' : 'none';
            });

            // Clear selected mitra
            document.querySelectorAll('.mitra-card').forEach(card => {
                card.classList.remove('border-green-500', 'bg-green-50/30', 'ring-2', 'ring-green-500');
                card.classList.add('border-gray-300');
            });

            // Reset variables
            selectedMitraId = null;
            selectedMitraNama = null;

            // Reset button state
            btnLanjutMitra.disabled = true;
            btnLanjutMitra.classList.remove('bg-[#16a34a]', 'text-white', 'hover:bg-green-700', 'cursor-pointer');
            btnLanjutMitra.classList.add('bg-gray-300', 'text-gray-500', 'cursor-not-allowed');

            // Show all mitras
            mitrasCards.forEach(card => {
                card.style.display = 'block';
            });
            noMitrasMessage.classList.add('hidden');
            mitrasContainer.style.display = 'grid';

            // Hide reset button
            const btnResetFilter = document.getElementById('btn-reset-filter');
            btnResetFilter.classList.add('hidden');
        }

        function goToStep(targetStep) {
            // Sembunyikan semua step
            document.getElementById('step-1').classList.add('hidden');
            document.getElementById('step-2').classList.add('hidden');
            document.getElementById('step-3').classList.add('hidden');
            document.getElementById('step-4').classList.add('hidden');

            // Tampilkan step target
            document.getElementById('step-' + targetStep).classList.remove('hidden');

            // Update Stepper Indikator
            for (let i = 1; i <= 4; i++) {
                let ind = document.getElementById('ind-' + i);
                if (i <= targetStep) {
                    ind.classList.remove('bg-gray-200', 'text-gray-500');
                    ind.classList.add('bg-green-600', 'text-white');
                } else {
                    ind.classList.remove('bg-green-600', 'text-white');
                    ind.classList.add('bg-gray-200', 'text-gray-500');
                }
            }

            // Jika ke Step 3, render ringkasan
            if (targetStep === 3) {
                renderRingkasan();
            }
        }

        function renderRingkasan() {
            const container = document.getElementById('ringkasan-belanja');
            let totalHarga = 0;
            let html = `
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                    <p class="text-xs text-gray-400 mb-3 uppercase tracking-wider font-bold">Daftar Pupuk</p>
                    <div class="space-y-3">
            `;

            keranjang.forEach(item => {
                totalHarga += item.subtotal;
                html += `
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-600 font-medium">${item.nama} (${item.jumlah} Kg)</span>
                        <span class="text-gray-900 font-bold">Rp ${item.subtotal.toLocaleString('id-ID')}</span>
                    </div>
                `;
            });

            html += `
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-200 flex justify-between items-center">
                        <span class="text-base font-bold text-gray-800">Total Akhir</span>
                        <span class="text-xl font-black text-green-600">Rp ${totalHarga.toLocaleString('id-ID')}</span>
                    </div>
                </div>
                <div class="bg-blue-50 border border-blue-100 rounded-lg p-3 flex items-start gap-3">
                    <i data-lucide="info" class="w-5 h-5 text-blue-500 flex-shrink-0"></i>
                    <p class="text-[11px] text-blue-700">Pembayaran aman dengan Midtrans. Anda bisa menggunakan QRIS (Gopay, ShopeePay, Dana, dll) atau Transfer Bank.</p>
                </div>
            `;

            container.innerHTML = html;
            lucide.createIcons();
        }

        async function prosesPembayaranQRIS() {
            if (keranjang.length === 0) return alert("Keranjang kosong!");

            const btn = document.getElementById('btn-final-pay');
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="animate-spin" data-lucide="loader-2"></i> Memproses...';
            lucide.createIcons();

            const payload = {
                id_mitra: selectedMitraId,
                total_pembayaran: keranjang.reduce((sum, item) => sum + item.subtotal, 0),
                keranjang: keranjang,
                _token: '{{ csrf_token() }}'
            };

            try {
                const response = await fetch('/api/checkout', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();

                if (data.snap_token) {
                    window.snap.pay(data.snap_token, {
                        onSuccess: function(result) {
                            Swal.fire('Berhasil', 'Pembayaran Anda telah diterima!', 'success');

                            // ==========================================
                            // INJEKSI DATA ASLI KE STRUK STEP 4
                            // ==========================================

                            // 1. Ambil ID Transaksi dari Backend (Pastikan Controller mengirim id_transaksi)
                            const txId = data.id_transaksi;

                            // 2. Format Tanggal Saat Ini
                            const now = new Date();
                            const options = {
                                year: 'numeric',
                                month: 'long',
                                day: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit'
                            };
                            const formattedDate = now.toLocaleDateString('id-ID', options) + ' WIB';

                            // 3. Masukkan Data ke Struk
                            document.getElementById('struk-id').innerText = txId;
                            document.getElementById('struk-mitra').innerText = selectedMitraNama;
                            document.getElementById('struk-tanggal').innerText = formattedDate;

                            // 4. Render Detail Item
                            let itemsHtml =
                                '<p class="text-xs font-bold text-gray-800 mb-2">Detail Pembelian:</p>';
                            keranjang.forEach(item => {
                                itemsHtml += `
                                <div class="flex justify-between items-center text-[13px] mb-1">
                                    <span class="text-gray-600">${item.nama} (${item.jumlah} kg)</span>
                                    <span class="text-gray-800">Rp ${item.subtotal.toLocaleString('id-ID')}</span>
                                </div>`;
                            });
                            document.getElementById('struk-items-container').innerHTML = itemsHtml;

                            // 5. Total dan QR Code Dinamis
                            const total = keranjang.reduce((a, b) => a + b.subtotal, 0);
                            document.getElementById('struk-total').innerText =
                                `Rp ${total.toLocaleString('id-ID')}`;

                            // Generate QR Code otomatis berdasarkan txId
                            document.getElementById('struk-qr').src =
                                `https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=${txId}`;

                            // ==========================================

                            // 6. Tampilkan Step 4 setelah data selesai dirakit
                            goToStep(4);
                        },
                        onPending: function(result) {
                            Swal.fire('Pending', 'Silakan selesaikan pembayaran Anda.', 'info');
                        },
                        onError: function(result) {
                            Swal.fire('Error', 'Pembayaran gagal!', 'error');
                        },
                        onClose: function() {
                            console.log('Customer closed the popup without finishing the payment');
                        }
                    });
                } else {
                    const errorMsg = data.error || 'Gagal mendapatkan token pembayaran';
                    Swal.fire('Gagal', errorMsg, 'error');
                }
            } catch (error) {
                console.error('Checkout error:', error);
                Swal.fire('Error', 'Terjadi kesalahan sistem saat menghubungi server.', 'error');
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalText;
                lucide.createIcons();
            }
        }
    </script>
@endsection
