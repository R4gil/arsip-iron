<script>
    var lihatModal = document.getElementById('lihatPeminjamanModal');
    if (lihatModal) {
        lihatModal.addEventListener('show.bs.modal', function (event) {
            var btn = event.relatedTarget;
            document.getElementById('detailNomor').textContent = btn.getAttribute('data-nomor');
            document.getElementById('detailNama').textContent = btn.getAttribute('data-nama');
            document.getElementById('detailKategori').textContent = btn.getAttribute('data-kategori');
            document.getElementById('detailLokasi').textContent = btn.getAttribute('data-lokasi');
            document.getElementById('detailPeminjam').textContent = btn.getAttribute('data-peminjam');
            document.getElementById('detailDivisi').textContent = btn.getAttribute('data-divisi');
            document.getElementById('detailTglKeluar').textContent = btn.getAttribute('data-tgl-keluar');
            document.getElementById('detailTglMasuk').textContent = btn.getAttribute('data-tgl-masuk');
            document.getElementById('detailStatus').textContent = btn.getAttribute('data-status');
            document.getElementById('detailKeterangan').textContent = btn.getAttribute('data-keterangan');
            var fileUrl = btn.getAttribute('data-file');
            var fileContainer = document.getElementById('fileContainer');
            if (fileUrl) {
                fileContainer.innerHTML = fileUrl.toLowerCase().endsWith('.pdf')
                    ? '<embed src="' + fileUrl + '" width="100%" height="600px" type="application/pdf">'
                    : '<img src="' + fileUrl + '" class="img-fluid">';
            } else {
                fileContainer.innerHTML = '<p class="text-muted mb-0">File arsip tidak tersedia.</p>';
            }
        });
    }
</script>
