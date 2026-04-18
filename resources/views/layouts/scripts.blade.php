//Modal Edit Petani
<script>
    // 1. Fungsi Buka Modal (Sudah disesuaikan dengan struktur relasi yang baru)
    function openEditModal(id_user, nik, nama_petani, email) {
        const modal = document.getElementById('editModal');
        const form = document.getElementById('editForm');

        // Sesuaikan URL action form. Pastikan '/admin/petani/update/' sesuai dengan route di web.php
        form.action = `/admin/petani/update/${id_user}`;

        // Mengisi field modal
        document.getElementById('edit_nik').value = nik;
        document.getElementById('edit_name').value = nama_petani;
        document.getElementById('edit_email').value = email;

        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden'; // Mengunci scroll background
    }

    // 2. Fungsi Tutup Modal (Ini yang membuat tombol Batal dan (X) berfungsi)
    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
        document.body.style.overflow = 'auto'; // Mengembalikan scroll background
    }

    // 3. Fungsi SweetAlert untuk Konfirmasi Status
    function confirmStatus(userId, status, userName) {
        const actionText = status === 'aktif' ? 'mengaktifkan' : 'menonaktifkan';
        const confirmColor = status === 'aktif' ? '#10b981' : '#f59e0b';

        Swal.fire({
            title: 'Konfirmasi Perubahan',
            text: `Apakah Anda yakin ingin ${actionText} akun ${userName}?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: confirmColor,
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Lanjutkan!',
            cancelButtonText: 'Batal',
            borderRadius: '1.5rem',
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('input-status-' + userId).value = status;
                document.getElementById('form-status-' + userId).submit();
            }
        });
    }

    // 4. Render Icon Lucide
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>
