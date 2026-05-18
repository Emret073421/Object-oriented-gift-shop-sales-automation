<?php 
$page_title = 'Finans ve Detaylı Raporlar';
// header is now included in index.php
?>

<style>
    /* Özelleştirilmiş Tasarım Sınıfları */
    .view-toggle-btn {
        background: transparent;
        border: none;
        color: #64748b;
        font-weight: 600;
        padding: 8px 16px;
        border-radius: 8px;
        transition: all 0.3s ease;
    }
    .view-toggle-btn.active {
        background-color: #1e293b;
        color: white;
    }
    .report-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.02);
    }
    .icon-box {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
    .icon-box.blue { background-color: #eff6ff; color: #3b82f6; }
    .icon-box.yellow { background-color: #fefce8; color: #eab308; }
    .icon-box.green { background-color: #f0fdf4; color: #22c55e; }
    
    .table-header {
        font-size: 0.75rem;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
</style>

<!-- GÖRÜNÜM SEÇİCİ -->
<div class="card report-card mb-4">
    <div class="card-body py-3">
        <div class="d-flex gap-2">
            <button class="view-toggle-btn active" id="btnAylik" onclick="switchView('aylik')">Aylık Görünüm</button>
            <button class="view-toggle-btn" id="btnYillik" onclick="switchView('yillik')">Yıllık Görünüm</button>
        </div>
    </div>
</div>

<!-- ================= AYLIK GÖRÜNÜM ALANI ================= -->
<div id="aylikGorunum">
    <!-- Filtreler -->
    <div class="row mb-4">
        <div class="col-md-3">
            <select class="form-select" id="aySecimi" onchange="fetchAylikVeri()">
                <option value="1">Ocak</option>
                <option value="2">Şubat</option>
                <option value="3">Mart</option>
                <option value="4">Nisan</option>
                <option value="5" selected>Mayıs</option>
                <option value="6">Haziran</option>
                <option value="7">Temmuz</option>
                <option value="8">Ağustos</option>
                <option value="9">Eylül</option>
                <option value="10">Ekim</option>
                <option value="11">Kasım</option>
                <option value="12">Aralık</option>
            </select>
        </div>
        <div class="col-md-3">
            <select class="form-select" id="yilSecimiAylik" onchange="fetchAylikVeri()">
                <option value="2025">2025</option>
                <option value="2026" selected>2026</option>
            </select>
        </div>
    </div>

    <!-- İstatistik Kartları -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card report-card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="icon-box blue me-3"><i class="fa-solid fa-wallet"></i></div>
                    <div>
                        <div class="text-muted small fw-bold mb-1" id="aylikCiroBaslik">MAYIS AYI TOPLAM CİRO</div>
                        <h3 class="fw-bold mb-0" id="aylikCiroDeger">0,00 ₺</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card report-card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="icon-box yellow me-3"><i class="fa-solid fa-box"></i></div>
                    <div>
                        <div class="text-muted small fw-bold mb-1" id="aylikUrunBaslik">MAYIS AYI SATILAN ÜRÜN</div>
                        <h3 class="fw-bold mb-0" id="aylikUrunDeger">0 Adet</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card report-card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="icon-box green me-3"><i class="fa-solid fa-cart-shopping"></i></div>
                    <div>
                        <div class="text-muted small fw-bold mb-1">TESLİM EDİLMİŞ SİPARİŞ</div>
                        <h3 class="fw-bold mb-0" id="aylikSiparisDeger">0 Adet</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Satış Özeti Tablosu -->
    <div class="card report-card">
        <div class="card-body">
            <h6 class="fw-bold mb-4" id="aylikTabloBaslik">Mayıs Ayı Satış Özeti</h6>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="table-header border-0">TARİH</th>
                            <th class="table-header border-0">ÜRÜN ADI</th>
                            <th class="table-header border-0 text-center">ADET</th>
                            <th class="table-header border-0 text-end">BİRİM FİYAT</th>
                            <th class="table-header border-0 text-end">TOPLAM TUTAR</th>
                        </tr>
                    </thead>
                    <tbody id="aylikTabloGovde">
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Bu aya ait satış bulunamadı.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ================= YILLIK GÖRÜNÜM ALANI ================= -->
<div id="yillikGorunum" style="display: none;">
    <!-- Filtreler -->
    <div class="row mb-4">
        <div class="col-md-3">
            <select class="form-select" id="yilSecimiYillik" onchange="fetchYillikVeri()">
                <option value="2025">2025 Yılı</option>
                <option value="2026" selected>2026 Yılı</option>
            </select>
        </div>
    </div>

    <!-- İstatistik Kartları -->
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card report-card h-100">
                <div class="card-body d-flex align-items-center border-start border-4 border-primary rounded">
                    <div class="icon-box blue me-3" style="background-color: #eff6ff;"><i class="fa-solid fa-chart-line"></i></div>
                    <div>
                        <div class="text-muted small fw-bold mb-1">YILLIK TOPLAM HASILAT</div>
                        <h3 class="fw-bold mb-0" id="yillikCiroDeger">88.900,00 ₺</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card report-card h-100">
                <div class="card-body d-flex align-items-center border-start border-4 border-warning rounded">
                    <div class="icon-box yellow me-3" style="background-color: #fefce8;"><i class="fa-solid fa-award"></i></div>
                    <div>
                        <div class="text-muted small fw-bold mb-1">YILIN EN ÇOK SATILANI</div>
                        <h6 class="fw-bold mb-0" id="yillikEnCokSatan">iPhone 15 Pro Max 256 GB - Naturel Titanyum (1 Adet)</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Aylık Ciro Dağılımı Tablosu -->
    <div class="card report-card">
        <div class="card-body">
            <h6 class="fw-bold mb-4" id="yillikTabloBaslik">2026 Yılı Aylık Ciro Dağılımı</h6>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="table-header border-0">AY</th>
                            <th class="table-header border-0 text-center">TAMAMLANAN SİPARİŞ</th>
                            <th class="table-header border-0 text-center">SATILAN ÜRÜN (ADET)</th>
                            <th class="table-header border-0 text-end">AYLIK CİRO</th>
                        </tr>
                    </thead>
                    <tbody id="yillikTabloGovde">
                        <tr>
                            <td>Ocak</td><td class="text-center">-</td><td class="text-center">-</td><td class="text-end">-</td>
                        </tr>
                        <tr>
                            <td>Şubat</td><td class="text-center">-</td><td class="text-center">-</td><td class="text-end">-</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Mart</td><td class="text-center">1</td><td class="text-center">2</td><td class="text-end fw-bold text-success">88.900,00 ₺</td>
                        </tr>
                        <tr>
                            <td>Nisan</td><td class="text-center">-</td><td class="text-center">-</td><td class="text-end">-</td>
                        </tr>
                        <!-- Diğer aylar JS ile doldurulacak -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    /* =========================================================================
       RAPORLAR SAYFASI - JAVASCRIPT İSKELETİ
       Aşağıdaki JS kodları ile yeni oluşturduğun IslemManager sınıfındaki
       tarihFiltrele(), encokSatisYapanUrunler() vb. metodlarına AJAX istekleri atabilirsin.
    ========================================================================= */

    // 1. Görünüm Değiştirme (Aylık / Yıllık)
    function switchView(viewName) {
        // Buton stillerini ayarla
        document.getElementById('btnAylik').classList.remove('active');
        document.getElementById('btnYillik').classList.remove('active');
        
        // İçerikleri gizle
        document.getElementById('aylikGorunum').style.display = 'none';
        document.getElementById('yillikGorunum').style.display = 'none';

        if(viewName === 'aylik') {
            document.getElementById('btnAylik').classList.add('active');
            document.getElementById('aylikGorunum').style.display = 'block';
            fetchAylikVeri();
        } else {
            document.getElementById('btnYillik').classList.add('active');
            document.getElementById('yillikGorunum').style.display = 'block';
            fetchYillikVeri();
        }
    }

    // 2. Aylık Verileri Çekme ve Arayüzü Güncelleme
    function fetchAylikVeri() {
        let aySelect = document.getElementById('aySecimi');
        let ayAdi = aySelect.options[aySelect.selectedIndex].text;
        let yil = document.getElementById('yilSecimiAylik').value;
        let ayKodu = aySelect.value;

        // Başlıkları Seçilen Aya Göre Dinamik Güncelle
        document.getElementById('aylikCiroBaslik').innerText = `${ayAdi.toUpperCase()} AYI TOPLAM CİRO`;
        document.getElementById('aylikUrunBaslik').innerText = `${ayAdi.toUpperCase()} AYI SATILAN ÜRÜN`;
        document.getElementById('aylikTabloBaslik').innerText = `${ayAdi} Ayı Satış Özeti`;

        console.log(`Aylık veri isteniyor: Yıl: ${yil}, Ay: ${ayKodu}`);
        
        // TODO: AJAX/Fetch ile kendi yazdığın IslemManager endpointine istek at.
        // Örn: fetch('api/rapor_aylik.php?ay='+ayKodu+'&yil='+yil)
        // Gelen JSON verisine göre aşağıdaki alanları güncelle:
        // document.getElementById('aylikCiroDeger').innerText = veri.toplamCiro + " ₺";
        // document.getElementById('aylikUrunDeger').innerText = veri.satilanAdet + " Adet";
        // document.getElementById('aylikSiparisDeger').innerText = veri.siparisAdet + " Adet";
        // document.getElementById('aylikTabloGovde').innerHTML = '... tablonun HTML satırları (<tr>) ...';
    }

    // 3. Yıllık Verileri Çekme ve Arayüzü Güncelleme
    function fetchYillikVeri() {
        let yil = document.getElementById('yilSecimiYillik').value;

        // Başlık Güncelleme
        document.getElementById('yillikTabloBaslik').innerText = `${yil} Yılı Aylık Ciro Dağılımı`;

        console.log(`Yıllık veri isteniyor: Yıl: ${yil}`);

        // TODO: AJAX/Fetch ile kendi yazdığın IslemManager endpointine istek at.
        // Gelen JSON verisine göre aşağıdaki alanları güncelle:
        // document.getElementById('yillikCiroDeger').innerText = veri.toplamYillikCiro + " ₺";
        // document.getElementById('yillikEnCokSatan').innerText = veri.enCokSatanUrun;
        // document.getElementById('yillikTabloGovde').innerHTML = '... 12 ayın HTML satırları (<tr>) ...';
    }

    // Sayfa yüklendiğinde varsayılan olarak Aylık görünüm verilerini çekmek için:
    document.addEventListener('DOMContentLoaded', () => {
        fetchAylikVeri();
    });

</script>

// footer is now included in index.php
