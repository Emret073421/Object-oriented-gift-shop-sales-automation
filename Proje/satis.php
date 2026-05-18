<?php 
$page_title = 'Hızlı Satış Ekranı (POS)';
?>
<!-- jQuery ve SweetAlert2 CDN -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    /* Sadece bu sayfaya özel Koyu Tema (POS Ekranı Tasarımı) */
    .pos-container {
        background-color: transparent;
        border-radius: 12px;
        color: #1e293b;
        min-height: 80vh;
        padding: 0;
        font-family: 'Segoe UI', sans-serif;
    }
    .pos-card {
        background-color: #1e293b;
        border: 1px solid #334155;
        border-radius: 12px;
    }
    .pos-product-card {
        background-color: #1e293b;
        border: 1px solid #334155;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .pos-product-card:hover {
        border-color: #64748b;
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }
    .pos-product-card.selected {
        border: 2px solid #0ea5e9;
        background-color: #1e293b;
    }
    .pos-price {
        color: #10b981;
        font-weight: 800;
        font-size: 1.3rem;
    }
    .pos-stock {
        color: #10b981;
        font-size: 0.85rem;
        font-weight: 500;
    }
    .pos-search {
        background-color: #ffffff;
        border: 1px solid #cbd5e1;
        color: #1e293b;
        border-radius: 8px;
        font-size: 1.1rem;
    }
    .pos-search::placeholder {
        color: #94a3b8;
    }
    .pos-search:focus {
        background-color: #ffffff;
        color: #1e293b;
        box-shadow: 0 0 0 0.25rem rgba(14, 165, 233, 0.25);
        border-color: #0ea5e9;
    }
    .category-pill {
        background-color: #ffffff;
        color: #475569;
        border: 1px solid #cbd5e1;
        border-radius: 20px;
        padding: 6px 18px;
        font-size: 0.9rem;
        cursor: pointer;
        margin-right: 10px;
        margin-bottom: 10px;
        display: inline-block;
        transition: 0.2s;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }
    .category-pill:hover {
        background-color: #f1f5f9;
        color: #0f172a;
    }
    .category-pill.active {
        background-color: #1e293b;
        color: white;
        border-color: #1e293b;
    }
    .cart-summary {
        border-top: 1px solid #334155;
        padding-top: 20px;
        margin-top: auto;
    }
    .btn-complete {
        background-color: #047857;
        color: white;
        font-weight: 700;
        border: none;
        transition: 0.2s;
    }
    .btn-complete:hover {
        background-color: #065f46;
        color: white;
    }
    .form-select-dark, .form-control-dark {
        background-color: #0f172a;
        border: 1px solid #334155;
        color: #e2e8f0;
    }
    .form-select-dark:focus, .form-control-dark:focus {
        background-color: #0f172a;
        color: white;
        border-color: #0ea5e9;
        box-shadow: none;
    }
    ::-webkit-scrollbar { width: 8px; }
    ::-webkit-scrollbar-track { background: #0f172a; }
    ::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
    ::-webkit-scrollbar-thumb:hover { background: #475569; }
</style>

<div class="pos-container">
    <div class="row h-100">
        <!-- SOL BÖLÜM: Ürün Kataloğu -->
        <div class="col-lg-8 pe-lg-4 d-flex flex-column">
            
            <!-- Başlık ve Rozet -->
            <div class="d-flex justify-content-between align-items-center mb-4 mt-2">
                <h4 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-box text-primary me-2"></i> Ürün Kataloğu</h4>
                <span class="badge bg-primary rounded-pill px-3 py-2" id="katalogUrunSayisi" style="font-size: 0.9rem;">Yükleniyor...</span>
            </div>

            <!-- Arama Çubuğu -->
            <div class="mb-4 position-relative">
                <i class="fa-solid fa-magnifying-glass position-absolute text-muted" style="left: 15px; top: 12px; font-size: 1.2rem;"></i>
                <input type="text" id="aramaGirdisi" class="form-control pos-search ps-5 py-2 shadow-sm" placeholder="Ürün adı, barkod veya kategori ile ara..." onkeyup="urunAra()">
            </div>

            <!-- Kategori Combobox -->
            <div class="mb-4">
                <select id="kategoriSecimi" class="form-select pos-search py-2 shadow-sm" onchange="urunAra()" style="border-radius: 8px; font-size: 1.1rem;">
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

            <!-- Ürün Grid Alanı (Dikey Scroll) -->
            <div id="urun_listesi" class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-3 overflow-y-auto overflow-x-hidden pe-2 pb-3" style="height: 55vh; min-height: 380px;">
                <div class="col-12 py-4 text-center text-muted">Ürünler yükleniyor...</div>
            </div>
        </div>

        <!-- SAĞ BÖLÜM: Satış Sepeti -->
        <div class="col-lg-4 mt-4 mt-lg-0 h-100 d-flex flex-column">
            <div class="pos-card p-4 h-100 d-flex flex-column shadow">
                
                <!-- Sepet Başlık -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0 fw-bold text-white"><i class="fa-solid fa-basket-shopping text-warning me-2"></i> Satış Sepeti</h5>
                    <button class="btn btn-sm btn-outline-danger border-0 text-danger" style="background: rgba(220, 53, 69, 0.1);" onclick="clearSalesCart()"><i class="fa-solid fa-trash-can me-1"></i> Temizle</button>
                </div>

                <!-- Sepet İçi (JS ile Liste Buraya Gelecek) -->
                <div id="salesCartContainer" class="flex-grow-1 d-flex flex-column mb-4 overflow-y-auto pe-2" style="height: 40vh; min-height: 280px;">
                    <div id="emptyCartMessage" class="d-flex flex-column align-items-center justify-content-center h-100 p-4" style="border: 2px dashed #334155; border-radius: 12px; background-color: rgba(30, 41, 59, 0.5);">
                        <i class="fa-solid fa-cart-arrow-down fa-3x mb-3 text-secondary"></i>
                        <h6 class="text-white mb-1">Sepet boş</h6>
                        <small class="text-muted">Soldaki katalog/listeden ürün ekleyin</small>
                    </div>
                    <div id="salesCartItems"></div>
                </div>

                <!-- Sepet Alt Özet ve Tamamlama Alanı -->
                <div class="cart-summary">
                    <div class="d-flex justify-content-between mb-2 text-muted">
                        <span class="fs-6">Ürün Çeşidi</span>
                        <span class="fs-6" id="salesUniqueItemsCount">0</span>
                    </div>
                    <div class="d-flex justify-content-between mb-4 text-muted">
                        <span class="fs-6">Toplam Adet</span>
                        <span class="fs-6" id="salesTotalItemsCount">0</span>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-end mb-4">
                        <h5 class="mb-0 fw-bold text-white tracking-wide">GENEL TOPLAM</h5>
                        <h2 class="mb-0 fw-bold tracking-wide" style="color: #10b981;" id="salesGrandTotal">₺0.00</h2>
                    </div>

                    <div class="mb-4">
                        <input type="text" id="salesNote" class="form-control form-control-dark rounded-3 py-3" placeholder="Satış notu (özel indirim, not...)">
                    </div>

                    <button class="btn btn-complete w-100 py-3 rounded-3 fs-5 d-flex justify-content-center align-items-center shadow" onclick="completeSale()">
                        <i class="fa-solid fa-money-check-dollar me-2"></i> SATIŞI TAMAMLA
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    let salesCart = [];

    // Sayfa yüklendiğinde ürünleri getir
    document.addEventListener("DOMContentLoaded", function() {
        urunAra();
    });

    // Ürünleri AJAX ile getirme ve filtreleme
    function urunAra() {
        let arama = document.getElementById('aramaGirdisi').value;
        let kategori = document.getElementById('kategoriSecimi').value;

        let formData = new FormData();
        formData.append('arama', arama);
        formData.append('kategori', kategori);

        fetch("ajax/urun_getir.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            document.getElementById('urun_listesi').innerHTML = data;
            // Katalogdaki ürün sayısını güncelle
            let urunKartlari = document.getElementById('urun_listesi').getElementsByClassName('pos-product-card').length;
            document.getElementById('katalogUrunSayisi').innerText = urunKartlari + " ürün";
        })
        .catch(error => console.error("Ürün getirme hatası:", error));
    }

    // 1. Ürünü Sepete Ekleme
    function addSaleItem(product) {
        if (product.stok <= 0) {
            Swal.fire('Stok Yok!', 'Bu ürünün stoğu tükenmiş.', 'warning');
            return;
        }

        let existing = salesCart.find(item => item.id === product.id);
        if (existing) {
            if (existing.miktar >= product.stok) {
                Swal.fire('Stok Yetersiz!', 'Stokta olandan fazla ekleyemezsiniz.', 'warning');
                return;
            }
            existing.miktar += 1;
        } else {
            salesCart.push({ id: product.id, ad: product.ad, fiyat: product.fiyat, miktar: 1, stok: product.stok, barkod: product.barkod });
        }
        
        updateSalesCartUI();
    }

    // 2. Sepetten Ürün Çıkarma
    function removeSaleItem(productId) {
        salesCart = salesCart.filter(item => item.id !== productId);
        updateSalesCartUI();
    }

    // 3. Sepeti Tamamen Temizleme
    function clearSalesCart() {
        if (salesCart.length > 0) {
            Swal.fire({
                title: 'Sepeti Temizle',
                text: "Sepetteki tüm ürünleri silmek istediğinize emin misiniz?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Evet, temizle!',
                cancelButtonText: 'İptal'
            }).then((result) => {
                if (result.isConfirmed) {
                    salesCart = [];
                    updateSalesCartUI();
                }
            });
        } else {
            Swal.fire('Sepet Zaten Boş', 'Temizlenecek ürün bulunamadı.', 'info');
        }
    }

    // Miktar Değiştirme Fonksiyonu (+ / - butonları ve doğrudan input girişi için)
    function miktarDegistir(productId, yeniMiktar) {
        yeniMiktar = parseInt(yeniMiktar);
        if (isNaN(yeniMiktar) || yeniMiktar < 1) {
            removeSaleItem(productId);
            return;
        }

        let existing = salesCart.find(item => item.id === productId);
        if (existing) {
            if (yeniMiktar > existing.stok) {
                Swal.fire('Stok Yetersiz!', `Bu üründen stokta en fazla ${existing.stok} adet var.`, 'warning');
                existing.miktar = existing.stok;
            } else {
                existing.miktar = yeniMiktar;
            }
            updateSalesCartUI();
        }
    }

    // 4. Sepet Arayüzünü Güncelleme
    function updateSalesCartUI() {
        let itemsContainer = document.getElementById('salesCartItems');
        let emptyMsg = document.getElementById('emptyCartMessage');
        let uniqueCount = document.getElementById('salesUniqueItemsCount');
        let totalCount = document.getElementById('salesTotalItemsCount');
        let grandTotal = document.getElementById('salesGrandTotal');
        
        let totalItems = 0;
        let totalPrice = 0;
        let itemsHTML = '';

        if (salesCart.length === 0) {
            emptyMsg.classList.remove('d-none');
            emptyMsg.classList.add('d-flex');
            itemsContainer.innerHTML = '';
        } else {
            emptyMsg.classList.remove('d-flex');
            emptyMsg.classList.add('d-none');
            
            salesCart.forEach(item => {
                let itemTotal = item.fiyat * item.miktar;
                totalItems += item.miktar;
                totalPrice += itemTotal;

                itemsHTML += `
                <div class="bg-dark p-2 rounded mb-2 border border-secondary shadow-sm">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <div class="text-white fw-bold text-truncate pe-2" style="font-size: 0.95rem; max-width: 75%;">${item.ad}</div>
                        <button class="btn btn-sm btn-link text-danger p-0 border-0" onclick="removeSaleItem(${item.id})" title="Kaldır"><i class="fa-solid fa-trash-can fs-6"></i></button>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">₺${item.fiyat.toFixed(2)} / adet</div>
                        <div class="d-flex align-items-center">
                            <div class="input-group input-group-sm me-2" style="width: 90px;">
                                <button class="btn btn-outline-secondary text-white px-2 py-0" type="button" onclick="miktarDegistir(${item.id}, ${item.miktar - 1})"><i class="fa-solid fa-minus" style="font-size: 0.75rem;"></i></button>
                                <input type="number" class="form-control text-center bg-light fw-bold px-1 py-0" value="${item.miktar}" min="1" max="${item.stok}" onchange="miktarDegistir(${item.id}, this.value)">
                                <button class="btn btn-outline-secondary text-white px-2 py-0" type="button" onclick="miktarDegistir(${item.id}, ${item.miktar + 1})"><i class="fa-solid fa-plus" style="font-size: 0.75rem;"></i></button>
                            </div>
                            <div class="text-success fw-bold text-end" style="font-size: 1.05rem; min-width: 75px;">₺${itemTotal.toFixed(2)}</div>
                        </div>
                    </div>
                </div>`;
            });

            itemsContainer.innerHTML = itemsHTML;
        }

        uniqueCount.innerText = salesCart.length;
        totalCount.innerText = totalItems;
        grandTotal.innerText = '₺' + totalPrice.toFixed(2);
    }

    // 5. Satışı Tamamlama (Veritabanına Kayıt)
    function completeSale() {
        if (salesCart.length === 0) {
            Swal.fire('Sepet Boş', 'Lütfen satışı tamamlamak için sepete ürün ekleyin.', 'warning');
            return;
        }
        
        let note = document.getElementById('salesNote').value;
        
        fetch("ajax/satis_yap.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ items: salesCart, note: note })
        })
        .then(response => response.json())
        .then(data => {
            if (data.basarili) {
                Swal.fire({
                    title: 'Satış Başarılı!',
                    html: `<b>İşlem Kodu:</b> <span class="text-success fs-5">${data.islem_kodu}</span><br><br>Satış başarıyla kaydedildi ve stoklar düşüldü.`,
                    icon: 'success',
                    confirmButtonText: 'Tamam'
                }).then(() => {
                    salesCart = [];
                    document.getElementById('salesNote').value = '';
                    updateSalesCartUI();
                    urunAra(); // Stokların güncel halini ekrana yansıt
                });
            } else {
                Swal.fire('Hata!', data.mesaj || 'Satış işlemi başarısız oldu.', 'error');
            }
        })
        .catch(error => {
            console.error("Satış hatası:", error);
            Swal.fire('Hata!', 'Sunucu ile iletişim kurulurken bir hata oluştu.', 'error');
        });
    }
</script>
