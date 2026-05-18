<?php
class PersonelManager extends TemelManager {

    // Personel ekleme (OOP Model Nesnesi Alır)
    public function personelEkle(Personel $personel)
    {
        $ad = $this->db->real_escape_string(trim($personel->getAd()));
        $soyad = $this->db->real_escape_string(trim($personel->getSoyad()));
        $kadi = $this->db->real_escape_string(trim($personel->getKullaniciAdi()));
        
        // Yetki standardizasyonu (YÖNETİCİ -> YONETICI dönüşümü)
        $hamYetki = mb_strtoupper(trim($personel->getYetki()), 'UTF-8');
        if ($hamYetki === 'YÖNETİCİ' || $hamYetki === 'YONETICI' || $hamYetki === 'ADMIN') {
            $yetki = 'YONETICI';
        } elseif ($hamYetki === 'KASİYER' || $hamYetki === 'KASIYER') {
            $yetki = 'KASIYER';
        } else {
            $yetki = 'PERSONEL';
        }

        // Kullanıcı adı kontrolü
        $sorgu = $this->db->query("SELECT id FROM personeller WHERE kullanici_adi = '$kadi' AND durum = 1");
        if ($sorgu && $sorgu->num_rows > 0) {
            return ['basarili' => false, 'mesaj' => 'Bu kullanıcı adı zaten başka bir personel tarafından kullanılıyor.'];
        }

        // Şifreyi güvenli hashle
        $hashliSifre = password_hash($personel->getSifre(), PASSWORD_DEFAULT);

        $sql = "INSERT INTO personeller (ad, soyad, kullanici_adi, sifre, yetki, durum) 
                VALUES ('$ad', '$soyad', '$kadi', '$hashliSifre', '$yetki', 1)";
        
        if ($this->db->query($sql)) {
            return ['basarili' => true, 'mesaj' => 'Personel başarıyla kaydedildi.'];
        }
        return ['basarili' => false, 'mesaj' => 'Personel eklenirken hata oluştu: ' . $this->db->error];
    }

    // Personel getir
    public function getir($parametre = null)
    {
        $sql = "SELECT id, ad, soyad, kullanici_adi, yetki, durum FROM personeller WHERE durum = 1 ORDER BY ad ASC";
        $sonuc = $this->db->query($sql);
        if ($sonuc === false) return false;
        $personeller = [];
        while ($row = $sonuc->fetch_assoc()) {
            $personeller[] = $row;
        }
        return $personeller;
    }

    // Personel güncelleme (OOP Model Nesnesi Alır)
    public function guncelle(Personel $personel)
    {
        $id = (int)$personel->getId();
        $ad = $this->db->real_escape_string(trim($personel->getAd()));
        $soyad = $this->db->real_escape_string(trim($personel->getSoyad()));
        $kadi = $this->db->real_escape_string(trim($personel->getKullaniciAdi()));
        
        // Yetki standardizasyonu (YÖNETİCİ -> YONETICI dönüşümü)
        $hamYetki = mb_strtoupper(trim($personel->getYetki()), 'UTF-8');
        if ($hamYetki === 'YÖNETİCİ' || $hamYetki === 'YONETICI' || $hamYetki === 'ADMIN') {
            $yetki = 'YONETICI';
        } elseif ($hamYetki === 'KASİYER' || $hamYetki === 'KASIYER') {
            $yetki = 'KASIYER';
        } else {
            $yetki = 'PERSONEL';
        }

        // Kullanıcı adı çakışma kontrolü
        $sorgu = $this->db->query("SELECT id FROM personeller WHERE kullanici_adi = '$kadi' AND id != $id AND durum = 1");
        if ($sorgu && $sorgu->num_rows > 0) {
            return ['basarili' => false, 'mesaj' => 'Bu kullanıcı adı zaten başka bir personel tarafından kullanılıyor.'];
        }

        $sifreEk = "";
        if (!empty($personel->getSifre())) {
            $hashliSifre = password_hash($personel->getSifre(), PASSWORD_DEFAULT);
            $sifreEk = ", sifre = '$hashliSifre'";
        }

        $sql = "UPDATE personeller SET ad = '$ad', soyad = '$soyad', kullanici_adi = '$kadi', yetki = '$yetki' $sifreEk WHERE id = $id";
        
        if ($this->db->query($sql)) {
            return ['basarili' => true, 'mesaj' => 'Personel bilgileri başarıyla güncellendi.'];
        }
        return ['basarili' => false, 'mesaj' => 'Güncelleme hatası: ' . $this->db->error];
    }

    // Personel silme
    public function sil($id = null)
    {
        $id = (int)$id;
        if ($id <= 0) return ['basarili' => false, 'mesaj' => 'Geçersiz personel ID.'];

        if (isset($_SESSION['personel_id']) && $_SESSION['personel_id'] == $id) {
            return ['basarili' => false, 'mesaj' => 'Kendi oturumunuzu silemezsiniz!'];
        }

        $sql = "UPDATE personeller SET durum = 0 WHERE id = $id";
        if ($this->db->query($sql)) {
            return ['basarili' => true, 'mesaj' => 'Personel başarıyla pasife alındı (arşivlendi).'];
        }
        return ['basarili' => false, 'mesaj' => 'Silme hatası: ' . $this->db->error];
    }

    // Personel giris
    public function giris($kadi, $sifre)
    {
        $temizKullanici = $this->db->real_escape_string(trim($kadi));
        $sql = "SELECT * FROM personeller WHERE kullanici_adi = '$temizKullanici' AND durum = 1 LIMIT 1";
        $sonuc = $this->db->query($sql);

        if ($sonuc && $sonuc->num_rows > 0) {
            $kullaniciVerisi = $sonuc->fetch_assoc();

            if (password_verify($sifre, $kullaniciVerisi['sifre']) || $sifre === $kullaniciVerisi['sifre']) {
                $personel = new Personel();
                $personel->setId($kullaniciVerisi['id']); 
                $personel->setAdSoyad($kullaniciVerisi['ad'] . ' ' . $kullaniciVerisi['soyad']);
                $personel->setKullaniciAdi($kullaniciVerisi['kullanici_adi']);
                $personel->setYetki($kullaniciVerisi['yetki']);
                return $personel;
            }
        }
        return false;
    }

    public function kullaniciListele() { return $this->getir(); }
    public function yetkiKontrol() { return true; }
}
?>
