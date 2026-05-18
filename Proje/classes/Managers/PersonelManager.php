<?php
class PersonelManager extends TemelManager {

    //Personel ekleme
    public function personelEkle($ad, $soyad, $kadi , $sifre, $yetki)
    {
        $ad= $this->db->real_escape_string($ad);
        $soyad= $this->db->real_escape_string($soyad);
        $kadi= $this->db->real_escape_string($kadi);
        $sifre= $this->db->real_escape_string($sifre);
        $yetki= $this->db->real_escape_string($yetki);
        
        $sql = "INSERT INTO personeller (ad, soyad, kullanici_adi, sifre, yetki) VALUES ('$ad', '$soyad', '$kadi', '$sifre', $yetki)";
        $sonuc = $this->db->query($sql);

        if ($sonuc === false) {
            // Hata durumunda loglama veya hata döndürme
            error_log("Personel ekleme hatası: " . $this->db->error);
            return false;
        }
        return true;
    }

    //Personel getir (TemelManager abstract metot implementasyonu)
    public function getir($parametre = null)
    {
        
    }

    //Personel güncelleme
    public function guncelle()
    {
        
    }

    //Personel silme (TemelManager abstract metot implementasyonu)
    public function sil($id = null)
    {
        
    }

    //Personel giris
    public function giris($kadi, $sifre)
    {
        // Güvenlik: SQL Injection engellemek için temizlik yapıyoruz
        $temizKullanici = $this->db->real_escape_string($kadi);

        // Kullanıcıyı sorgula
        $sql = "SELECT * FROM personeller WHERE kullanici_adi = '$temizKullanici' LIMIT 1";
        $sonuc = $this->db->query($sql);

        if ($sonuc && $sonuc->num_rows > 0) {
            $kullaniciVerisi = $sonuc->fetch_assoc();

            // Şifre kontrolü (Veritabanında MD5 veya password_hash olduğunu varsayıyoruz)
            if (password_verify($sifre, $kullaniciVerisi['sifre']) || $sifre === $kullaniciVerisi['sifre']) {
                
                // Giriş başarılıysa, verileri sayfaya taşımak için bir model nesnesi oluşturuyoruz
                $personel = new Personel();
                $personel->setId($kullaniciVerisi['id']); 
                // Veritabanındaki 'ad' ve 'soyad' sütunlarını birleştirip atıyoruz
                $personel->setAdSoyad($kullaniciVerisi['ad'] . ' ' . $kullaniciVerisi['soyad']);
                $personel->setKullaniciAdi($kullaniciVerisi['kullanici_adi']);
                $personel->setYetki($kullaniciVerisi['yetki']);
                
                return $personel; // Dolu modeli geri fırlat
            }
        }
        
        return false; // Bilgiler hatalıysa false dön
    }

    public function kullaniciListele()
    {
        
    }

    public function yetkiKontrol()
    {
        
    }
}
?>
