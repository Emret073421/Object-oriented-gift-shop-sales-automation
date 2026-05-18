<?php
require_once '../config.php';
header('Content-Type: application/json; charset=utf-8');

$kadi = trim($_POST['kadi'] ?? '');
$sifre = trim($_POST['sifre'] ?? '');
$hedefSayfa = trim($_POST['hedef_sayfa'] ?? '');
$gerekliYetki = trim($_POST['gerekli_yetki'] ?? 'YONETICI');

if (empty($kadi) || empty($sifre)) {
    echo json_encode(['basarili' => false, 'mesaj' => 'Kullanıcı adı ve şifre boş olamaz.']);
    exit;
}

$personelManager = new PersonelManager($db);
$yontici = $personelManager->giris($kadi, $sifre);

if ($yontici) {
    $yYetki = strtoupper($yontici->getYetki());
    
    $yetkili = false;
    if ($gerekliYetki === 'YONETICI' && $yYetki === 'YONETICI') {
        $yetkili = true;
    } elseif ($gerekliYetki === 'PERSONEL' && in_array($yYetki, ['YONETICI', 'PERSONEL'])) {
        $yetkili = true;
    } elseif ($gerekliYetki === 'KASIYER') {
        $yetkili = true;
    }

    if ($yetkili) {
        // Anlık izni session'a kaydediyoruz
        $_SESSION['anlik_izin_sayfa'] = $hedefSayfa;
        echo json_encode(['basarili' => true, 'mesaj' => 'Yetki onayı başarılı. Yönlendiriliyorsunuz...']);
    } else {
        echo json_encode(['basarili' => false, 'mesaj' => 'Giriş yapan personelin bu işlem için yeterli yetkisi bulunmuyor.']);
    }
} else {
    echo json_encode(['basarili' => false, 'mesaj' => 'Kullanıcı adı veya şifre hatalı.']);
}
?>
