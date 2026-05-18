<?php
$page_title = 'Ürün İade ve İptal İşlemleri';
?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="row mb-4">
    <div class="col-lg-8 mx-auto">
        <div class="card card-custom bg-white border-0 shadow-sm p-4">
            <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-magnifying-glass text-primary me-2"></i> Satış Fişi / İşlem Kodu Sorgulama</h5>
            <p class="text-muted small mb-4">İade almak istediğiniz satışın işlem kodunu (Örn: <b>FIS-20260519-1234</b> veya <b>FIS-001</b>) aşağıdaki alana girerek arama yapınız.</p>
            
            <form id="islemSorguForm" onsubmit="islemSorgula(event)">
                <div class="input-group mb-3">
                    <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-receipt text-secondary"></i></span>
                    <input type="text" id="sorguIslemKodu" class="form-control border-start-0 ps-0 py-3 fs-5" placeholder="İşlem Kodu (Örn: FIS-001)" required>
                    <button class="btn btn-primary px-5 fw-bold fs-6" type="submit">
                        <i class="fa-solid fa-search me-2"></i> Sorgula
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card card-custom bg-white border-0 shadow-sm p-4">
            <h5 class="fw-bold text-dark mb-4"><i class="fa-solid fa-list-check text-success me-2"></i> Satış Detayları ve İade İşlemi</h5>
            <div id="islemSonucAlani">
                <div class="text-center py-5 text-muted">
                    <i class="fa-solid fa-file-invoice fa-3x mb-3 text-secondary"></i>
                    <p class="fs-5 mb-0">Sorgulama sonucu burada görüntülenecektir.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function islemSorgula(event) {
    event.preventDefault();
    let islemKodu = document.getElementById('sorguIslemKodu').value;
    let sonucAlani = document.getElementById('islemSonucAlani');

    sonucAlani.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div><p class="mt-2 text-muted">Sorgulanıyor...</p></div>';

    let formData = new FormData();
    formData.append('islem_kodu', islemKodu);

    fetch("ajax/islem_detay_getir.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        sonucAlani.innerHTML = data;
    })
    .catch(error => {
        console.error("Sorgulama hatası:", error);
        sonucAlani.innerHTML = '<div class="alert alert-danger">Sunucu ile iletişim kurulurken bir hata oluştu.</div>';
    });
}

function iadePenceresi(islemKodu, urunId, urunAdi, maxMiktar) {
    Swal.fire({
        title: 'Ürün İade Al',
        html: `
            <div class="text-start mb-3">
                <label class="fw-bold form-label">Ürün Adı:</label>
                <input type="text" class="form-control bg-light" value="${urunAdi}" readonly>
            </div>
            <div class="text-start mb-3">
                <label class="fw-bold form-label">İade Edilecek Miktar (Max: ${maxMiktar}):</label>
                <input type="number" id="iadeMiktar" class="form-control" value="1" min="1" max="${maxMiktar}">
            </div>
            <div class="text-start mb-3">
                <label class="fw-bold form-label">İade Nedeni / Açıklama:</label>
                <textarea id="iadeAciklama" class="form-control" rows="2" placeholder="Müşteri iadesi, kusurlu ürün vb..."></textarea>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: '<i class="fa-solid fa-rotate-left me-1"></i> İadeyi Onayla',
        cancelButtonText: 'İptal',
        preConfirm: () => {
            let miktar = document.getElementById('iadeMiktar').value;
            let aciklama = document.getElementById('iadeAciklama').value;
            if (!miktar || miktar < 1 || miktar > maxMiktar) {
                Swal.showValidationMessage(`Lütfen 1 ile ${maxMiktar} arasında bir miktar girin.`);
            }
            return { miktar: miktar, aciklama: aciklama };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            let formData = new FormData();
            formData.append('islem_kodu', islemKodu);
            formData.append('urun_id', urunId);
            formData.append('miktar', result.value.miktar);
            formData.append('aciklama', result.value.aciklama);

            fetch("ajax/iade_yap.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.basarili) {
                    Swal.fire({
                        title: 'İade Başarılı!',
                        html: `<b>İade Kodu:</b> <span class="text-danger fs-5">${data.iade_kodu}</span><br><br>${data.mesaj}`,
                        icon: 'success',
                        confirmButtonText: 'Tamam'
                    }).then(() => {
                        // Listeyi güncelle
                        document.getElementById('islemSorguForm').dispatchEvent(new Event('submit'));
                    });
                } else {
                    Swal.fire('Hata!', data.mesaj || 'İade işlemi başarısız oldu.', 'error');
                }
            })
            .catch(error => {
                console.error("İade hatası:", error);
                Swal.fire('Hata!', 'Sunucu ile iletişim kurulurken bir hata oluştu.', 'error');
            });
        }
    });
}
</script>
