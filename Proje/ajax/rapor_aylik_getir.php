<?php
require_once '../config.php';
header('Content-Type: application/json; charset=utf-8');

$ay = (int)($_POST['ay'] ?? date('n'));
$yil = (int)($_POST['yil'] ?? date('Y'));

// %100 OOP: Rapor model nesnemizi üretip setterlar ile dolduruyoruz
$rapor = new Rapor();
$rapor->setAy($ay);
$rapor->setYil($yil);

// RaporManager nesnesini çağırıp, Model nesnemizi teslim ediyoruz
$raporManager = new RaporManager($db);
$sonuc = $raporManager->getirAylik($rapor);

echo json_encode($sonuc);
?>
