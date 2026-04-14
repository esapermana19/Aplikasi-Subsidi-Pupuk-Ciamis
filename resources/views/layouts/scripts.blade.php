<script>
    function openEditModal(id, nik, name, email) {
        const modal = document.getElementById('editModal');
        const form = document.getElementById('editForm');
        form.action = `/admin/petani/update/${id}`;

        // Mengisi field modal
        document.getElementById('edit_nik').value = nik;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_email').value = email;

        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

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

    document.addEventListener('DOMContentLoaded', function() {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>
