<?php
require_once 'config.php';

// Eğer kullanıcı zaten giriş yapmışsa, login sayfasına girmesini engelle ve dashboard'a yönlendir
if (isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

// Form gönderilmişse giriş işlemlerini burada yapabilirsin
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $formKullanici = $_POST['username'] ?? '';
    $formSifre     = $_POST['password'] ?? '';

    // Beyin sınıfı çağrılıyor (Autoload bunu tık diye bulup yükleyecek)
    $personelManager = new PersonelManager($db);
    $girisDurumu = $personelManager->giris($formKullanici, $formSifre);

    if ($girisDurumu !== false) {
        // Giriş Başarılı! Kapsüllenmiş ID'yi session'a güvenle yazıyoruz
        $_SESSION['personel_id'] = $girisDurumu->getId();
        $_SESSION['personel_adi'] = $girisDurumu->getAdSoyad();
        $_SESSION['personel_yetki'] = $girisDurumu->getYetki();
        $_SESSION['login'] = true;
        // PRG DESENİ: Çift gönderimi engellemek için sayfa güvenle kendine yönlendiriliyor
        header("Location: index.php");
        exit; // PHP çalışmasını kesinlikle durduruyoruz!
    } else {
        $hataMesaji = "Kullanıcı adı veya şifre hatalı!";
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HediyeOto - Yönetim Paneli Girişi</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            /* Modern, canlı ve yumuşak bir degrade arka plan */
            background: linear-gradient(135deg, #f6d365 0%, #fda085 100%);
            overflow: hidden;
        }

        /* Arka plan süslemeleri (Hareketli şekiller) */
        .shape {
            position: absolute;
            filter: blur(80px);
            z-index: 0;
            opacity: 0.6;
            animation: float 10s infinite ease-in-out alternate;
        }
        .shape-1 {
            width: 400px; height: 400px;
            background: #ff9a9e;
            top: -100px; left: -100px;
            border-radius: 50%;
        }
        .shape-2 {
            width: 500px; height: 500px;
            background: #fecfef;
            bottom: -150px; right: -100px;
            border-radius: 50%;
            animation-delay: -5s;
        }

        @keyframes float {
            0% { transform: translateY(0px) scale(1); }
            100% { transform: translateY(30px) scale(1.1); }
        }

        .login-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 3rem 2.5rem;
            width: 100%;
            max-width: 420px;
            z-index: 1;
            transform: translateY(20px);
            opacity: 0;
            animation: slideUp 0.6s ease-out forwards;
        }

        @keyframes slideUp {
            to { transform: translateY(0); opacity: 1; }
        }

        .brand-logo {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #ff758c 0%, #ff7eb3 100%);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
            margin: 0 auto 1.5rem auto;
            box-shadow: 0 10px 20px rgba(255, 117, 140, 0.3);
            transform: rotate(-10deg);
            transition: transform 0.3s ease;
        }

        .brand-logo:hover {
            transform: rotate(0deg) scale(1.05);
        }

        .login-title {
            font-weight: 700;
            color: #2d3748;
            text-align: center;
            margin-bottom: 0.5rem;
            font-size: 1.5rem;
        }

        .login-subtitle {
            color: #718096;
            text-align: center;
            margin-bottom: 2.5rem;
            font-size: 0.95rem;
        }

        .form-floating > .form-control,
        .form-floating > .form-select {
            height: calc(3.5rem + 2px);
            padding: 1rem 0.75rem;
            background-color: rgba(255, 255, 255, 0.9);
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            color: #2d3748;
            font-weight: 500;
        }

        .form-floating > .form-control:focus {
            border-color: #ff758c;
            box-shadow: 0 0 0 4px rgba(255, 117, 140, 0.1);
            background-color: #fff;
        }

        .form-floating > label {
            color: #a0aec0;
            font-weight: 400;
            padding: 1rem 1rem;
        }

        .input-icon {
            position: absolute;
            top: 50%;
            right: 15px;
            transform: translateY(-50%);
            color: #a0aec0;
            z-index: 10;
        }

        .btn-login {
            background: linear-gradient(135deg, #ff758c 0%, #ff7eb3 100%);
            border: none;
            color: white;
            padding: 1rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1.1rem;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            box-shadow: 0 10px 20px rgba(255, 117, 140, 0.3);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 25px rgba(255, 117, 140, 0.4);
            color: white;
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .forgot-password {
            color: #718096;
            font-size: 0.85rem;
            text-decoration: none;
            transition: color 0.2s;
            display: inline-block;
            margin-top: 1.5rem;
        }

        .forgot-password:hover {
            color: #ff758c;
        }
    </style>
</head>
<body>

    <!-- Arka plan animasyonları -->
    <div class="shape shape-1"></div>
    <div class="shape shape-2"></div>

    <!-- Giriş Kartı -->
    <div class="login-card">
        <div class="brand-logo">
            <i class="fa-solid fa-gift"></i>
        </div>
        <h2 class="login-title">HediyeOto'ya Hoş Geldiniz</h2>
        <p class="login-subtitle">Lütfen yönetici bilgilerinizi giriniz.</p>

        <?php if (!empty($hataMesaji)): ?>
            <div class="alert alert-danger text-center" role="alert">
                <?= htmlspecialchars($hataMesaji) ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            
            <!-- Kullanıcı Adı -->
            <div class="form-floating mb-3 position-relative">
                <input type="text" class="form-control" id="username" name="username" placeholder="Kullanıcı Adı" required autocomplete="off">
                <label for="username">Kullanıcı Adı</label>
                <i class="fa-solid fa-user input-icon"></i>
            </div>
            
            <!-- Şifre -->
            <div class="form-floating mb-4 position-relative">
                <input type="password" class="form-control" id="password" name="password" placeholder="Şifre" required>
                <label for="password">Şifre</label>
                <i class="fa-solid fa-lock input-icon"></i>
            </div>
            
            <!-- Beni Hatırla & Buton -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="" id="rememberMe">
                    <label class="form-check-label text-muted small" for="rememberMe">
                        Beni Hatırla
                    </label>
                </div>
            </div>

            <button type="submit" class="btn btn-login w-100">
                Giriş Yap <i class="fa-solid fa-arrow-right ms-2"></i>
            </button>

        </form>

        <div class="text-center">
            <a href="#" class="forgot-password">Şifrenizi mi unuttunuz?</a>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

