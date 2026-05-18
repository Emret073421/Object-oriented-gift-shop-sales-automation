<?php
// classes/Models/TemelVarlik.php

abstract class TemelVarlik {
    protected $id;
    protected $olusturulma_tarihi;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getOlusturulmaTarihi()
    {
        return $this->olusturulma_tarihi;
    }

    public function setOlusturulmaTarihi($olusturulma_tarihi)
    {
        $this->olusturulma_tarihi = $olusturulma_tarihi;
    } 
}
?>