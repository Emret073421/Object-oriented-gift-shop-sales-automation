<?php
class KategoriManager extends TemelManager {

    // Kategori Ekleme (OOP Model Nesnesi Alır)
    public function kategoriEkle(Kategori $kategori)
    {
        $ad = $this->db->real_escape_string(trim($kategori->getAd()));
        $aciklama = $this->db->real_escape_string(trim($kategori->getAciklama()));

        // Çakışma kontrolü sınıf içerisine alındı (Encapsulation)
        $kontrol = $this->db->query("SELECT id FROM kategoriler WHERE ad = '$ad' AND durum = 1");
        if ($kontrol && $kontrol->num_rows > 0) {
            return ['basarili' => false, 'mesaj' => 'Bu isimde aktif bir kategori zaten mevcut.'];
        }

        $sql = "INSERT INTO kategoriler (ad, aciklama, durum) VALUES ('$ad', '$aciklama', 1)";
        if ($this->db->query($sql)) {
            return ['basarili' => true, 'mesaj' => 'Kategori başarıyla eklendi.'];
        }
        return ['basarili' => false, 'mesaj' => 'Kategori eklenirken hata oluştu: ' . $this->db->error];
    }

    // Kategorileri Getirme
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

    // Kategori Güncelleme (OOP Model Nesnesi Alır)
    public function kategoriGuncelle(Kategori $kategori)
    {
        $id = (int)$kategori->getId();
        $ad = $this->db->real_escape_string(trim($kategori->getAd()));
        $aciklama = $this->db->real_escape_string(trim($kategori->getAciklama()));

        // Çakışma kontrolü (Kendi ID'si hariç)
        $kontrol = $this->db->query("SELECT id FROM kategoriler WHERE ad = '$ad' AND id != $id AND durum = 1");
        if ($kontrol && $kontrol->num_rows > 0) {
            return ['basarili' => false, 'mesaj' => 'Bu isimde başka bir aktif kategori zaten mevcut.'];
        }

        $sql = "UPDATE kategoriler SET ad = '$ad', aciklama = '$aciklama' WHERE id = $id";
        if ($this->db->query($sql)) {
            return ['basarili' => true, 'mesaj' => 'Kategori başarıyla güncellendi.'];
        }
        return ['basarili' => false, 'mesaj' => 'Kategori güncellenirken hata oluştu: ' . $this->db->error];
    }

    // Kategori Silme
    public function sil($id = null)
    {
        $id = (int)$id;
        
        $kontrol = $this->db->query("SELECT id FROM urunler WHERE kategori_id = $id AND durum = 1 LIMIT 1");
        if ($kontrol && $kontrol->num_rows > 0) {
            return ['basarili' => false, 'mesaj' => 'Bu kategoriye ait aktif ürünler bulunmaktadır. Lütfen önce ürünleri başka kategoriye taşıyın veya silin.'];
        }

        $sql = "UPDATE kategoriler SET durum = 0 WHERE id = $id";
        if ($this->db->query($sql)) {
            return ['basarili' => true, 'mesaj' => 'Kategori başarıyla silindi (arşivlendi).'];
        }

        return ['basarili' => false, 'mesaj' => 'Kategori silinirken veritabanı hatası oluştu.'];
    }
}
?>
