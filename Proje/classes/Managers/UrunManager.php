<?php 
class UrunManager extends TemelManager {

    //Abstact metotları implemente ediyoruz.

    //Ürün ekleme
    public function urunEkle($barkod, $ad, $alis_fiyati, $satis_fiyati, $stok_miktari)
    {
        $barkod = $this->db->real_escape_string($barkod);
        $ad = $this->db->real_escape_string($ad);
        $alis_fiyati = $this->db->real_escape_string($alis_fiyati);
        $satis_fiyati = $this->db->real_escape_string($satis_fiyati);
        $stok_miktari = $this->db->real_escape_string($stok_miktari);

        $sql = "INSERT INTO urunler (barkod, ad, alis_fiyati, satis_fiyati, stok_miktari) VALUES ('$barkod', '$ad', '$alis_fiyati', '$satis_fiyati', '$stok_miktari')";
        $sonuc = $this->db->query($sql);

        if ($sonuc === false) {
            // Hata durumunda loglama veya hata döndürme
            error_log("Ürün ekleme hatası: " . $this->db->error);
            return false;
        }
        return true;
    }

    //Ürün getirme
    public function urunleriGetir($kategori_id = "tüm", $harf = "")
    {
        // Arama kelimesinin başına ve sonuna % ekliyoruz (LIKE araması için)
        $arama_kelimesi = "%" . $harf . "%";

        if ($kategori_id == "tüm") {
            // INNER JOIN ile tabloları doğru şekilde bağladık (Kartezyen çarpım engellendi)
            // Arama şartlarını parantez içine alarak işlem önceliğini koruduk
            $sql = "SELECT u.* FROM urunler u 
                    INNER JOIN kategoriler k ON u.kategori_id = k.id 
                    WHERE (u.ad LIKE ? OR u.barkod LIKE ? OR k.kategori_adi LIKE ?)";
            
            $stmt = $this->db->prepare($sql);
            // 3 tane soru işareti için 3 tane string ("sss") parametresi bağlıyoruz
            $stmt->bind_param("sss", $arama_kelimesi, $arama_kelimesi, $arama_kelimesi);
        } else {
            // Kategori filtresi ve arama filtresi bir arada
            // Kategori ID'sini dışarıda tuttuk, aramayı paranteze aldık
            $sql = "SELECT u.* FROM urunler u 
                    WHERE u.kategori_id = ? 
                    AND (u.ad LIKE ? OR u.barkod LIKE ?)";
            
            $stmt = $this->db->prepare($sql);
            $kategori_id_int = (int)$kategori_id; // Sayıya zorlayarak ekstra koruma
            // 1 integer, 2 string parametre ("iss")
            $stmt->bind_param("iss", $kategori_id_int, $arama_kelimesi, $arama_kelimesi);
        }

        // Sorguyu güvenli bir şekilde çalıştır
        if (!$stmt->execute()) {
            error_log("Ürün getirme hatası: " . $this->db->error);
            return false;
        }

        $sonuc = $stmt->get_result();
        $urunler = [];

        while ($row = $sonuc->fetch_assoc()) {
            $urun = new Urun();
            // Nesne property'lerini dolduruyoruz
            $urun->setId($row['id']);
            $urun->setBarkod($row['barkod']);
            $urun->setAd($row['ad']);
            $urun->setAlisFiyati($row['alis_fiyati']);
            $urun->setSatisFiyati($row['satis_fiyati']);
            $urun->setStokMiktari($row['stok_miktari']);
            $urunler[] = $urun;
        }

        return $urunler;
    }

    //Ürün güncelleme
    public function urunGuncelle($id, $barkod, $ad, $alis_fiyati, $satis_fiyati, $stok_miktari)
    {
        $id = $this->db->real_escape_string($id);
        $barkod = $this->db->real_escape_string($barkod);
        $ad = $this->db->real_escape_string($ad);
        $alis_fiyati = $this->db->real_escape_string($alis_fiyati);
        $satis_fiyati = $this->db->real_escape_string($satis_fiyati);
        $stok_miktari = $this->db->real_escape_string($stok_miktari);

        $sql = "UPDATE urunler SET barkod = '$barkod', ad = '$ad', alis_fiyati = '$alis_fiyati', satis_fiyati = '$satis_fiyati', stok_miktari = '$stok_miktari' WHERE id = $id";
        $sonuc = $this->db->query($sql);

        if ($sonuc === false) {
            // Hata durumunda loglama veya hata döndürme
            error_log("Ürün güncelleme hatası: " . $this->db->error);
            return false;
        }
        return true;
    }

    //Ürün silme
    public function urunSil($id)
    {
        $id = $this->db->real_escape_string($id);
        $sql = "DELETE FROM urunler WHERE id = $id";
        $sonuc = $this->db->query($sql);
        if ($sonuc === false) {
            // Hata durumunda loglama veya hata döndürme
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