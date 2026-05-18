CREATE DATABASE IF NOT EXISTS hediyelik_otomasyon CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE hediyelik_otomasyon;

-- 1. Kategoriler Tablosu
CREATE TABLE IF NOT EXISTS kategoriler (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ad VARCHAR(100) NOT NULL,
    aciklama TEXT,
    durum TINYINT(1) DEFAULT 1,
    olusturulma_tarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Personeller Tablosu
CREATE TABLE IF NOT EXISTS personeller (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ad VARCHAR(50) NOT NULL,
    soyad VARCHAR(50) NOT NULL,
    telefon VARCHAR(20),
    kullanici_adi VARCHAR(50) NOT NULL UNIQUE,
    sifre VARCHAR(255) NOT NULL,
    yetki ENUM('YONETICI', 'KASIYER', 'PERSONEL') DEFAULT 'PERSONEL',
    durum TINYINT(1) DEFAULT 1,
    olusturulma_tarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. Ürünler Tablosu
CREATE TABLE IF NOT EXISTS urunler (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kategori_id INT NOT NULL,
    barkod VARCHAR(50) UNIQUE,
    ad VARCHAR(150) NOT NULL,
    aciklama TEXT,
    alis_fiyati DECIMAL(10,2) NOT NULL,
    satis_fiyati DECIMAL(10,2) NOT NULL,
    stok_miktari INT DEFAULT 0,
    durum TINYINT(1) DEFAULT 1,
    olusturulma_tarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (kategori_id) REFERENCES kategoriler(id) ON DELETE RESTRICT
);

-- 4. İşlemler (Hareketler) Tablosu
-- Tüm alış, satış, iade ve değişim işlemlerinin tek bir tabloda tutulduğu yapı
CREATE TABLE IF NOT EXISTS islemler (
    id INT AUTO_INCREMENT PRIMARY KEY,
    islem_kodu VARCHAR(50) NULL, -- Aynı anda yapılan işlemleri (örn. tek fişteki satışlar) gruplamak için fiş/fatura kodu
    islem_tipi ENUM('ALIS', 'SATIS', 'IADE', 'DEGISIM') NOT NULL,
    
    -- Ana Ürün (Satılan, Alınan, İade Gelen veya Değişimde Müşteriden ALINAN eski ürün)
    urun_id INT NOT NULL,
    miktar INT NOT NULL, -- İşlem gören ana ürün adedi
    
    -- YENİ: Sadece Değişim İşleminde Kullanılacak Alanlar (Müşteriye VERİLEN yeni ürün)
    degisim_verilen_urun_id INT NULL, 
    degisim_verilen_miktar INT NULL,
    
    personel_id INT NOT NULL,
    birim_fiyat DECIMAL(10,2) NOT NULL, -- Ana ürünün işlem sırasındaki anlık fiyatı
    toplam_tutar DECIMAL(10,2) NOT NULL, -- Değişimde fiyat farkı çıkarsa buraya yazılabilir (Örn: +15 TL veya -5 TL)
    musteri_bilgisi VARCHAR(150) NULL, -- Satış, iade vs. için opsiyonel müşteri bilgisi
    islem_tarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    aciklama TEXT,
    
    FOREIGN KEY (urun_id) REFERENCES urunler(id) ON DELETE RESTRICT,
    FOREIGN KEY (degisim_verilen_urun_id) REFERENCES urunler(id) ON DELETE RESTRICT,
    FOREIGN KEY (personel_id) REFERENCES personeller(id) ON DELETE RESTRICT
);

-- ÖRNEK VERİLER (Sistemi test etmek için)
INSERT INTO kategoriler (ad, aciklama) VALUES 
('Kupalar', 'Baskılı ve özel tasarım kupalar'),
('Magnetler', 'Şehir ve figür magnetleri'),
('Anahtarlıklar', 'Metal ve ahşap anahtarlıklar'),
('Kar Küreleri', 'Müzikli ve ışıklı kar küreleri'),
('Biblolar & Seramik', 'El yapımı biblolar, çini ve seramik ürünler'),
('Kartpostallar', 'Tarihi ve turistik mekan kartpostalları'),
('Takılar', 'Gümüş, doğal taş ve otantik takılar'),
('Tişört ve Çantalar', 'Baskılı turistik tişört ve bez çantalar');

INSERT INTO personeller (ad, soyad, kullanici_adi, sifre, yetki) VALUES 
('Ahmet', 'Yılmaz', 'admin', '123456', 'YONETICI'),
('Ayşe', 'Demir', 'kasiyer_ayse', '123456', 'KASIYER'),
('Mehmet', 'Kaya', 'kasiyer_mehmet', '123456', 'KASIYER');

INSERT INTO urunler (kategori_id, barkod, ad, alis_fiyati, satis_fiyati, stok_miktari) VALUES 
(1, '869000000001', 'İstanbul Temalı Kupa', 25.00, 50.00, 100),
(1, '869000000002', 'Çini Desenli Kupa', 30.00, 65.00, 80),
(2, '869000000003', 'Galata Kulesi Magnet', 5.00, 15.00, 250),
(2, '869000000004', 'Kapadokya Balon Magnet', 6.00, 18.00, 300),
(3, '869000000005', 'Nazar Boncuklu Ahşap Anahtarlık', 7.50, 20.00, 150),
(4, '869000000006', 'Kız Kulesi Işıklı Kar Küresi', 85.00, 150.00, 45),
(5, '869000000007', 'Semazen Biblosu (Büyük Boy)', 60.00, 120.00, 30),
(5, '869000000008', 'El Yapımı Çini Kase', 120.00, 250.00, 15),
(6, '869000000009', 'Efes Antik Kenti Kartpostalı', 1.00, 5.00, 500),
(7, '869000000010', 'Doğal Taşlı Otantik Kolye', 45.00, 95.00, 60),
(8, '869000000011', 'I Love Istanbul Baskılı Tişört', 40.00, 90.00, 120),
(8, '869000000012', 'Türkiye Haritalı Bez Çanta', 20.00, 45.00, 200);

-- Sistemin nasıl çalıştığını görmek için örnek bikaç hareket (işlem) ekleyelim
INSERT INTO islemler (islem_kodu, islem_tipi, urun_id, miktar, degisim_verilen_urun_id, degisim_verilen_miktar, personel_id, birim_fiyat, toplam_tutar, musteri_bilgisi) VALUES
('FIS-001', 'SATIS', 6, 1, NULL, NULL, 2, 150.00, 150.00, 'Nakit Müşteri'), -- Kar küresi satışı
('FIS-001', 'SATIS', 3, 2, NULL, NULL, 2, 15.00, 30.00, 'Nakit Müşteri'), -- Aynı fişte 2 magnet satışı
('AL-100', 'ALIS', 8, 10, NULL, NULL, 1, 120.00, 1200.00, 'Kütahya Çini Toptan'), -- Toptancıdan çini kase alımı
('DEG-001', 'DEGISIM', 2, 1, 1, 1, 2, 65.00, -15.00, 'Değişim İşlemi'); -- Müşteri çini kupayı getirip, yerine İstanbul kupası aldı (fark iade edildi)

