<?php 
class UrunManager extends TemelManager {

    //Abstact metotları implemente ediyoruz.

    //Ürün ekleme
    public function urunEkle($barkod, $ad, $alis_fiyati, $satis_fiyati, $stok_miktari, $kategori_id = 1)
    {
        $barkod = $this->db->real_escape_string($barkod);
        $ad = $this->db->real_escape_string($ad);
        $alis_fiyati = (float)$alis_fiyati;
        $satis_fiyati = (float)$satis_fiyati;
        $stok_miktari = (int)$stok_miktari;
        $kategori_id = (int)$kategori_id;

        $sql = "INSERT INTO urunler (barkod, ad, alis_fiyati, satis_fiyati, stok_miktari, kategori_id, durum) VALUES ('$barkod', '$ad', $alis_fiyati, $satis_fiyati, $stok_miktari, $kategori_id, 1)";
        $sonuc = $this->db->query($sql);

        if ($sonuc === false) {
            error_log("Ürün ekleme hatası: " . $this->db->error);
            return false;
        }
        return true;
    }

    //Ürün getirme (TemelManager abstract metot implementasyonu)
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
            // Ekstra olarak kategori_adi bilgisini dinamik property olarak ekleyebiliriz
            $urun->kategori_adi = $row['kategori_adi'] ?? 'Genel';
            $urunler[] = $urun;
        }

        return $urunler;
    }

    //Ürün güncelleme
    public function urunGuncelle($id, $barkod, $ad, $alis_fiyati, $satis_fiyati, $stok_miktari, $kategori_id = 1)
    {
        $id = (int)$id;
        $barkod = $this->db->real_escape_string($barkod);
        $ad = $this->db->real_escape_string($ad);
        $alis_fiyati = (float)$alis_fiyati;
        $satis_fiyati = (float)$satis_fiyati;
        $stok_miktari = (int)$stok_miktari;
        $kategori_id = (int)$kategori_id;

        $sql = "UPDATE urunler SET barkod = '$barkod', ad = '$ad', alis_fiyati = $alis_fiyati, satis_fiyati = $satis_fiyati, stok_miktari = $stok_miktari, kategori_id = $kategori_id WHERE id = $id";
        $sonuc = $this->db->query($sql);

        if ($sonuc === false) {
            error_log("Ürün güncelleme hatası: " . $this->db->error);
            return false;
        }
        return true;
    }

    //Ürün silme (TemelManager abstract metot implementasyonu)
    public function sil($id = null)
    {
        $id = (int)$id;
        // Soft delete (durum = 0) yapıyoruz ki geçmiş satış ve iade raporları bozulmasın!
        $sql = "UPDATE urunler SET durum = 0 WHERE id = $id";
        $sonuc = $this->db->query($sql);
        if ($sonuc === false) {
            error_log("Ürün silme hatası: " . $this->db->error);
            return false;
        }
        return true;
    }

    //Stok Ekleme
    public function stokEkle($id, $adet)
    {
        $id = $this->db->real_escape_string($id);
        $adet = $this->db->real_escape_string($adet);
        $sql = "UPDATE urunler SET stok_miktari = stok_miktari + $adet WHERE id = $id";
        $sonuc = $this->db->query($sql);
        if ($sonuc === false) {
            // Hata durumunda loglama veya hata döndürme
            error_log("Stok ekleme hatası: " . $this->db->error);
            return false;
        }
        return true;
    }

    //Stok çıkarma
    public function stokCikar($id, $adet)
    {
        $id = $this->db->real_escape_string($id);
        $adet = $this->db->real_escape_string($adet);
        $sql = "UPDATE urunler SET stok_miktari = stok_miktari - $adet WHERE id = $id";
        $sonuc = $this->db->query($sql);
        if ($sonuc === false) {
            // Hata durumunda loglama veya hata döndürme
            error_log("Stok çıkarma hatası: " . $this->db->error);
            return false;
        }
        return true;
    }
        
}
?>