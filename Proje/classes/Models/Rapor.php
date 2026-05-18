<?php
// classes/Models/Rapor.php

class Rapor extends TemelVarlik {
    private $ay;
    private $yil;
    private $net_ciro;
    private $satilan_adet;
    private $siparis_adet;
    private $tablo_html;
    private $yillik_ciro;
    private $en_cok_satan;

    // SETTERLAR
    public function setAy($ay) { $this->ay = (int)$ay; }
    public function setYil($yil) { $this->yil = (int)$yil; }
    public function setNetCiro($net_ciro) { $this->net_ciro = $net_ciro; }
    public function setSatilanAdet($satilan_adet) { $this->satilan_adet = $satilan_adet; }
    public function setSiparisAdet($siparis_adet) { $this->siparis_adet = $siparis_adet; }
    public function setTabloHtml($tablo_html) { $this->tablo_html = $tablo_html; }
    public function setYillikCiro($yillik_ciro) { $this->yillik_ciro = $yillik_ciro; }
    public function setEnCokSatan($en_cok_satan) { $this->en_cok_satan = $en_cok_satan; }

    // GETTERLAR
    public function getAy() { return $this->ay; }
    public function getYil() { return $this->yil; }
    public function getNetCiro() { return $this->net_ciro; }
    public function getSatilanAdet() { return $this->satilan_adet; }
    public function getSiparisAdet() { return $this->siparis_adet; }
    public function getTabloHtml() { return $this->tablo_html; }
    public function getYillikCiro() { return $this->yillik_ciro; }
    public function getEnCokSatan() { return $this->en_cok_satan; }

    public function getOzetBilgi(): string {
        return "Rapor: " . $this->yil . "/" . $this->ay;
    }
}
?>
