<?php 
$page_title = 'Mal Alış (Tedarik) İşlemleri';
// header is now included in index.php
?>

<div class="card card-custom bg-white mb-4">
    <div class="card-body text-center p-5">
        <i class="fa-solid fa-truck-ramp-box fa-4x text-muted mb-3"></i>
        <h4 class="mb-3">Toptancı / Tedarikçi Mal Girişi</h4>
        <p class="text-muted mb-4">Dükkanınıza yeni mal/stok girişi yapmak için bu modülü kullanabilirsiniz.</p>
        <button class="btn btn-primary btn-lg" onclick="openCartModal()"><i class="fa-solid fa-plus me-2"></i>Yeni Alış Fişi Oluştur</button>
    </div>
</div>

<div class="card card-custom bg-white">
    <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center">
        <h6 class="mb-0">Son Alış (Tedarik) Hareketleri</h6>
        <!-- Tarih Filtresi Alanı -->
        <div class="d-flex align-items-center gap-2 mt-2 mt-md-0">
            <label for="startDate" class="form-label mb-0 small text-muted d-none d-md-block">Tarih Aralığı:</label>
            <input type="date" id="startDate" class="form-control form-control-sm" style="max-width: 150px;">
            <span class="text-muted">-</span>
            <input type="date" id="endDate" class="form-control form-control-sm" style="max-width: 150px;">
            <button class="btn btn-sm btn-outline-primary" onclick="filterByDate()">
                <i class="fa-solid fa-filter"></i> Filtrele
            </button>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive" style="max-height: 60vh; overflow-y: auto;">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">İşlem Kodu</th>
                        <th>Tarih</th>
                        <th>Tedarikçi/Not</th>
                        <th>Alınan Ürün</th>
                        <th>Miktar</th>
                        <th>Toplam Tutar</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="ps-4 text-muted">#AL-100</td>
                        <td>Bugün, 10:00</td>
                        <td>Kütahya Çini Toptan</td>
                        <td>El Yapımı Çini Kase</td>
                        <td><span class="badge bg-primary">10 Adet</span></td>
                        <td class="fw-bold">₺1,200.00</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Alış Sepeti Modalı (JS kodlaması için iskelet) -->
<div class="modal fade" id="cartModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa-solid fa-cart-flatbed text-primary me-2"></i>Yeni Mal Alış Fişi (Tedarik Sepeti)</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body bg-light">
        <div class="row">
            <!-- Sol Taraf: Ürün Ekleme -->
            <div class="col-md-5">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <h6 class="card-title fw-bold">Ürün Ekle</h6>
                        <div class="input-group mb-3">
                            <span class="input-group-text"><i class="fa-solid fa-barcode"></i></span>
                            <input type="text" id="barcodeInput" class="form-control" placeholder="Barkod Okutun veya Yazın...">
                            <button class="btn btn-primary" onclick="searchAndAddProduct()">Ekle</button>
                        </div>
                        <small class="text-muted">Buraya bir ürün arama/seçme listesi de yapabilirsiniz.</small>
                        <div class="mt-3">
                             <button class="btn btn-outline-secondary btn-sm w-100" onclick="showProductList()"><i class="fa-solid fa-list me-1"></i> Ürün Listesinden Seç</button>
                        </div>
                    </div>
                </div>
                
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="card-title fw-bold">Fiş / Tedarikçi Bilgileri</h6>
                        <input type="text" id="supplierName" class="form-control mb-2" placeholder="Tedarikçi Adı (Örn: Kütahya Çini)">
                        <input type="date" id="purchaseDate" class="form-control mb-2">
                        <textarea id="purchaseNote" class="form-control" rows="2" placeholder="Notlar..."></textarea>
                    </div>
                </div>
            </div>
            
            <!-- Sağ Taraf: Sepet Listesi -->
            <div class="col-md-7">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="card-title fw-bold mb-0">Sepet İçeriği</h6>
                            <button class="btn btn-sm btn-outline-danger" onclick="clearCart()"><i class="fa-solid fa-trash me-1"></i>Temizle</button>
                        </div>
                        
                        <div class="table-responsive flex-grow-1" style="min-height: 250px;">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Ürün Adı</th>
                                        <th style="width: 100px;">Birim Fiyat</th>
                                        <th style="width: 120px;">Miktar</th>
                                        <th style="width: 100px;">Ara Toplam</th>
                                        <th style="width: 50px;"></th>
                                    </tr>
                                </thead>
                                <tbody id="cartTableBody">
                                    <!-- JS İLE BURAYA SATIRLAR EKLENECEK ÖRNEK SATIR: -->
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">Sepette henüz ürün yok.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="border-top pt-3 mt-auto">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5>Genel Toplam:</h5>
                                <h3 class="text-primary fw-bold mb-0" id="cartTotal">₺0.00</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
        <button type="button" class="btn btn-success px-4" onclick="completePurchaseFi()"><i class="fa-solid fa-check me-2"></i>Fişi Kaydet</button>
      </div>
    </div>
  </div>
</div>

<script>
    /* =========================================================================
       MAL ALIŞ (TEDARİK) SEPETİ - JAVASCRIPT İSKELETİ
       Aşağıdaki fonksiyonların içini kendi mantığına göre doldurabilirsin.
       Ben JS kodlamada neleri açman gerektiğini ayarladım.
    ========================================================================= */

    // Sepet verilerini tutacağın dizi (Array)
    let purchaseCart = []; 

    // 1. Yeni Alış Fişi Modalı Açma Fonksiyonu
    function openCartModal() {
        // Modal'ı açmak için Bootstrap JS kodunu kullanıyoruz
        var myModal = new bootstrap.Modal(document.getElementById('cartModal'));
        myModal.show();
        
        // Modal açıldığında barkod inputuna odaklansın
        setTimeout(() => {
            document.getElementById('barcodeInput').focus();
        }, 500);
    }

    // 2. Barkod ile ürünü aratıp sepete ekleme
    function searchAndAddProduct() {
        let barcode = document.getElementById('barcodeInput').value;
        if(barcode.trim() === "") {
            alert("Lütfen bir barkod girin!");
            return;
        }

        // TODO: AJAX/Fetch ile veritabanından barkoda göre ürünü bul.
        // Bulunan ürünü sepete eklemek için addToCart() fonksiyonunu çağır.
        console.log("Aranan Barkod: " + barcode);
        
        // Örnek sahte ürün eklemesi (Sen burayı sileceksin, fetch ile gelen veriyi ekleyeceksin)
        // addToCart({ id: 1, name: "Test Ürün", price: 50.00 }, 1);
        
        document.getElementById('barcodeInput').value = ""; // Inputu temizle
    }

    // 3. Ürün listesi modalını veya alanını açma (Manuel seçim için)
    function showProductList() {
        // TODO: Ürünlerin listelendiği ayrı bir modal açabilirsin.
        console.log("Ürün listesi açılıyor...");
    }

    // 4. Sepete ürün ekleme fonksiyonu
    function addToCart(product, quantity) {
        // TODO: Ürün zaten sepette varsa (purchaseCart dizisinde) miktarını artır.
        // Yoksa diziye yeni eleman olarak ekle.
        
        // Örnek mantık:
        /*
        let existingItem = purchaseCart.find(item => item.id === product.id);
        if(existingItem) {
            existingItem.qty += quantity;
        } else {
            purchaseCart.push({ ...product, qty: quantity });
        }
        */
        
        updateCartUI(); // Ekledikten sonra arayüzü güncelle
    }

    // 5. Sepetten ürün çıkarma fonksiyonu
    function removeFromCart(productId) {
        // TODO: purchaseCart dizisinden productId'ye sahip ürünü sil.
        
        // Örnek mantık:
        // purchaseCart = purchaseCart.filter(item => item.id !== productId);
        
        updateCartUI(); // Sildikten sonra arayüzü güncelle
    }

    // 6. Sepetteki bir ürünün miktarını veya alış fiyatını değiştirme
    function updateItemData(productId, newQty, newPrice) {
        // TODO: Sepetteki ürünün miktar veya fiyat inputları değiştiğinde diziyi güncelle
        
        updateCartUI(); // Değişiklikten sonra arayüzü güncelle (Genel toplam vs.)
    }

    // 7. Sepeti tamamen boşaltma
    function clearCart() {
        if(confirm("Sepeti temizlemek istediğinize emin misiniz?")) {
            purchaseCart = [];
            updateCartUI();
        }
    }

    // 8. Sepet Arayüzünü (Tabloyu ve Toplamı) Güncelleme
    function updateCartUI() {
        let tableBody = document.getElementById('cartTableBody');
        let totalElement = document.getElementById('cartTotal');
        
        tableBody.innerHTML = ""; // Tabloyu temizle
        let grandTotal = 0;

        if(purchaseCart.length === 0) {
            tableBody.innerHTML = `<tr><td colspan="5" class="text-center text-muted py-4">Sepette henüz ürün yok.</td></tr>`;
            totalElement.innerText = "₺0.00";
            return;
        }

        // TODO: purchaseCart dizisinde dön (map/forEach) ve tablo satırlarını (<tr>) oluşturup tableBody.innerHTML içine ekle.
        // Ayrıca grandTotal'i hesapla.
        /*
        purchaseCart.forEach(item => {
            let lineTotal = item.price * item.qty;
            grandTotal += lineTotal;
            
            tableBody.innerHTML += `
                <tr>
                    <td>${item.name}</td>
                    <td><input type="number" value="${item.price}" onchange="updateItemData(${item.id}, ${item.qty}, this.value)" class="form-control form-control-sm"></td>
                    <td><input type="number" value="${item.qty}" onchange="updateItemData(${item.id}, this.value, ${item.price})" class="form-control form-control-sm"></td>
                    <td>₺${lineTotal.toFixed(2)}</td>
                    <td><button class="btn btn-sm btn-danger" onclick="removeFromCart(${item.id})"><i class="fa-solid fa-trash"></i></button></td>
                </tr>
            `;
        });
        */

        totalElement.innerText = "₺" + grandTotal.toFixed(2);
    }

    // 9. Alış Fişini Kaydetme (Veritabanına Gönderme)
    function completePurchaseFi() {
        if(purchaseCart.length === 0) {
            alert("Sepet boş! Önce ürün ekleyin.");
            return;
        }
        
        let supplierName = document.getElementById('supplierName').value;
        let date = document.getElementById('purchaseDate').value;
        let note = document.getElementById('purchaseNote').value;

        // TODO: Sepet dizisini (purchaseCart) ve fiş bilgilerini (tedarikçi, tarih vs.) AJAX/Fetch ile PHP'ye (backend) yolla.
        // PHP tarafında bu verileri veritabanına kaydet (Alışlar ve Alış Detayları tablolarına) ve stoğu artır.
        console.log("Gönderilecek Veriler:", {
            supplier: supplierName,
            date: date,
            note: note,
            items: purchaseCart
        });
        
        alert("Fiş başarıyla kaydedildi! (Bu sadece simülasyon, backend kodunu yazacaksınız)");
        
        // Kayıttan sonra:
        // clearCart();
        // Modal'ı kapat: bootstrap.Modal.getInstance(document.getElementById('cartModal')).hide();
    }

    // 10. Tarihe Göre Filtreleme (Ana ekrandaki tablo için)
    function filterByDate() {
        let start = document.getElementById('startDate').value;
        let end = document.getElementById('endDate').value;
        
        if(!start || !end) {
            alert("Lütfen başlangıç ve bitiş tarihlerini seçin.");
            return;
        }

        // TODO: Bu tarihleri kullanarak AJAX ile geçmiş alışları getir ve ana ekrandaki tabloyu (Son Alış Hareketleri) güncelle.
        // Ya da formu submit ettirerek PHP ile sayfayı yenile.
        console.log(`Tarih Filtresi: ${start} - ${end}`);
        alert(`${start} ve ${end} arasındaki kayıtlar getirilecek.`);
    }

</script>

// footer is now included in index.php
