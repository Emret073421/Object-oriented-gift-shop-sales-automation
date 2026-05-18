<?php 
$page_title = 'Finans ve Detaylı Raporlar';
?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    .view-toggle-btn {
        background: transparent;
        border: none;
        color: #64748b;
        font-weight: 600;
        padding: 10px 20px;
        border-radius: 8px;
        transition: all 0.3s ease;
    }
    .view-toggle-btn.active {
        background-color: #1e293b;
        color: white;
        box-shadow: 0 4px 12px rgba(30, 41, 59, 0.15);
    }
    .report-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.03);
    }
    .icon-box {
        width: 54px;
        height: 54px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
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
<div class="card report-card mb-4 bg-white border-0">
    <div class="card-body py-3 px-4 d-flex flex-wrap justify-content-between align-items-center">
        <div class="d-flex gap-2">
            <button class="view-toggle-btn active" id="btnAylik" onclick="switchView('aylik')">
                <i class="fa-solid fa-calendar-days me-2"></i> Aylık Görünüm
            </button>
            <button class="view-toggle-btn" id="btnYillik" onclick="switchView('yillik')">
                <i class="fa-solid fa-chart-pie me-2"></i> Yıllık Görünüm
            </button>
        </div>
        <div class="text-muted small mt-2 mt-md-0">
            <i class="fa-solid fa-circle-info me-1 text-primary"></i> Satışlar ve İadeler net ciroya otomatik yansıtılmaktadır.
        </div>
    </div>
</div>

<!-- ================= AYLIK GÖRÜNÜM ALANI ================= -->
<div id="aylikGorunum">
    <!-- Filtreler -->
    <div class="card report-card mb-4 bg-white border-0">
        <div class="card-body p-4">
            <div class="row g-3 align-items-center">
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted mb-1">Rapor Ayı</label>
                    <select class="form-select py-2 shadow-sm" id="aySecimi" onchange="fetchAylikVeri()">
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
                    <label class="form-label small fw-bold text-muted mb-1">Rapor Yılı</label>
                    <select class="form-select py-2 shadow-sm" id="yilSecimiAylik" onchange="fetchAylikVeri()">
                        <option value="2025">2025</option>
                        <option value="2026" selected>2026</option>
                        <option value="2027">2027</option>
                    </select>
                </div>
                <div class="col-md-6 text-md-end mt-4 mt-md-0">
                    <button class="btn btn-primary px-4 py-2 fw-bold shadow-sm" onclick="fetchAylikVeri()">
                        <i class="fa-solid fa-rotate me-2"></i> Verileri Yenile
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- İstatistik Kartları -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card report-card h-100 bg-white border-0 border-start border-4 border-primary shadow-sm">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="icon-box blue me-4"><i class="fa-solid fa-wallet"></i></div>
                    <div>
                        <div class="text-muted small fw-bold mb-1" id="aylikCiroBaslik">MAYIS AYI NET CİRO</div>
                        <h3 class="fw-bold mb-0 text-dark" id="aylikCiroDeger">0,00 ₺</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card report-card h-100 bg-white border-0 border-start border-4 border-warning shadow-sm">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="icon-box yellow me-4"><i class="fa-solid fa-box"></i></div>
                    <div>
                        <div class="text-muted small fw-bold mb-1" id="aylikUrunBaslik">MAYIS AYI SATILAN ÜRÜN</div>
                        <h3 class="fw-bold mb-0 text-dark" id="aylikUrunDeger">0 Adet</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card report-card h-100 bg-white border-0 border-start border-4 border-success shadow-sm">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="icon-box green me-4"><i class="fa-solid fa-cart-shopping"></i></div>
                    <div>
                        <div class="text-muted small fw-bold mb-1">TAMAMLANAN SİPARİŞ</div>
                        <h3 class="fw-bold mb-0 text-dark" id="aylikSiparisDeger">0 Adet</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Satış Özeti Tablosu -->
    <div class="card report-card bg-white border-0 shadow-sm">
        <div class="card-body p-4">
            <h5 class="fw-bold text-dark mb-4" id="aylikTabloBaslik"><i class="fa-solid fa-list-check text-primary me-2"></i> Mayıs Ayı Satış ve İade Özeti</h5>
            <div class="table-responsive" style="max-height: 450px; overflow-y: auto;">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light sticky-top">
                        <tr>
                            <th class="table-header border-0 ps-4">TARİH / FİŞ KODU</th>
                            <th class="table-header border-0">ÜRÜN ADI / TİP</th>
                            <th class="table-header border-0 text-center">ADET</th>
                            <th class="table-header border-0 text-end">BİRİM FİYAT</th>
                            <th class="table-header border-0 text-end pe-4">TOPLAM TUTAR</th>
                        </tr>
                    </thead>
                    <tbody id="aylikTabloGovde">
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">Yükleniyor...</td>
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
    <div class="card report-card mb-4 bg-white border-0 shadow-sm">
        <div class="card-body p-4">
            <div class="row g-3 align-items-center">
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted mb-1">Rapor Yılı</label>
                    <select class="form-select py-2 shadow-sm" id="yilSecimiYillik" onchange="fetchYillikVeri()">
                        <option value="2025">2025 Yılı</option>
                        <option value="2026" selected>2026 Yılı</option>
                        <option value="2027">2027 Yılı</option>
                    </select>
                </div>
                <div class="col-md-9 text-md-end mt-4 mt-md-0">
                    <button class="btn btn-primary px-4 py-2 fw-bold shadow-sm" onclick="fetchYillikVeri()">
                        <i class="fa-solid fa-rotate me-2"></i> Verileri Yenile
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- İstatistik Kartları -->
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card report-card h-100 bg-white border-0 border-start border-4 border-primary shadow-sm">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="icon-box blue me-4"><i class="fa-solid fa-chart-line"></i></div>
                    <div>
                        <div class="text-muted small fw-bold mb-1">YILLIK NET HASILAT (SATIS - IADE)</div>
                        <h2 class="fw-bold mb-0 text-primary" id="yillikCiroDeger">0,00 ₺</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card report-card h-100 bg-white border-0 border-start border-4 border-warning shadow-sm">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="icon-box yellow me-4"><i class="fa-solid fa-award"></i></div>
                    <div>
                        <div class="text-muted small fw-bold mb-1">YILIN EN ÇOK SATILAN ÜRÜNÜ</div>
                        <h5 class="fw-bold mb-0 text-dark" id="yillikEnCokSatan">Yükleniyor...</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Aylık Ciro Dağılımı Tablosu -->
    <div class="card report-card bg-white border-0 shadow-sm">
        <div class="card-body p-4">
            <h5 class="fw-bold text-dark mb-4" id="yillikTabloBaslik"><i class="fa-solid fa-calendar-grid-58 text-success me-2"></i> 2026 Yılı Aylık Hasılat Dağılımı</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="table-header border-0 ps-4">AY</th>
                            <th class="table-header border-0 text-center">TAMAMLANAN SİPARİŞ</th>
                            <th class="table-header border-0 text-center">SATILAN ÜRÜN (ADET)</th>
                            <th class="table-header border-0 text-end pe-4">NET AYLIK CİRO</th>
                        </tr>
                    </thead>
                    <tbody id="yillikTabloGovde">
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">Yükleniyor...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Sayfa açıldığında şu anki ayı seçili yapalım
    let currentMonth = new Date().getMonth() + 1;
    document.getElementById('aySecimi').value = currentMonth;
    fetchAylikVeri();
});

// 1. Görünüm Değiştirme (Aylık / Yıllık)
function switchView(viewName) {
    document.getElementById('btnAylik').classList.remove('active');
    document.getElementById('btnYillik').classList.remove('active');
    
    document.getElementById('aylikGorunum').style.display = 'none';
    document.getElementById('yillikGorunum').style.display = 'none';

    if (viewName === 'aylik') {
        document.getElementById('btnAylik').classList.add('active');
        document.getElementById('aylikGorunum').style.display = 'block';
        fetchAylikVeri();
    } else {
        document.getElementById('btnYillik').classList.add('active');
        document.getElementById('yillikGorunum').style.display = 'block';
        fetchYillikVeri();
    }
}

// 2. Aylık Verileri Çekme
function fetchAylikVeri() {
    let aySelect = document.getElementById('aySecimi');
    let ayAdi = aySelect.options[aySelect.selectedIndex].text;
    let yil = document.getElementById('yilSecimiAylik').value;
    let ayKodu = aySelect.value;

    document.getElementById('aylikCiroBaslik').innerText = `${ayAdi.toUpperCase()} AYI NET CİRO`;
    document.getElementById('aylikUrunBaslik').innerText = `${ayAdi.toUpperCase()} AYI SATILAN ÜRÜN`;
    document.getElementById('aylikTabloBaslik').innerHTML = `<i class="fa-solid fa-list-check text-primary me-2"></i> ${ayAdi} Ayı Satış ve İade Özeti`;

    let formData = new FormData();
    formData.append('ay', ayKodu);
    formData.append('yil', yil);

    fetch("ajax/rapor_aylik_getir.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.basarili) {
            document.getElementById('aylikCiroDeger').innerText = data.net_ciro;
            document.getElementById('aylikUrunDeger').innerText = data.satilan_adet;
            document.getElementById('aylikSiparisDeger').innerText = data.siparis_adet;
            document.getElementById('aylikTabloGovde').innerHTML = data.tablo_html;
        } else {
            Swal.fire('Hata!', data.mesaj, 'error');
        }
    })
    .catch(error => console.error("Aylık rapor hatası:", error));
}

// 3. Yıllık Verileri Çekme
function fetchYillikVeri() {
    let yil = document.getElementById('yilSecimiYillik').value;

    document.getElementById('yillikTabloBaslik').innerHTML = `<i class="fa-solid fa-calendar-grid-58 text-success me-2"></i> ${yil} Yılı Aylık Hasılat Dağılımı`;

    let formData = new FormData();
    formData.append('yil', yil);

    fetch("ajax/rapor_yillik_getir.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.basarili) {
            document.getElementById('yillikCiroDeger').innerText = data.yillik_ciro;
            document.getElementById('yillikEnCokSatan').innerText = data.en_cok_satan;
            document.getElementById('yillikTabloGovde').innerHTML = data.tablo_html;
        } else {
            Swal.fire('Hata!', data.mesaj, 'error');
        }
    })
    .catch(error => console.error("Yıllık rapor hatası:", error));
}
</script>
