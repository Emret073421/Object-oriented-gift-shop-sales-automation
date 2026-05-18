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

// %100 OOP: Personel model nesnemizi üretip setterlar ile dolduruyoruz
$personel = new Personel();
$personel->setAd($ad);
$personel->setSoyad($soyad);
$personel->setKullaniciAdi($kadi);
$personel->setSifre($sifre);
$personel->setYetki($yetki);

// PersonelManager nesnesini çağırıp, Model nesnemizi teslim ediyoruz
$personelManager = new PersonelManager($db);
$sonuc = $personelManager->personelEkle($personel);

echo json_encode($sonuc);
?>
