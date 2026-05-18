<?php 
$page_title = 'Kategori Yönetimi';
?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="row g-4 mb-4">
    <!-- SOL BÖLÜM: Yeni Kategori Ekleme Formu -->
    <div class="col-lg-4">
        <div class="card card-custom bg-white border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-folder-plus text-primary me-2"></i> Yeni Kategori Ekle</h5>
            </div>
            <div class="card-body p-4">
                <form id="kategoriEkleForm" onsubmit="kategoriEkle(event)">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Kategori Adı <span class="text-danger">*</span></label>
                        <input type="text" id="ekleKatAd" class="form-control py-2 shadow-sm" placeholder="Örn: Seramik Ürünler" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold">Açıklama (Opsiyonel)</label>
                        <textarea id="ekleKatAciklama" class="form-control shadow-sm" rows="4" placeholder="Kategori içeriği hakkında kısa bilgi..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm">
                        <i class="fa-solid fa-save me-2"></i> Kaydet
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- SAĞ BÖLÜM: Mevcut Kategoriler Tablosu -->
    <div class="col-lg-8">
        <div class="card card-custom bg-white border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-folder-tree text-success me-2"></i> Mevcut Kategoriler</h5>
                <button class="btn btn-sm btn-outline-secondary" onclick="kategorileriGetir()" title="Yenile">
                    <i class="fa-solid fa-rotate-right"></i>
                </button>
            </div>
            <div class="card-body p-4">
                <div id="kategoriYonetimListesi" style="min-height: 350px;">
                    <div class="text-center py-5 text-muted">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-2">Kategoriler yükleniyor...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    kategorileriGetir();
});

// Kategorileri AJAX ile getirme
function kategorileriGetir() {
    fetch("ajax/kategori_getir.php")
    .then(response => response.text())
    .then(data => {
        document.getElementById('kategoriYonetimListesi').innerHTML = data;
    })
    .catch(error => console.error("Kategori getirme hatası:", error));
}

// Yeni Kategori Ekleme İşlemi
function kategoriEkle(event) {
    event.preventDefault();

    let ad = document.getElementById('ekleKatAd').value;
    let aciklama = document.getElementById('ekleKatAciklama').value;

    let formData = new FormData();
    formData.append('ad', ad);
    formData.append('aciklama', aciklama);

    fetch("ajax/kategori_ekle.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.basarili) {
            Swal.fire('Başarılı!', data.mesaj, 'success').then(() => {
                document.getElementById('kategoriEkleForm').reset();
                kategorileriGetir();
            });
        } else {
            Swal.fire('Hata!', data.mesaj, 'error');
        }
    })
    .catch(error => {
        console.error('Ekleme hatası:', error);
        Swal.fire('Hata!', 'Sunucu ile iletişim kurulurken bir hata oluştu.', 'error');
    });
}

// Kategori Düzenleme Penceresi
function kategoriDuzenlePenceresi(kat) {
    Swal.fire({
        title: 'Kategori Düzenle',
        html: `
            <div class="text-start mb-3">
                <label class="fw-bold form-label">Kategori Adı:</label>
                <input type="text" id="duzenleKatAd" class="form-control" value="${kat.ad}" required>
            </div>
            <div class="text-start mb-3">
                <label class="fw-bold form-label">Açıklama:</label>
                <textarea id="duzenleKatAciklama" class="form-control" rows="3">${kat.aciklama}</textarea>
            </div>
        `,
        icon: 'edit',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: '<i class="fa-solid fa-check me-1"></i> Güncelle',
        cancelButtonText: 'İptal',
        preConfirm: () => {
            let ad = document.getElementById('duzenleKatAd').value;
            let aciklama = document.getElementById('duzenleKatAciklama').value;

            if (!ad) {
                Swal.showValidationMessage('Lütfen kategori adını girin.');
            }
            return { id: kat.id, ad, aciklama };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            let formData = new FormData();
            formData.append('id', result.value.id);
            formData.append('ad', result.value.ad);
            formData.append('aciklama', result.value.aciklama);

            fetch("ajax/kategori_guncelle.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.basarili) {
                    Swal.fire('Başarılı!', data.mesaj, 'success').then(() => kategorileriGetir());
                } else {
                    Swal.fire('Hata!', data.mesaj, 'error');
                }
            })
            .catch(error => {
                console.error('Güncelleme hatası:', error);
                Swal.fire('Hata!', 'Sunucu ile iletişim kurulurken bir hata oluştu.', 'error');
            });
        }
    });
}

// Kategori Silme (Arşivleme)
function kategoriSil(id, ad) {
    Swal.fire({
        title: 'Kategoriyi Sil',
        html: `<b>${ad}</b> isimli kategoriyi silmek istediğinize emin misiniz?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Evet, Sil!',
        cancelButtonText: 'İptal'
    }).then((result) => {
        if (result.isConfirmed) {
            let formData = new FormData();
            formData.append('id', id);

            fetch("ajax/kategori_sil.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.basarili) {
                    Swal.fire('Silindi!', data.mesaj, 'success').then(() => kategorileriGetir());
                } else {
                    Swal.fire('Hata!', data.mesaj, 'error');
                }
            })
            .catch(error => {
                console.error('Silme hatası:', error);
                Swal.fire('Hata!', 'Sunucu ile iletişim kurulurken bir hata oluştu.', 'error');
            });
        }
    });
}
</script>
