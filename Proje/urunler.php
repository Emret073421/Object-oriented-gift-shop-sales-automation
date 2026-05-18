<?php 
$page_title = 'Ürün Yönetimi';
?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="card card-custom bg-white border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-0">
        <h5 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-boxes-stacked me-2 text-primary"></i> Sistemdeki Ürünler</h5>
        <button class="btn btn-primary px-4 fw-bold shadow-sm" onclick="urunEklePenceresi()">
            <i class="fa-solid fa-plus me-2"></i> Yeni Ürün Ekle
        </button>
    </div>
    <div class="card-body p-4">
        <!-- Filtreleme Alanı -->
        <div class="row g-3 mb-4">
            <div class="col-md-6 col-lg-5 position-relative">
                <i class="fa-solid fa-magnifying-glass position-absolute text-muted" style="left: 25px; top: 14px;"></i>
                <input type="text" id="aramaGirdisi" class="form-control ps-5 py-2 shadow-sm" placeholder="Ürün adı veya barkod ile ara..." onkeyup="urunleriGetir()">
            </div>
            <div class="col-md-6 col-lg-4">
                <select id="kategoriSecimi" class="form-select py-2 shadow-sm" onchange="urunleriGetir()">
                    <option value="tumu">Tüm Kategoriler</option>
                    <?php
                    $kategoriSorgu = $db->query("SELECT * FROM kategoriler WHERE durum = 1 ORDER BY ad ASC");
                    if ($kategoriSorgu && $kategoriSorgu->num_rows > 0) {
                        while ($kat = $kategoriSorgu->fetch_assoc()) {
                            echo '<option value="' . $kat['id'] . '">' . htmlspecialchars($kat['ad']) . '</option>';
                        }
                    }
                    ?>
                </select>
            </div>
        </div>
        
        <!-- Ürün Listesi Tablo Alanı -->
        <div id="urunYonetimListesi" style="min-height: 400px;">
            <div class="text-center py-5 text-muted">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="mt-2">Ürünler yükleniyor...</p>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    urunleriGetir();
});

// Ürünleri AJAX ile getirme
function urunleriGetir() {
    let arama = document.getElementById('aramaGirdisi').value;
    let kategori = document.getElementById('kategoriSecimi').value;

    let formData = new FormData();
    formData.append('arama', arama);
    formData.append('kategori', kategori);

    fetch("ajax/urun_yonetim_getir.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        document.getElementById('urunYonetimListesi').innerHTML = data;
    })
    .catch(error => console.error("Ürün getirme hatası:", error));
}

// Kategorilerin HTML seçeneklerini JS içinde kullanmak için hazırlıyoruz
const kategoriSecenekleri = `
    <?php
    $kategoriSorgu = $db->query("SELECT * FROM kategoriler WHERE durum = 1 ORDER BY ad ASC");
    if ($kategoriSorgu && $kategoriSorgu->num_rows > 0) {
        while ($kat = $kategoriSorgu->fetch_assoc()) {
            echo '<option value="' . $kat['id'] . '">' . addslashes(htmlspecialchars($kat['ad'])) . '</option>';
        }
    }
    ?>
`;

// Yeni Ürün Ekleme Penceresi
function urunEklePenceresi() {
    Swal.fire({
        title: 'Yeni Ürün Ekle',
        html: `
            <div class="text-start mb-3">
                <label class="fw-bold form-label">Barkod:</label>
                <input type="text" id="ekleBarkod" class="form-control" placeholder="Örn: 869000000001" required>
            </div>
            <div class="text-start mb-3">
                <label class="fw-bold form-label">Ürün Adı:</label>
                <input type="text" id="ekleAd" class="form-control" placeholder="Örn: Işıklı Kar Küresi" required>
            </div>
            <div class="text-start mb-3">
                <label class="fw-bold form-label">Kategori:</label>
                <select id="ekleKategori" class="form-select">
                    ${kategoriSecenekleri}
                </select>
            </div>
            <div class="row g-2 mb-3">
                <div class="col-6 text-start">
                    <label class="fw-bold form-label">Alış Fiyatı (₺):</label>
                    <input type="number" id="ekleAlis" class="form-control" step="0.01" min="0" placeholder="0.00" required>
                </div>
                <div class="col-6 text-start">
                    <label class="fw-bold form-label">Satış Fiyatı (₺):</label>
                    <input type="number" id="ekleSatis" class="form-control" step="0.01" min="0" placeholder="0.00" required>
                </div>
            </div>
            <div class="text-start mb-3">
                <label class="fw-bold form-label">Başlangıç Stok Miktarı:</label>
                <input type="number" id="ekleStok" class="form-control" min="0" value="10" required>
            </div>
        `,
        icon: 'info',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: '<i class="fa-solid fa-check me-1"></i> Kaydet',
        cancelButtonText: 'İptal',
        preConfirm: () => {
            let barkod = document.getElementById('ekleBarkod').value;
            let ad = document.getElementById('ekleAd').value;
            let kategori_id = document.getElementById('ekleKategori').value;
            let alis_fiyati = document.getElementById('ekleAlis').value;
            let satis_fiyati = document.getElementById('ekleSatis').value;
            let stok_miktari = document.getElementById('ekleStok').value;

            if (!barkod || !ad || !alis_fiyati || !satis_fiyati || !stok_miktari) {
                Swal.showValidationMessage('Lütfen tüm alanları doldurun.');
            }
            return { barkod, ad, kategori_id, alis_fiyati, satis_fiyati, stok_miktari };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            let formData = new FormData();
            formData.append('barkod', result.value.barkod);
            formData.append('ad', result.value.ad);
            formData.append('kategori_id', result.value.kategori_id);
            formData.append('alis_fiyati', result.value.alis_fiyati);
            formData.append('satis_fiyati', result.value.satis_fiyati);
            formData.append('stok_miktari', result.value.stok_miktari);

            fetch("ajax/urun_ekle.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.basarili) {
                    Swal.fire('Başarılı!', data.mesaj, 'success').then(() => urunleriGetir());
                } else {
                    Swal.fire('Hata!', data.mesaj, 'error');
                }
            })
            .catch(error => {
                console.error('Ekleme hatası:', error);
                Swal.fire('Hata!', 'Sunucu ile iletişim kurulurken bir hata oluştu.', 'error');
            });
        }
    });
}

// Ürün Düzenleme Penceresi
function urunDuzenlePenceresi(urun) {
    Swal.fire({
        title: 'Ürün Düzenle',
        html: `
            <div class="text-start mb-3">
                <label class="fw-bold form-label">Barkod:</label>
                <input type="text" id="duzenleBarkod" class="form-control" value="${urun.barkod}" required>
            </div>
            <div class="text-start mb-3">
                <label class="fw-bold form-label">Ürün Adı:</label>
                <input type="text" id="duzenleAd" class="form-control" value="${urun.ad}" required>
            </div>
            <div class="text-start mb-3">
                <label class="fw-bold form-label">Kategori:</label>
                <select id="duzenleKategori" class="form-select">
                    ${kategoriSecenekleri}
                </select>
            </div>
            <div class="row g-2 mb-3">
                <div class="col-6 text-start">
                    <label class="fw-bold form-label">Alış Fiyatı (₺):</label>
                    <input type="number" id="duzenleAlis" class="form-control" step="0.01" min="0" value="${urun.alis_fiyati}" required>
                </div>
                <div class="col-6 text-start">
                    <label class="fw-bold form-label">Satış Fiyatı (₺):</label>
                    <input type="number" id="duzenleSatis" class="form-control" step="0.01" min="0" value="${urun.satis_fiyati}" required>
                </div>
            </div>
            <div class="text-start mb-3">
                <label class="fw-bold form-label">Stok Miktarı:</label>
                <input type="number" id="duzenleStok" class="form-control" min="0" value="${urun.stok_miktari}" required>
            </div>
        `,
        icon: 'edit',
        didOpen: () => {
            document.getElementById('duzenleKategori').value = urun.kategori_id;
        },
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: '<i class="fa-solid fa-check me-1"></i> Güncelle',
        cancelButtonText: 'İptal',
        preConfirm: () => {
            let barkod = document.getElementById('duzenleBarkod').value;
            let ad = document.getElementById('duzenleAd').value;
            let kategori_id = document.getElementById('duzenleKategori').value;
            let alis_fiyati = document.getElementById('duzenleAlis').value;
            let satis_fiyati = document.getElementById('duzenleSatis').value;
            let stok_miktari = document.getElementById('duzenleStok').value;

            if (!barkod || !ad || !alis_fiyati || !satis_fiyati || !stok_miktari) {
                Swal.showValidationMessage('Lütfen tüm alanları doldurun.');
            }
            return { id: urun.id, barkod, ad, kategori_id, alis_fiyati, satis_fiyati, stok_miktari };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            let formData = new FormData();
            formData.append('id', result.value.id);
            formData.append('barkod', result.value.barkod);
            formData.append('ad', result.value.ad);
            formData.append('kategori_id', result.value.kategori_id);
            formData.append('alis_fiyati', result.value.alis_fiyati);
            formData.append('satis_fiyati', result.value.satis_fiyati);
            formData.append('stok_miktari', result.value.stok_miktari);

            fetch("ajax/urun_guncelle.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.basarili) {
                    Swal.fire('Başarılı!', data.mesaj, 'success').then(() => urunleriGetir());
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

// Ürün Silme (Arşivleme)
function urunSil(id, ad) {
    Swal.fire({
        title: 'Ürünü Sil',
        html: `<b>${ad}</b> isimli ürünü silmek (arşivlemek) istediğinize emin misiniz?`,
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

            fetch("ajax/urun_sil.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.basarili) {
                    Swal.fire('Silindi!', data.mesaj, 'success').then(() => urunleriGetir());
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
