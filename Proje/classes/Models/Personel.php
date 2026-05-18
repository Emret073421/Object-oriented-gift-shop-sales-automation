<?php
// classes/Models/Personel.php

class Personel extends TemelVarlik {
    
    private $ad;
    private $soyad;
    private $kullanici_adi;
    private $sifre;
    private $ad_soyad;
    private $yetki;

    // SETTERLAR
    public function setAd($ad) { $this->ad = $ad; }
    public function setSoyad($soyad) { $this->soyad = $soyad; }
    public function setKullaniciAdi($kullanici_adi) { $this->kullanici_adi = $kullanici_adi; }
    public function setSifre($sifre) { $this->sifre = $sifre; }
    public function setAdSoyad($ad_soyad) { $this->ad_soyad = $ad_soyad; }
    public function setYetki($yetki) { $this->yetki = $yetki; }

    // GETTERLAR
    public function getAd() { return $this->ad; }
    public function getSoyad() { return $this->soyad; }
    public function getKullaniciAdi() { return $this->kullanici_adi; }
    public function getSifre() { return $this->sifre; }
    public function getAdSoyad() { return $this->ad_soyad; }
    public function getYetki() { return $this->yetki; }

    public function getOzetBilgi(): string
    {
        return "Personel: " . ($this->ad_soyad ?? ($this->ad . ' ' . $this->soyad)) . " (@" . $this->kullanici_adi . ")";
    }
}
?>
