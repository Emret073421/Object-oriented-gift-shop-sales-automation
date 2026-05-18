<?php 
$page_title = 'Hızlı Satış Ekranı (POS)';
// header is now included in index.php
?>
<style>
    /* Sadece bu sayfaya özel Koyu Tema (POS Ekranı Tasarımı) */
    .pos-container {
        background-color: transparent; /* Arka planı genel temayla uyumlu (şeffaf/açık gri) yaptık */
        border-radius: 12px;
        color: #1e293b; /* Başlıklar ve yazılar için koyu renk */
        min-height: 80vh;
        padding: 0;
        font-family: 'Segoe UI', sans-serif;
    }
    .pos-card {
        background-color: #1e293b; /* Kart arka planı */
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
        border: 2px solid #0ea5e9; /* Seçili üründe mavi çerçeve */
        background-color: #1e293b;
    }
    .pos-price {
        color: #10b981; /* Açık yeşil fiyat */
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
        background-color: #047857; /* Koyu yeşil buton */
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
    /* Scrollbar tasarımı */
    ::-webkit-scrollbar {
        width: 8px;
    }
    ::-webkit-scrollbar-track {
        background: #0f172a; 
    }
    ::-webkit-scrollbar-thumb {
        background: #334155; 
        border-radius: 4px;
    }
    ::-webkit-scrollbar-thumb:hover {
        background: #475569; 
    }
</style>

<div class="pos-container">
    <div class="row h-100">
        <!-- SOL BÖLÜM: Ürün Kataloğu -->
        <div class="col-lg-8 pe-lg-4 d-flex flex-column">
            
            <!-- Başlık ve Rozet -->
            <div class="d-flex justify-content-between align-items-center mb-4 mt-2">
                <h4 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-box text-primary me-2"></i> Ürün Kataloğu</h4>
                <!-- Stoktaki ürün sayısını göster -->
                <span class="badge bg-primary rounded-pill px-3 py-2" style="font-size: 0.9rem;">10 ürün</span>
            </div>

            <!-- Arama Çubuğu -->
            <div class="mb-4 position-relative">
                <i class="fa-solid fa-magnifying-glass position-absolute text-muted" style="left: 15px; top: 12px; font-size: 1.2rem;"></i>
                <input type="text" class="form-control pos-search ps-5 py-2 shadow-sm" placeholder="Ürün adı, barkod veya kategori ile ara..." onkeyup="urunAra(this.value)">
            </div>

            <!-- Kategori Combobox -->
            <div class="mb-4">
                <select class="form-select pos-search py-2 shadow-sm" onchange="filtreliUrunGetir(this.value)" style="border-radius: 8px; font-size: 1.1rem;">
                    <option value="tumu">Tümü</option>
                    <?php
                    // Kategorileri veritabanından çekiyoruz
                    $kategoriSorgu = $db->query("SELECT * FROM kategoriler WHERE durum = 1 ORDER BY ad ASC");
                    if($kategoriSorgu && $kategoriSorgu->num_rows > 0){
                        while($kat = $kategoriSorgu->fetch_assoc()){
                            echo '<option value="' . $kat['id'] . '">' . htmlspecialchars($kat['ad']) . '</option>';
                        }
                    }
                    ?>
                </select>
            </div>

            <!-- Ürün Grid Alanı (Dikey Scroll) -->
            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-3 overflow-y-auto overflow-x-hidden pe-2 pb-3" style="max-height: 55vh;">

                <!-- Ürün 6 -->
                <div class="col">
                    <div class="pos-product-card p-3 h-100 d-flex flex-column">
                        <h6 class="fw-bold mb-1 text-white">Doğal Taşlı Otantik Kolye</h6>
                        <small class="text-muted d-block mb-3" style="font-size: 0.75rem; letter-spacing: 1px;">869000000010</small>
                        <small class="d-block mb-auto text-secondary">Takılar</small>
                        <div class="mt-3">
                            <div class="pos-price mb-1">₺95.00</div>
                            <div class="pos-stock"><i class="fa-solid fa-check me-1"></i> 60 adet</div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- SAĞ BÖLÜM: Satış Sepeti -->
        <div class="col-lg-4 mt-4 mt-lg-0 h-100 d-flex flex-column">
            <div class="pos-card p-4 h-100 d-flex flex-column shadow">
                
                <!-- Sepet Başlık -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0 fw-bold"><i class="fa-solid fa-basket-shopping text-warning me-2"></i> Satış Sepeti</h5>
                    <button class="btn btn-sm btn-outline-danger border-0 text-danger" style="background: rgba(220, 53, 69, 0.1);" onclick="clearSalesCart()"><i class="fa-solid fa-trash-can me-1"></i> Temizle</button>
                </div>

                <!-- Müşteri Seçimi Kaldırıldı -->

                <!-- Sepet İçi (JS ile Liste Buraya Gelecek) -->
                <div id="salesCartContainer" class="flex-grow-1 d-flex flex-column mb-4 overflow-y-auto pe-2" style="max-height: 40vh;">
                    <!-- Boş Durum Gösterimi -->
                    <div id="emptyCartMessage" class="d-flex flex-column align-items-center justify-content-center h-100 p-4" style="border: 2px dashed #334155; border-radius: 12px; background-color: rgba(30, 41, 59, 0.5);">
                        <i class="fa-solid fa-cart-arrow-down fa-3x mb-3 text-secondary"></i>
                        <h6 class="text-white mb-1">Sepet boş</h6>
                        <small class="text-muted">Soldaki katalogdan ürün ekleyin</small>
                    </div>
                    
                    <!-- JS ile buraya sepet öğeleri eklenecek, örnek HTML yapısı:
                    <div class="d-flex justify-content-between align-items-center bg-dark p-2 rounded mb-2 border border-secondary">
                        <div>
                            <div class="text-white fw-bold">Ürün Adı</div>
                            <small class="text-muted">₺Fiyat x Adet</small>
                        </div>
                        <div class="d-flex align-items-center">
                            <h6 class="text-success mb-0 me-3">₺Toplam</h6>
                            <button class="btn btn-sm btn-danger" onclick="removeSaleItem(1)"><i class="fa-solid fa-xmark"></i></button>
                        </div>
                    </div>
                    -->
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

    function urunAra(arama){
        
    }

    function filtreliUrunGetir(kategori){
        
    }

    function urunGetir(arama) {
        $.ajax({
            url: "ajax/urun_getir.php",
            type: "POST",
            data: { arama: arama },
            success: function(response) {
                $("#urun_listesi").html(response);
            }
        });
    }
    /* =========================================================================
       SATIŞ (POS) SEPETİ - JAVASCRIPT İSKELETİ
       Aşağıdaki fonksiyonların içini satış (satis.php) mantığına göre doldurabilirsin.
    ========================================================================= */
    
    let salesCart = [];

    // 1. Ürünü Sepete Ekleme (Sol taraftaki ürün kartlarına tıklanınca çalışmalı)
    // Ürün kartlarındaki <div> etiketine onclick="addSaleItem({id: 1, name: 'Kupa', price: 50.00})" ekleyebilirsin.
    function addSaleItem(product) {
        // TODO: Ürün sepette varsa miktarını artır, yoksa yeni obje olarak diziye ekle.
        /*
        let existing = salesCart.find(item => item.id === product.id);
        if(existing) {
            existing.qty += 1;
        } else {
            salesCart.push({ ...product, qty: 1 });
        }
        */
        
        updateSalesCartUI();
    }

    // 2. Sepetten Ürün Çıkarma
    function removeSaleItem(productId) {
        // TODO: salesCart dizisinden productId'ye sahip öğeyi çıkar.
        // salesCart = salesCart.filter(item => item.id !== productId);
        updateSalesCartUI();
    }

    // 3. Sepeti Tamamen Temizleme
    function clearSalesCart() {
        if(confirm("Sepeti temizlemek istediğinize emin misiniz?")) {
            salesCart = [];
            updateSalesCartUI();
        }
    }

    // 4. Sepet Arayüzünü Güncelleme
    function updateSalesCartUI() {
        let container = document.getElementById('salesCartContainer');
        let emptyMsg = document.getElementById('emptyCartMessage');
        let uniqueCount = document.getElementById('salesUniqueItemsCount');
        let totalCount = document.getElementById('salesTotalItemsCount');
        let grandTotal = document.getElementById('salesGrandTotal');
        
        // TODO: salesCart dizisindeki elemanları dönerek HTML oluştur ve container içine yazdır.
        // emptyMsg elementini sepet doluyken gizle ( display: none ), boşken göster ( display: flex ).
        // Toplam fiyat ve adetleri hesaplayıp ilgili span'lere yazdır.
        
        if (salesCart.length === 0) {
            // Boş sepet durumu
            // container.innerHTML = emptyMsg HTML'ini geri getir vs.
        } else {
            // Dolu sepet durumu, döngüyle ürünleri yazdır...
        }
    }

    // 5. Satışı Tamamlama (Veritabanına Kayıt)
    function completeSale() {
        if(salesCart.length === 0) {
            alert("Sepet boş! Ürün ekleyin.");
            return;
        }
        
        let note = document.getElementById('salesNote').value;
        
        // TODO: AJAX/Fetch ile salesCart dizisini ve notu PHP'ye gönder, veritabanına kaydet.
        // Veritabanında Satislar ve SatisDetay tablolarına kayıt atılmalı ve stok düşürülmeli.
        console.log("Satış Tamamlanıyor", { note: note, items: salesCart });
        
        alert("Satış başarıyla tamamlandı! (Bu simülasyondur)");
        // clearSalesCart();
    }
</script>

// footer is now included in index.php
