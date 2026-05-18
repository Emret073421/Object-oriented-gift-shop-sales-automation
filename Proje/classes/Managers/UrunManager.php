<?php 
class UrunManager extends TemelManager {

    // Ürün ekleme (OOP Model Nesnesi Alır)
    public function urunEkle(Urun $urun)
    {
        $barkod = $this->db->real_escape_string(trim($urun->getBarkod()));
        $ad = $this->db->real_escape_string(trim($urun->getAd()));
        $alis_fiyati = (float)$urun->getAlisFiyati();
        $satis_fiyati = (float)$urun->getSatisFiyati();
        $stok_miktari = (int)$urun->getStokMiktari();
        $kategori_id = (int)$urun->getKategoriId();

        // Barkod çakışma kontrolü tamamen Sınıf içerisine alındı (Encapsulation)
        $kontrol = $this->db->query("SELECT id FROM urunler WHERE barkod = '$barkod' AND durum = 1");
        if ($kontrol && $kontrol->num_rows > 0) {
            return ['basarili' => false, 'mesaj' => 'Bu barkoda sahip aktif bir ürün zaten sistemde mevcut.'];
        }

        $sql = "INSERT INTO urunler (barkod, ad, alis_fiyati, satis_fiyati, stok_miktari, kategori_id, durum) 
                VALUES ('$barkod', '$ad', $alis_fiyati, $satis_fiyati, $stok_miktari, $kategori_id, 1)";
        
        if ($this->db->query($sql)) {
            return ['basarili' => true, 'mesaj' => 'Ürün başarıyla eklendi.'];
        }
        return ['basarili' => false, 'mesaj' => 'Ürün eklenirken hata oluştu: ' . $this->db->error];
    }

    // Ürün getirme
    public function getir($kategori_id = "tüm", $harf = "")
    {
        $arama_kelimesi = "%" . $harf . "%";

        if ($kategori_id === "tüm" || $kategori_id === "tumu") {
            $sql = "SELECT u.*, k.ad AS kategori_adi FROM urunler u 
                    LEFT JOIN kategoriler k ON u.kategori_id = k.id 
                    WHERE u.durum = 1 AND (u.ad LIKE ? OR u.barkod LIKE ? OR k.ad LIKE ?)
                    ORDER BY u.ad ASC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("sss", $arama_kelimesi, $arama_kelimesi, $arama_kelimesi);
        } else {
            $sql = "SELECT u.*, k.ad AS kategori_adi FROM urunler u 
                    LEFT JOIN kategoriler k ON u.kategori_id = k.id 
                    WHERE u.durum = 1 AND u.kategori_id = ? 
                    AND (u.ad LIKE ? OR u.barkod LIKE ?)
                    ORDER BY u.ad ASC";
            
            $stmt = $this->db->prepare($sql);
            $kategori_id_int = (int)$kategori_id;
            $stmt->bind_param("iss", $kategori_id_int, $arama_kelimesi, $arama_kelimesi);
        }

        if (!$stmt->execute()) {
            error_log("Ürün getirme hatası: " . $this->db->error);
            return false;
        }

        $sonuc = $stmt->get_result();
        $urunler = [];

        while ($row = $sonuc->fetch_assoc()) {
            $urun = new Urun();
            $urun->setId($row['id']);
            $urun->setBarkod($row['barkod']);
            $urun->setAd($row['ad']);
            $urun->setAlisFiyati($row['alis_fiyati']);
            $urun->setSatisFiyati($row['satis_fiyati']);
            $urun->setStokMiktari($row['stok_miktari']);
            $urun->setKategoriId($row['kategori_id']);
            $urun->kategori_adi = $row['kategori_adi'] ?? 'Genel';
            $urunler[] = $urun;
        }

        return $urunler;
    }

    // Ürün güncelleme (OOP Model Nesnesi Alır)
    public function urunGuncelle(Urun $urun)
    {
        $id = (int)$urun->getId();
        $barkod = $this->db->real_escape_string(trim($urun->getBarkod()));
        $ad = $this->db->real_escape_string(trim($urun->getAd()));
        $alis_fiyati = (float)$urun->getAlisFiyati();
        $satis_fiyati = (float)$urun->getSatisFiyati();
        $stok_miktari = (int)$urun->getStokMiktari();
        $kategori_id = (int)$urun->getKategoriId();

        // Barkod çakışma kontrolü (Kendi ID'si hariç)
        $kontrol = $this->db->query("SELECT id FROM urunler WHERE barkod = '$barkod' AND id != $id AND durum = 1");
        if ($kontrol && $kontrol->num_rows > 0) {
            return ['basarili' => false, 'mesaj' => 'Bu barkoda sahip başka bir aktif ürün zaten sistemde mevcut.'];
        }

        $sql = "UPDATE urunler SET barkod = '$barkod', ad = '$ad', alis_fiyati = $alis_fiyati, satis_fiyati = $satis_fiyati, stok_miktari = $stok_miktari, kategori_id = $kategori_id WHERE id = $id";
        
        if ($this->db->query($sql)) {
            return ['basarili' => true, 'mesaj' => 'Ürün başarıyla güncellendi.'];
        }
        return ['basarili' => false, 'mesaj' => 'Ürün güncellenirken hata oluştu: ' . $this->db->error];
    }

    // Ürün silme
    public function sil($id = null)
    {
        $id = (int)$id;
        $sql = "UPDATE urunler SET durum = 0 WHERE id = $id";
        if ($this->db->query($sql)) {
            return true;
        }
        error_log("Ürün silme hatası: " . $this->db->error);
        return false;
    }

    // Stok Ekleme
    public function stokEkle($id, $adet)
    {
        $id = (int)$id;
        $adet = (int)$adet;
        $sql = "UPDATE urunler SET stok_miktari = stok_miktari + $adet WHERE id = $id";
        return $this->db->query($sql);
    }

    // Stok çıkarma
    public function stokCikar($id, $adet)
    {
        $id = (int)$id;
        $adet = (int)$adet;
        $sql = "UPDATE urunler SET stok_miktari = stok_miktari - $adet WHERE id = $id";
        return $this->db->query($sql);
    }
}
?>