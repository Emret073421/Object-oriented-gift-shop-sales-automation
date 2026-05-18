<?php
class TemelManager{
    protected $db;
    protected $baglanti;

    public function __construct($db){
        $this->db = $db;
        $this->baglanti = $db; // mysqli nesnesini doğrudan atıyoruz
    }
}
?>