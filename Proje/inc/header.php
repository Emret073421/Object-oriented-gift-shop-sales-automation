<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hediyelik Otomasyonu - Admin Panel</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome (İkonlar için) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f6f9; }
        #wrapper { display: flex; width: 100%; height: 100vh; overflow: hidden; }
        #sidebar { width: 260px; background-color: #2c3e50; color: #fff; display: flex; flex-direction: column; transition: all 0.3s; box-shadow: 2px 0 5px rgba(0,0,0,0.1); z-index: 10; }
        #sidebar .sidebar-header { padding: 25px 20px; background-color: #1a252f; text-align: center; font-size: 1.4rem; font-weight: 600; letter-spacing: 1px; }
        #sidebar ul.components { padding: 15px 0; flex-grow: 1; overflow-y: auto; }
        #sidebar ul li a { padding: 15px 25px; font-size: 1.05em; display: flex; align-items: center; color: #bdc3c7; text-decoration: none; transition: 0.2s; cursor: pointer; }
        #sidebar ul li a:hover, #sidebar ul li.active > a { color: #fff; background: #34495e; border-left: 5px solid #3498db; }
        #sidebar ul li a i { margin-right: 15px; width: 20px; text-align: center; font-size: 1.1em; }
        #content { flex-grow: 1; display: flex; flex-direction: column; overflow-y: auto; }
        .navbar-custom { background-color: #fff; box-shadow: 0 2px 10px rgba(0,0,0,0.05); padding: 15px 30px; }
        .card-custom { border: none; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); transition: transform 0.2s ease-in-out; }
        .card-custom:hover { transform: translateY(-5px); }
        .stat-icon { opacity: 0.2; position: absolute; right: 20px; top: 20px; font-size: 4rem; }
        .card-body { position: relative; overflow: hidden; }
    </style>
</head>
<body>
<?php
// Hangi sayfada olduğumuzu $_GET['sayfa'] parametresinden buluyoruz
$current_page = $_GET['sayfa'] ?? 'dashboard';
?>
<div id="wrapper">
    <!-- SOL MENÜ (SIDEBAR) -->
    <nav id="sidebar">
        <div class="sidebar-header">
            <i class="fa-solid fa-gift text-info me-2"></i>HediyeOto
        </div>
        <ul class="list-unstyled components">
            <li class="<?= ($current_page == 'dashboard') ? 'active' : '' ?>">
                <a onclick="checkAuthAndNavigate(event, 'index.php?sayfa=dashboard', 'dashboard', 'YONETICI')"><i class="fa-solid fa-chart-pie"></i> Dashboard</a>
            </li>
            <li class="<?= ($current_page == 'satis') ? 'active' : '' ?>">
                <a onclick="checkAuthAndNavigate(event, 'index.php?sayfa=satis', 'satis', 'KASIYER')"><i class="fa-solid fa-cart-arrow-down"></i> Hızlı Satış / Değişim</a>
            </li>
            <li class="<?= ($current_page == 'iade') ? 'active' : '' ?>">
                <a onclick="checkAuthAndNavigate(event, 'index.php?sayfa=iade', 'iade', 'YONETICI')"><i class="fa-solid fa-rotate-left"></i> Ürün İade İşlemleri</a>
            </li>
            <li class="<?= ($current_page == 'urunler') ? 'active' : '' ?>">
                <a onclick="checkAuthAndNavigate(event, 'index.php?sayfa=urunler', 'urunler', 'YONETICI')"><i class="fa-solid fa-boxes-stacked"></i> Ürün Yönetimi</a>
            </li>
            <li class="<?= ($current_page == 'kategoriler') ? 'active' : '' ?>">
                <a onclick="checkAuthAndNavigate(event, 'index.php?sayfa=kategoriler', 'kategoriler', 'YONETICI')"><i class="fa-solid fa-tags"></i> Kategoriler</a>
            </li>
            <li class="<?= ($current_page == 'alislar') ? 'active' : '' ?>">
                <a onclick="checkAuthAndNavigate(event, 'index.php?sayfa=alislar', 'alislar', 'PERSONEL')"><i class="fa-solid fa-truck-ramp-box"></i> Mal Alış (Tedarik)</a>
            </li>
            <li class="<?= ($current_page == 'raporlar') ? 'active' : '' ?>">
                <a onclick="checkAuthAndNavigate(event, 'index.php?sayfa=raporlar', 'raporlar', 'YONETICI')"><i class="fa-solid fa-file-invoice-dollar"></i> Finans ve Raporlar</a>
            </li>
            <li class="<?= ($current_page == 'personel') ? 'active' : '' ?>">
                <a onclick="checkAuthAndNavigate(event, 'index.php?sayfa=personel', 'personel', 'YONETICI')"><i class="fa-solid fa-users-gear"></i> Personel Yönetimi</a>
            </li>
        </ul>
        <div class="p-4 border-top border-secondary">
            <div class="small text-muted mb-2">Giriş Yapan:</div>
            <div class="d-flex align-items-center mb-3">
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['personel_adi'] ?? 'Misafir'); ?>&background=3498db&color=fff&rounded=true" width="40" class="me-2">
                <div>
                    <strong><?php echo htmlspecialchars($_SESSION['personel_adi'] ?? 'Misafir'); ?></strong><br>
                    <small class="text-info"><?php echo ucfirst(strtolower($_SESSION['personel_yetki'] ?? 'Personel')); ?></small>
                </div>
            </div>
            <button type="button" class="btn btn-outline-danger w-100 btn-sm" onclick="logout()">
                <i class="fa-solid fa-right-from-bracket me-2"></i> Çıkış Yap
            </button>
        </div>
    </nav>

    <!-- SAĞ İÇERİK (CONTENT) -->
    <div id="content">
        <!-- ÜST BİLGİ ÇUBUĞU (NAVBAR) -->
        <nav class="navbar navbar-expand-lg navbar-light navbar-custom">
            <div class="container-fluid">
                <h5 class="mb-0 text-dark fw-bold"><?= isset($page_title) ? $page_title : 'Sayfa' ?></h5>
                
                <!-- Sağ Üst Araçlar -->
                <div class="d-flex align-items-center">
                    <a href="index.php?sayfa=satis" class="btn btn-primary me-3 shadow-sm rounded-pill px-4">
                        <i class="fa-solid fa-plus me-2"></i> Yeni İşlem
                    </a>
                    <span class="text-muted"><i class="fa-regular fa-calendar me-2"></i> <?= date('d.m.Y') ?></span>
                </div>
            </div>
        </nav>

        <!-- ANA İÇERİK ALANI (Tüm sayfalara buraya içerik gelecek) -->
        <div class="container-fluid p-4">

         <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
         <script>
            const CURRENT_USER_YETKI = "<?php echo strtoupper($_SESSION['personel_yetki'] ?? 'PERSONEL'); ?>";

            function checkAuthAndNavigate(event, targetUrl, hedefSayfa, gerekliYetki) {
                if (event) event.preventDefault();

                let yetkili = false;
                if (CURRENT_USER_YETKI === 'YONETICI') {
                    yetkili = true;
                } else if (CURRENT_USER_YETKI === 'PERSONEL' && (gerekliYetki === 'PERSONEL' || gerekliYetki === 'KASIYER')) {
                    yetkili = true;
                } else if (CURRENT_USER_YETKI === 'KASIYER' && gerekliYetki === 'KASIYER') {
                    yetkili = true;
                }

                if (yetkili) {
                    window.location.href = targetUrl;
                    return;
                }

                // Yetkisi yoksa anlık giriş penceresi açalım!
                Swal.fire({
                    title: '<i class="fa-solid fa-user-lock text-warning mb-2 fa-2x"></i><br><span class="fs-4">Yönetici İzni Gerekiyor</span>',
                    html: `
                        <p class="text-muted small mb-4">Bu ekrana erişmek için <b>${gerekliYetki === 'YONETICI' ? 'Yönetici' : 'Yetkili Personel'}</b> onayı gerekmektedir. Lütfen anlık yetkili girişi yapın.</p>
                        <div class="text-start mb-3">
                            <label class="form-label small fw-bold text-secondary">Yetkili Kullanıcı Adı:</label>
                            <input type="text" id="anlikKadi" class="form-control py-2 shadow-sm" placeholder="Örn: admin" required autocomplete="off">
                        </div>
                        <div class="text-start mb-2">
                            <label class="form-label small fw-bold text-secondary">Şifre:</label>
                            <input type="password" id="anlikSifre" class="form-control py-2 shadow-sm" placeholder="••••••••" required autocomplete="off">
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: '<i class="fa-solid fa-unlock me-1"></i> Anlık Giriş Yap',
                    cancelButtonText: 'İptal',
                    focusConfirm: false,
                    preConfirm: () => {
                        let kadi = document.getElementById('anlikKadi').value;
                        let sifre = document.getElementById('anlikSifre').value;

                        if (!kadi || !sifre) {
                            Swal.showValidationMessage('Lütfen kullanıcı adı ve şifre girin.');
                            return false;
                        }

                        let formData = new FormData();
                        formData.append('kadi', kadi);
                        formData.append('sifre', sifre);
                        formData.append('hedef_sayfa', hedefSayfa);
                        formData.append('gerekli_yetki', gerekliYetki);

                        return fetch("ajax/anlik_yetki_kontrol.php", {
                            method: "POST",
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (!data.basarili) {
                                throw new Error(data.mesaj);
                            }
                            return data;
                        })
                        .catch(error => {
                            Swal.showValidationMessage(error.message);
                        });
                    }
                }).then((result) => {
                    if (result.isConfirmed && result.value.basarili) {
                        Swal.fire({
                            title: 'Erişim Onaylandı!',
                            text: 'Sayfaya yönlendiriliyorsunuz...',
                            icon: 'success',
                            timer: 1000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = targetUrl;
                        });
                    }
                });
            }

            function logout() {
                window.location.href = "logout.php";
            }
         </script>