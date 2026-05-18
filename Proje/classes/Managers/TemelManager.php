<?php
abstract class TemelManager{
    protected $db;
    protected $baglanti;

    public function __construct($db){
        $this->db = $db;
        $this->baglanti = $db; // mysqli nesnesini doğrudan atıyoruz
    }

    // Bütün alt menajer sınıflarının (UrunManager, PersonelManager, IslemManager) 
    // temel veritabanı işlemlerini barındırmasını zorunlu kılan abstract (soyut) metotlar.
    // Alt sınıfların esnek parametreler alabilmesi için varsayılan değerler tanımlanmıştır.
    abstract public function getir($parametre = null);
    abstract public function sil($id = null);
}
?>