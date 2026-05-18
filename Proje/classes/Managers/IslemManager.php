<?php
class IslemManager extends TemelManager {

    public function satisIcinUrunleriGetir($kategori_id = "tüm")
    {
        if($kategori_id == "tüm"){
            $sql = "SELECT * FROM urunler";
        }else{
            $sql = "SELECT * FROM urunler WHERE kategori_id = $kategori_id";
        }
        $sonuc = $this->db->query($sql);

        if ($sonuc === false) {
            // Hata durumunda loglama veya hata döndürme
            error_log("Ürün getirme hatası: " . $this->db->error);
            return false;
        }

        $urunler = [];
        while ($row = $sonuc->fetch_assoc()) {
            $urun = new Urun();
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

    public function satisYap($urunler)
    {
        foreach ($urunler as $urun)
            {   
                $sql = "
                INSERT INTO islemler (
                    islem_kodu,
                    islem_tipi,
                    urun_id,
                    miktar,
                    personel_id,
                    birim_fiyat,
                    toplam_tutar,
                    musteri_bilgisi
                ) VALUES ('', 'satış', $urun[id], $urun[miktar], $urun[personel_id], $urun[birim_fiyat], $urun[toplam_tutar], $urun[musteri_bilgisi])
                ";

                $this->db->query($sql);
            }


    }


    public function iadeAl(){
        
    }

    public function degisimYap(){
        
    }

    public function tarihAylıkFiltrele(string $ay, string $yil){
        
    }

    public function tarihYıllıkFiltrele(string $yil){
        
    }

    public function encokSatisYapanUrunler(){
        
    }

    public function dashboardUrunAdedi(){
        
    }

    // İşlem getirme (TemelManager abstract metot implementasyonu)
    public function getir($parametre = null)
    {
        $sql = "SELECT * FROM islemler";
        $sonuc = $this->db->query($sql);
        if ($sonuc === false) return false;
        $islemler = [];
        while ($row = $sonuc->fetch_assoc()) {
            $islemler[] = $row;
        }
        return $islemler;
    }

    // İşlem silme / iptal (TemelManager abstract metot implementasyonu)
    public function sil($id = null)
    {
        if (!$id) return false;
        $id = $this->db->real_escape_string($id);
        $sql = "DELETE FROM islemler WHERE id = $id";
        return $this->db->query($sql);
    }
}
?>
