# 🎁 Nesne Yönelimli Hediyelik Eşya Satış Otomasyonu

Bu proje, Nesne Yönelimli Programlama (OOP) prensipleri kullanılarak PHP ve MySQL ile geliştirilmiş kapsamlı bir Hediyelik Eşya Satış ve Mağaza Yönetim Otomasyonudur.

---

## 📁 Proje Yapısı

Tüm ana uygulama dosyaları, sınıf yapıları ve arayüzler `Proje/` klasörü içerisinde yer almaktadır:

```text
Nesne/
├── README.md           # Proje genel bilgilendirme dosyası
└── Proje/              # Ana Web Uygulaması Dizini
    ├── classes/        # OOP Sınıfları (Modeller ve Yöneticiler)
    ├── inc/            # Ortak arayüz parçaları (Header, Footer)
    ├── config.php      # Veritabanı yapılandırma dosyası
    ├── database.sql    # Veritabanı tablo ve örnek veri dökümü
    ├── index.php       # Ana yönlendirici (Front Controller)
    └── ...             # Yönetim, satış, ürün ve raporlama modülleri
```

---

## 🚀 Kurulum ve Çalıştırma

1. Proje klasörünü yerel sunucunuzun (XAMPP/WAMP) `htdocs` veya `www` dizinine yerleştirin.
2. `phpMyAdmin` üzerinden yeni bir veritabanı oluşturun.
3. `Proje/database.sql` dosyasını oluşturduğunuz veritabanına içe aktarın (import).
4. `Proje/config.php` dosyasındaki veritabanı bağlantı bilgilerini kendi sisteminize göre güncelleyin.
5. Tarayıcınızdan `http://localhost/Nesne/Proje/` adresine giderek uygulamayı çalıştırın.
