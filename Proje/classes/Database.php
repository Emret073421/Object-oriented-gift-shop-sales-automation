<?php

class Veritabani {
    
    private $host = 'localhost';
    private $username = 'root';
    private $password = '';
    private $dbname = 'db_market';

    public $conn;

    public function __construct()
    {
        // %100 OOP: mysqli_connect() yerine 'new mysqli()' nesnesi üretiyoruz
        // Ve sonucu sınıfın kendi malı olan $this->conn içine atıyoruz
        $this->conn = new mysqli($this->host, $this->username, $this->password, $this->dbname);

        // Hata kontrolünü de OOP tarzı (ok işareti ile) yapıyoruz
        if ($this->conn->connect_error) {
            die("Veritabanı bağlantı hatası: " . $this->conn->connect_error);
        }

        // İleride 'Ş' veya 'Ğ' harfleri veritabanında bozuk çıkmasın diye:
        $this->conn->set_charset("utf8");
    }
}

?>