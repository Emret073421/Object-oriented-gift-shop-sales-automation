<?php 
$page_title = 'Mal Alış (Tedarik) İşlemleri';
?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- ÜST BİLGİ VE YENİ ALIŞ BUTONU -->
<div class="card card-custom bg-white border-0 shadow-sm mb-4">
    <div class="card-body text-center p-5">
        <i class="fa-solid fa-truck-ramp-box fa-4x text-primary mb-3"></i>
        <h4 class="mb-2 fw-bold text-dark">Toptancı / Tedarikçi Mal Girişi</h4>
        <p class="text-muted mb-4">Dükkanınıza yeni mal/stok girişi yapmak ve resmi alış fişi düzenlemek için bu modülü kullanabilirsiniz.</p>
        <button class="btn btn-primary btn-lg px-5 fw-bold shadow-sm" onclick="openCartModal()">
            <i class="fa-solid fa-plus me-2"></i> Yeni Alış Fişi Oluştur
        </button>
    </div>
</div>

<!-- GEÇMİŞ ALIŞ HAREKETLERİ LİSTESİ -->
<div class="card card-custom bg-white border-0 shadow-sm">
    <div class="card-header bg-white py-3 border-0 d-flex flex-wrap justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-clock-rotate-left text-success me-2"></i> Son Alış (Tedarik) Hareketleri</h5>
        <!-- Tarih Filtresi Alanı -->
        <div class="d-flex align-items-center gap-2 mt-2 mt-md-0">
            <label for="startDate" class="form-label mb-0 small text-muted d-none d-md-block">Tarih Aralığı:</label>
            <input type="date" id="startDate" class="form-control form-control-sm py-1 shadow-sm" style="max-width: 140px;">
            <span class="text-muted">-</span>
            <input type="date" id="endDate" class="form-control form-control-sm py-1 shadow-sm" style="max-width: 140px;">
            <button class="btn btn-sm btn-primary px-3 shadow-sm" onclick="alisHareketleriniGetir()">
                <i class="fa-solid fa-filter me-1"></i> Filtrele
            </button>
            <button class="btn btn-sm btn-outline-secondary" onclick="tarihFiltresiniTemizle()" title="Filtreyi Temizle">
                <i class="fa-solid fa-rotate-right"></i>
            </button>
        </div>
    </div>
    <div class="card-body p-4">
        <div id="alisHareketleriListesi" style="min-height: 300px;">
            <div class="text-center py-5 text-muted">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="mt-2">Alış hareketleri yükleniyor...</p>
            </div>
        </div>
    </div>
</div>

<!-- ALIŞ SEPETİ MODALI -->
<div class="modal fade" id="cartModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-primary text-white border-0 py-3">
        <h5 class="modal-title fw-bold"><i class="fa-solid fa-cart-flatbed me-2"></i> Yeni Mal Alış Fişi (Tedarik Sepeti)</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body bg-light p-4">
        <div class="row g-4">
            <!-- Sol Taraf: Ürün Ekleme & Fiş Bilgileri -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h6 class="card-title fw-bold text-dark mb-3"><i class="fa-solid fa-barcode text-primary me-2"></i> Ürün Ekle</h6>
                        <div class="input-group mb-3 shadow-sm">
                            <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-barcode text-muted"></i></span>
                            <input type="text" id="barcodeInput" class="form-control border-start-0 ps-0" placeholder="Barkod Okutun veya Yazın..." onkeydown="if(event.key === 'Enter') searchAndAddProduct()">
                            <button class="btn btn-primary px-4 fw-bold" onclick="searchAndAddProduct()">Ekle</button>
                        </div>
                        <button class="btn btn-outline-secondary btn-sm w-100 py-2 fw-bold shadow-sm" onclick="showProductList()">
                            <i class="fa-solid fa-list me-2"></i> Sistemdeki Ürünler Listesinden Seç
                        </button>
                    </div>
                </div>
                
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h6 class="card-title fw-bold text-dark mb-3"><i class="fa-solid fa-file-invoice text-success me-2"></i> Fiş / Tedarikçi Bilgileri</h6>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted mb-1">Tedarikçi / Toptancı Adı</label>
                            <input type="text" id="supplierName" class="form-control py-2 shadow-sm" placeholder="Örn: Kütahya Çini Toptan">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted mb-1">İşlem Tarihi</label>
                            <input type="datetime-local" id="purchaseDate" class="form-control py-2 shadow-sm">
                        </div>
                        <div class="mb-0">
                            <label class="form-label small fw-bold text-muted mb-1">Açıklama / Not</label>
                            <textarea id="purchaseNote" class="form-control shadow-sm" rows="3" placeholder="Fiş ile ilgili eklemek istediğiniz notlar..."></textarea>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Sağ Taraf: Sepet Listesi -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="card-title fw-bold text-dark mb-0"><i class="fa-solid fa-boxes-packing text-warning me-2"></i> Sepet İçeriği</h6>
                            <button class="btn btn-sm btn-outline-danger shadow-sm" onclick="clearCart()">
                                <i class="fa-solid fa-trash me-1"></i> Sepeti Temizle
                            </button>
                        </div>
                        
                        <div class="table-responsive flex-grow-1 mb-4" style="min-height: 320px; max-height: 400px; overflow-y: auto;">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th>Ürün Adı</th>
                                        <th style="width: 120px;">Birim Alış F. (₺)</th>
                                        <th style="width: 110px;">Alınan Miktar</th>
                                        <th style="width: 110px;" class="text-end">Ara Toplam</th>
                                        <th style="width: 50px;" class="text-end"></th>
                                    </tr>
                                </thead>
                                <tbody id="cartTableBody">
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-5">Sepette henüz ürün yok.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="border-top pt-4 mt-auto">
                            <div class="d-flex justify-content-between align-items-center bg-light p-3 rounded shadow-sm">
                                <h5 class="mb-0 fw-bold text-dark">Genel Toplam:</h5>
                                <h2 class="text-success fw-bold mb-0" id="cartTotal">₺0.00</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
      </div>
      <div class="modal-footer bg-white py-3 border-0">
        <button type="button" class="btn btn-secondary px-4 fw-bold shadow-sm" data-bs-dismiss="modal">İptal</button>
        <button type="button" class="btn btn-success px-5 fw-bold shadow-sm" onclick="completePurchaseFi()">
            <i class="fa-solid fa-check me-2"></i> Alış Fişini Kaydet
        </button>
      </div>
    </div>
  </div>
</div>

<script>
let purchaseCart = [];
let cartModalInstance = null;

document.addEventListener("DOMContentLoaded", function() {
    alisHareketleriniGetir();
    // Tarih alanına şu anki zamanı varsayılan atayalım
    let now = new Date();
    now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
    document.getElementById('purchaseDate').value = now.toISOString().slice(0,16);
});

// 1. Geçmiş Alış Hareketlerini Getirme
function alisHareketleriniGetir() {
    let baslangic = document.getElementById('startDate').value;
    let bitis = document.getElementById('endDate').value;

    let formData = new FormData();
    formData.append('baslangic', baslangic);
    formData.append('bitis', bitis);

    fetch("ajax/alis_hareketleri_getir.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        document.getElementById('alisHareketleriListesi').innerHTML = data;
    })
    .catch(error => console.error("Alış hareketleri getirme hatası:", error));
}

function tarihFiltresiniTemizle() {
    document.getElementById('startDate').value = "";
    document.getElementById('endDate').value = "";
    alisHareketleriniGetir();
}

// 2. Alış Fişi Modalı Açma
function openCartModal() {
    if (!cartModalInstance) {
        cartModalInstance = new bootstrap.Modal(document.getElementById('cartModal'));
    }
    cartModalInstance.show();
    setTimeout(() => document.getElementById('barcodeInput').focus(), 500);
}

// 3. Barkod ile Ürün Arama ve Sepete Ekleme
function searchAndAddProduct() {
    let barkod = document.getElementById('barcodeInput').value.trim();
    if (!barkod) {
        Swal.fire({icon: 'warning', title: 'Uyarı', text: 'Lütfen bir barkod girin veya okutun!', timer: 1500, showConfirmButton: false});
        return;
    }

    let formData = new FormData();
    formData.append('barkod', barkod);

    fetch("ajax/urun_arama_alis.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.bulundu) {
            addToCart(data.urun, 1);
            document.getElementById('barcodeInput').value = "";
        } else {
            Swal.fire('Bulunamadı', data.mesaj, 'warning');
        }
    })
    .catch(error => console.error("Arama hatası:", error));
}

// 4. Sistemdeki Ürünler Listesini Açma (Manuel Seçim)
function showProductList() {
    Swal.fire({
        title: 'Sistemdeki Ürünler',
        html: `<div id="urunListesiAlani"><div class="spinner-border text-primary my-4"></div></div>`,
        width: '800px',
        showConfirmButton: false,
        showCloseButton: true,
        didOpen: () => {
            fetch("ajax/urun_listesi_alis.php")
            .then(response => response.text())
            .then(data => {
                document.getElementById('urunListesiAlani').innerHTML = data;
            });
        }
    });
}

function secilenUrunuSepeteEkle(urun) {
    addToCart(urun, 1);
    Swal.fire({
        icon: 'success',
        title: 'Eklendi',
        text: `${urun.ad} sepete eklendi.`,
        timer: 1000,
        showConfirmButton: false
    });
}

// 5. Sepete Ürün Ekleme / Miktar Artırma
function addToCart(urun, miktar) {
    let existing = purchaseCart.find(item => item.id === urun.id);
    if (existing) {
        existing.qty += miktar;
    } else {
        purchaseCart.push({ id: urun.id, barkod: urun.barkod, ad: urun.ad, price: urun.alis_fiyati, qty: miktar });
    }
    updateCartUI();
}

// 6. Sepetten Ürün Çıkarma
function removeFromCart(id) {
    purchaseCart = purchaseCart.filter(item => item.id !== id);
    updateCartUI();
}

// 7. Sepetteki Miktar veya Fiyatı Güncelleme
function updateItemData(id, newQty, newPrice) {
    let item = purchaseCart.find(i => i.id === id);
    if (item) {
        item.qty = Math.max(1, parseInt(newQty) || 1);
        item.price = Math.max(0, parseFloat(newPrice) || 0);
    }
    updateCartUI();
}

// 8. Sepeti Temizleme
function clearCart() {
    if (purchaseCart.length === 0) return;
    Swal.fire({
        title: 'Sepeti Temizle',
        text: 'Alış sepetindeki tüm ürünleri silmek istediğinize emin misiniz?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Evet, Temizle',
        cancelButtonText: 'İptal'
    }).then((result) => {
        if (result.isConfirmed) {
            purchaseCart = [];
            updateCartUI();
            Swal.fire({icon: 'success', title: 'Temizlendi', timer: 1000, showConfirmButton: false});
        }
    });
}

// 9. Sepet Arayüzünü Güncelleme
function updateCartUI() {
    let tableBody = document.getElementById('cartTableBody');
    let totalElement = document.getElementById('cartTotal');
    
    tableBody.innerHTML = "";
    let grandTotal = 0;

    if (purchaseCart.length === 0) {
        tableBody.innerHTML = `<tr><td colspan="5" class="text-center text-muted py-5">Sepette henüz ürün yok.</td></tr>`;
        totalElement.innerText = "₺0.00";
        return;
    }

    purchaseCart.forEach(item => {
        let lineTotal = item.price * item.qty;
        grandTotal += lineTotal;
        
        tableBody.innerHTML += `
            <tr>
                <td>
                    <div class="fw-bold text-dark">${item.ad}</div>
                    <div class="text-muted fs-7">${item.barkod}</div>
                </td>
                <td>
                    <input type="number" step="0.01" min="0" value="${item.price}" onchange="updateItemData(${item.id}, ${item.qty}, this.value)" class="form-control form-control-sm py-1 fw-bold text-primary">
                </td>
                <td>
                    <input type="number" min="1" value="${item.qty}" onchange="updateItemData(${item.id}, this.value, ${item.price})" class="form-control form-control-sm py-1 fw-bold">
                </td>
                <td class="text-end fw-bold text-dark">₺${lineTotal.toFixed(2)}</td>
                <td class="text-end">
                    <button class="btn btn-sm btn-outline-danger shadow-sm" onclick="removeFromCart(${item.id})" title="Sil">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
    });

    totalElement.innerText = "₺" + grandTotal.toFixed(2);
}

// 10. Alış Fişini Kaydetme (Backend'e Gönderme)
function completePurchaseFi() {
    if (purchaseCart.length === 0) {
        Swal.fire('Sepet Boş', 'Lütfen alış fişine en az bir ürün ekleyin.', 'warning');
        return;
    }
    
    let tedarikci = document.getElementById('supplierName').value.trim();
    let tarih = document.getElementById('purchaseDate').value;
    let not = document.getElementById('purchaseNote').value.trim();

    Swal.fire({
        title: 'Fişi Kaydet',
        text: 'Mal alış fişini kaydetmek ve stokları artırmak istediğinize emin misiniz?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#d33',
        confirmButtonText: '<i class="fa-solid fa-check me-1"></i> Evet, Kaydet',
        cancelButtonText: 'İptal'
    }).then((result) => {
        if (result.isConfirmed) {
            let payload = {
                tedarikci: tedarikci,
                tarih: tarih,
                not: not,
                sepet: purchaseCart
            };

            fetch("ajax/alis_yap.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(payload)
            })
            .then(response => response.json())
            .then(data => {
                if (data.basarili) {
                    Swal.fire({
                        title: 'Başarılı!',
                        text: data.mesaj,
                        icon: 'success',
                        confirmButtonText: 'Tamam'
                    }).then(() => {
                        purchaseCart = [];
                        updateCartUI();
                        document.getElementById('supplierName').value = "";
                        document.getElementById('purchaseNote').value = "";
                        if (cartModalInstance) cartModalInstance.hide();
                        alisHareketleriniGetir();
                    });
                } else {
                    Swal.fire('Hata!', data.mesaj, 'error');
                }
            })
            .catch(error => {
                console.error("Kayıt hatası:", error);
                Swal.fire('Hata!', 'Sunucu ile iletişim kurulurken bir hata oluştu.', 'error');
            });
        }
    });
}

// 11. Alış Detay İnceleme Modalı
function alisDetayGoster(islemKodu) {
    let formData = new FormData();
    formData.append('islem_kodu', islemKodu);

    fetch("ajax/alis_detay_modal_getir.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        Swal.fire({
            title: `Fiş Detayı: ${islemKodu}`,
            html: data,
            width: '800px',
            showConfirmButton: true,
            confirmButtonText: 'Kapat',
            confirmButtonColor: '#3085d6'
        });
    })
    .catch(error => console.error("Detay getirme hatası:", error));
}
</script>
