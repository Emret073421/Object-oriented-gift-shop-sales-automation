<?php
class KategoriManager extends TemelManager {

    // Kategori Ekleme
    public function kategoriEkle($ad, $aciklama)
    {
        $ad = $this->db->real_escape_string(trim($ad));
        $aciklama = $this->db->real_escape_string(trim($aciklama));

        $sql = "INSERT INTO kategoriler (ad, aciklama, durum) VALUES ('$ad', '$aciklama', 1)";
        $sonuc = $this->db->query($sql);

        if ($sonuc === false) {
            error_log("Kategori ekleme hatası: " . $this->db->error);
            return false;
        }
        return true;
    }

    // Kategorileri Getirme (TemelManager abstract metot implementasyonu)
    public function getir($parametre = null)
    {
        $sql = "SELECT * FROM kategoriler WHERE durum = 1 ORDER BY ad ASC";
        $sonuc = $this->db->query($sql);
        $kategoriler = [];

        if ($sonuc && $sonuc->num_rows > 0) {
            while ($row = $sonuc->fetch_assoc()) {
                $kategori = new Kategori();
                $kategori->setId($row['id']);
                $kategori->setAd($row['ad']);
                $kategori->setAciklama($row['aciklama']);
                $kategori->setDurum($row['durum']);
                $kategoriler[] = $kategori;
            }
        }

        return $kategoriler;
    }

    // Kategori Güncelleme
    public function kategoriGuncelle($id, $ad, $aciklama)
    {
        $id = (int)$id;
        $ad = $this->db->real_escape_string(trim($ad));
        $aciklama = $this->db->real_escape_string(trim($aciklama));

        $sql = "UPDATE kategoriler SET ad = '$ad', aciklama = '$aciklama' WHERE id = $id";
        $sonuc = $this->db->query($sql);

        if ($sonuc === false) {
            error_log("Kategori güncelleme hatası: " . $this->db->error);
            return false;
        }
        return true;
    }

    // Kategori Silme (TemelManager abstract metot implementasyonu)
    public function sil($id = null)
    {
        $id = (int)$id;
        
        // Kategoriye ait aktif ürün var mı kontrol edelim
        $kontrol = $this->db->query("SELECT id FROM urunler WHERE kategori_id = $id AND durum = 1 LIMIT 1");
        if ($kontrol && $kontrol->num_rows > 0) {
            return ['basarili' => false, 'mesaj' => 'Bu kategoriye ait aktif ürünler bulunmaktadır. Lütfen önce ürünleri başka kategoriye taşıyın veya silin.'];
        }

        // Soft delete (durum = 0)
        $sql = "UPDATE kategoriler SET durum = 0 WHERE id = $id";
        if ($this->db->query($sql)) {
            return ['basarili' => true, 'mesaj' => 'Kategori başarıyla silindi (arşivlendi).'];
        }

        return ['basarili' => false, 'mesaj' => 'Kategori silinirken veritabanı hatası oluştu.'];
    }
}
?>
