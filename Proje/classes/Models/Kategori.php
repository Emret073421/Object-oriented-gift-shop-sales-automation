<?php
// classes/Models/Kategori.php

class Kategori extends TemelVarlik {
    // Sadece kategoriye has olan özellik (Sütun)
    private $ad;
    private $aciklama;
    private $durum;

    // SETTERLAR
    public function setAd($ad)
    {
        $this->ad = $ad;
    }
    
    // GETTERLAR
    public function getAd()
    {
        return $this->ad;
    } 

    public function setAciklama($aciklama)
    {
        $this->aciklama = $aciklama;
    }
    
    public function getAciklama()
    {
        return $this->aciklama;
    } 

    public function setDurum($durum)
    {
        $this->durum = $durum;
    }
    
    public function getDurum()
    {
        return $this->durum;
    } 
    
    // id ve olusturulma_tarihi TemelVarlik'tan otomatik geliyor!

    // TemelVarlik'tan gelen abstract metodu implemente ediyoruz
    public function getOzetBilgi(): string
    {
        return "Kategori: " . $this->ad . " (" . ($this->durum ? "Aktif" : "Pasif") . ")";
    }
}
?>