<?php
// classes/Models/Rapor.php

class Rapor extends TemelVarlik {
    private $ay;
    private $yil;
    
    // Aylık Alanlar
    private $satis_toplam;
    private $iade_toplam;
    private $alis_toplam;
    private $net_kazanc;
    private $satilan_adet;
    private $siparis_adet;
    private $tablo_html;
    
    // Yıllık Alanlar
    private $yillik_satis;
    private $yillik_iade;
    private $yillik_alis;
    private $yillik_net;
    private $en_cok_satan;

    // SETTERLAR
    public function setAy($ay) { $this->ay = (int)$ay; }
    public function setYil($yil) { $this->yil = (int)$yil; }
    
    public function setSatisToplam($satis_toplam) { $this->satis_toplam = $satis_toplam; }
    public function setIadeToplam($iade_toplam) { $this->iade_toplam = $iade_toplam; }
    public function setAlisToplam($alis_toplam) { $this->alis_toplam = $alis_toplam; }
    public function setNetKazanc($net_kazanc) { $this->net_kazanc = $net_kazanc; }
    public function setSatilanAdet($satilan_adet) { $this->satilan_adet = $satilan_adet; }
    public function setSiparisAdet($siparis_adet) { $this->siparis_adet = $siparis_adet; }
    public function setTabloHtml($tablo_html) { $this->tablo_html = $tablo_html; }
    
    public function setYillikSatis($yillik_satis) { $this->yillik_satis = $yillik_satis; }
    public function setYillikIade($yillik_iade) { $this->yillik_iade = $yillik_iade; }
    public function setYillikAlis($yillik_alis) { $this->yillik_alis = $yillik_alis; }
    public function setYillikNet($yillik_net) { $this->yillik_net = $yillik_net; }
    public function setEnCokSatan($en_cok_satan) { $this->en_cok_satan = $en_cok_satan; }

    // GETTERLAR
    public function getAy() { return $this->ay; }
    public function getYil() { return $this->yil; }
    
    public function getSatisToplam() { return $this->satis_toplam; }
    public function getIadeToplam() { return $this->iade_toplam; }
    public function getAlisToplam() { return $this->alis_toplam; }
    public function getNetKazanc() { return $this->net_kazanc; }
    public function getSatilanAdet() { return $this->satilan_adet; }
    public function getSiparisAdet() { return $this->siparis_adet; }
    public function getTabloHtml() { return $this->tablo_html; }
    
    public function getYillikSatis() { return $this->yillik_satis; }
    public function getYillikIade() { return $this->yillik_iade; }
    public function getYillikAlis() { return $this->yillik_alis; }
    public function getYillikNet() { return $this->yillik_net; }
    public function getEnCokSatan() { return $this->en_cok_satan; }

    public function getOzetBilgi(): string {
        return "Finans Raporu: " . $this->yil . "/" . $this->ay;
    }
}
?>
