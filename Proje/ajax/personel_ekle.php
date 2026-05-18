<?php
require_once '../config.php';
header('Content-Type: application/json; charset=utf-8');

$ad = trim($_POST['ad'] ?? '');
$soyad = trim($_POST['soyad'] ?? '');
$kadi = trim($_POST['kadi'] ?? '');
$sifre = trim($_POST['sifre'] ?? '');
$yetki = trim($_POST['yetki'] ?? 'KASIYER');

if (empty($ad) || empty($soyad) || empty($kadi) || empty($sifre)) {
    echo json_encode(['basarili' => false, 'mesaj' => 'Lütfen tüm zorunlu alanları doldurun.']);
    exit;
}

$personelManager = new PersonelManager($db);
$sonuc = $personelManager->personelEkle($ad, $soyad, $kadi, $sifre, $yetki);

echo json_encode($sonuc);
?>
