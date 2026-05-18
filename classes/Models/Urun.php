<?php
// classes/Models/Urun.php

class Urun extends TemelVarlik {
    // Sadece ürüne has olan özellikler (Sütunlar)
    private $kategori_id;
    private $barkod;
    private $ad;
    private $alis_fiyati;
    private $satis_fiyati;
    private $stok_miktari;

    // SETTERLAR
    public function setKategoriId($kategori_id)
    {
        $this->kategori_id = $kategori_id;
    }
    public function setBarkod($barkod)
    {
        $this->barkod = $barkod;
    }
    public function setAd($ad)
    {
        $this->ad = $ad;
    }
    public function setAlisFiyati($alis_fiyati)
    {
        $this->alis_fiyati = $alis_fiyati;
    }
    public function setSatisFiyati($satis_fiyati)
    {
        $this->satis_fiyati = $satis_fiyati;
    }
    public function setStokMiktari($stok_miktari)
    {
        $this->stok_miktari = $stok_miktari;
    }
    
    // GETTERLAR
    public function getKategoriId()
    {
        return $this->kategori_id;
    }
    public function getBarkod()
    {
        return $this->barkod;
    }
    public function getAd()
    {
        return $this->ad;
    }
    public function getAlisFiyati()
    {
        return $this->alis_fiyati;
    }
    public function getSatisFiyati()
    {
        return $this->satis_fiyati;
    }
    public function getStokMiktari()
    {
        return $this->stok_miktari;
    }
}
?>