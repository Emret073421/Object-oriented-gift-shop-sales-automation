<?php

// 1. Session'ı başlat (En üstte olmak zorunda, yoksa hata verir!)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// 1. Hata Raporlama (Geliştirme aşamasında hataları görmek için)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 3. Autoload (Otomatik Class Yükleyici)
// Bu fonksiyon, sen 'new UrunManager()' dediğin anda devreye girer
// ve ilgili dosyayı klasöründe bulup projeye dahil eder.
spl_autoload_register(function ($className) {
    // Class'ların aranacağı klasörler
    $folders = [
        'classes/',
        'classes/Models/',
        'classes/Managers/'
    ];

    foreach ($folders as $folder) {
        $file = __DIR__ . '/' . $folder . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// 4. Veritabanı Bağlantısını Başlat
$dbObj = new Veritabani(); // Makineyi (Veritabani Sınıfı) oluşturduk
$db = $dbObj->conn;        // İçindeki conn nesnesini çalıştırıp bağlantıyı $db'ye aldık

// Artık projenin her yerinde $db değişkenini kullanarak 
// veritabanı işlemlerini yapabilirsin.
?>