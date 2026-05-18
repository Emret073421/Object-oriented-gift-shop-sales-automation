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

    // Satış Yapma İşlemi (Sepetteki ürünleri işler, stok düşer ve benzersiz işlem kodu üretir)
    public function satisYap($urunler, $not = '', $personelId = 1, $musteri = 'Nakit Müşteri')
    {
        if (empty($urunler)) return false;

        // Benzersiz İşlem Kodu Üretimi (Örn: FIS-20260519-4829)
        $islemKodu = 'FIS-' . date('Ymd') . '-' . rand(1000, 9999);
        $personelId = (int)$personelId;
        $not = $this->db->real_escape_string($not);
        $musteri = $this->db->real_escape_string($musteri);

        $basarili = true;

        foreach ($urunler as $urun) {
            $urunId = (int)$urun['id'];
            $miktar = (int)$urun['miktar'];
            $birimFiyat = (float)$urun['fiyat'];
            $toplamTutar = $birimFiyat * $miktar;

            // 1. İşlemler tablosuna ekle
            $sqlIslem = "INSERT INTO islemler (islem_kodu, islem_tipi, urun_id, miktar, personel_id, birim_fiyat, toplam_tutar, musteri_bilgisi, aciklama) 
                         VALUES ('$islemKodu', 'SATIS', $urunId, $miktar, $personelId, $birimFiyat, $toplamTutar, '$musteri', '$not')";
            if (!$this->db->query($sqlIslem)) {
                error_log("Satış ekleme hatası: " . $this->db->error);
                $basarili = false;
            }

            // 2. Ürün stok miktarını düşür
            $sqlStok = "UPDATE urunler SET stok_miktari = stok_miktari - $miktar WHERE id = $urunId";
            $this->db->query($sqlStok);
        }

        return $basarili ? $islemKodu : false;
    }

    // İade Alma İşlemi
    public function iadeAl($islemKodu, $urunId, $iadeMiktar, $personelId, $aciklama = 'Müşteri İadesi')
    {
        $islemKodu = $this->db->real_escape_string($islemKodu);
        $urunId = (int)$urunId;
        $iadeMiktar = (int)$iadeMiktar;
        $personelId = (int)$personelId;
        $aciklama = $this->db->real_escape_string($aciklama);

        // Orijinal satışı bul
        $sorgu = $this->db->query("SELECT birim_fiyat FROM islemler WHERE islem_kodu = '$islemKodu' AND urun_id = $urunId AND islem_tipi = 'SATIS' LIMIT 1");
        if (!$sorgu || $sorgu->num_rows === 0) {
            return ['basarili' => false, 'mesaj' => 'Belirtilen işlem koduna ait satış kaydı bulunamadı.'];
        }

        $satir = $sorgu->fetch_assoc();
        $birimFiyat = (float)$satir['birim_fiyat'];
        $iadeTutar = -($birimFiyat * $iadeMiktar); // İade olduğu için eksi tutar

        // İade kaydını islemler tablosuna ekle (Aynı islem_kodu ile, islem_tipi = 'IADE')
        $sqlIslem = "INSERT INTO islemler (islem_kodu, islem_tipi, urun_id, miktar, personel_id, birim_fiyat, toplam_tutar, musteri_bilgisi, aciklama) 
                     VALUES ('$islemKodu', 'IADE', $urunId, $iadeMiktar, $personelId, $birimFiyat, $iadeTutar, 'İade Müşterisi', '$aciklama')";
        
        if ($this->db->query($sqlIslem)) {
            // Ürün stoğunu geri artır
            $this->db->query("UPDATE urunler SET stok_miktari = stok_miktari + $iadeMiktar WHERE id = $urunId");
            return ['basarili' => true, 'mesaj' => 'İade işlemi başarıyla tamamlandı. Stok güncellendi.', 'iade_kodu' => $islemKodu];
        }

        return ['basarili' => false, 'mesaj' => 'İade işlemi sırasında veritabanı hatası oluştu.'];
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
        $q = $this->db->query("SELECT COUNT(id) AS cesit FROM urunler WHERE durum = 1");
        return ($q && $q->num_rows > 0) ? (int)$q->fetch_assoc()['cesit'] : 0;
    }

    // Dashboard Özet İstatistikleri
    public function dashboardOzet()
    {
        $ozet = [
            'gunluk_ciro' => 0,
            'toplam_ciro' => 0,
            'gunluk_islem' => 0,
            'toplam_islem' => 0,
            'iade_degisim' => 0,
            'toplam_cesit' => 0
        ];

        // Günlük Ciro (Bugün)
        $q1 = $this->db->query("SELECT SUM(toplam_tutar) AS ciro FROM islemler WHERE islem_tipi = 'SATIS' AND DATE(islem_tarihi) = CURDATE()");
        if ($q1 && $q1->num_rows > 0) {
            $ozet['gunluk_ciro'] = (float)$q1->fetch_assoc()['ciro'];
        }

        // Toplam Ciro (Genel)
        $q2 = $this->db->query("SELECT SUM(toplam_tutar) AS ciro FROM islemler WHERE islem_tipi = 'SATIS'");
        if ($q2 && $q2->num_rows > 0) {
            $ozet['toplam_ciro'] = (float)$q2->fetch_assoc()['ciro'];
        }

        // Günlük İşlem Sayısı
        $q3 = $this->db->query("SELECT COUNT(id) AS adet FROM islemler WHERE DATE(islem_tarihi) = CURDATE()");
        if ($q3 && $q3->num_rows > 0) {
            $ozet['gunluk_islem'] = (int)$q3->fetch_assoc()['adet'];
        }

        // Toplam İşlem Sayısı
        $q4 = $this->db->query("SELECT COUNT(id) AS adet FROM islemler");
        if ($q4 && $q4->num_rows > 0) {
            $ozet['toplam_islem'] = (int)$q4->fetch_assoc()['adet'];
        }

        // İade ve Değişim Sayısı
        $q5 = $this->db->query("SELECT COUNT(id) AS adet FROM islemler WHERE islem_tipi IN ('IADE', 'DEGISIM')");
        if ($q5 && $q5->num_rows > 0) {
            $ozet['iade_degisim'] = (int)$q5->fetch_assoc()['adet'];
        }

        // Toplam Ürün Çeşidi
        $q6 = $this->db->query("SELECT COUNT(id) AS cesit FROM urunler WHERE durum = 1");
        if ($q6 && $q6->num_rows > 0) {
            $ozet['toplam_cesit'] = (int)$q6->fetch_assoc()['cesit'];
        }

        return $ozet;
    }

    // Kritik Stok Seviyesindeki Ürünler (Örn: 50 adet ve altı)
    public function kritikStokGetir($limit = 50)
    {
        $limit = (int)$limit;
        $sql = "SELECT u.barkod, u.ad, u.satis_fiyati, u.stok_miktari, k.ad AS kategori_adi 
                FROM urunler u 
                LEFT JOIN kategoriler k ON u.kategori_id = k.id 
                WHERE u.stok_miktari <= $limit AND u.durum = 1 
                ORDER BY u.stok_miktari ASC";
        $sonuc = $this->db->query($sql);
        $urunler = [];
        if ($sonuc) {
            while ($row = $sonuc->fetch_assoc()) {
                $urunler[] = $row;
            }
        }
        return $urunler;
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
