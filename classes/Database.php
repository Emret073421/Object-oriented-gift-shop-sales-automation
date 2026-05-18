<?php
class Database {
    // 1. Kapsülleme (Private): Bu bilgiler sadece bu sınıfın içinde kalsın, dışarı sızmasın.
    private $host = "localhost";
    private $dbname = "hediyelik_otomasyon"; // db_market demiştin, kendi db adınla güncelleyebilirsin
    private $username = "root";
    private $password = "";
    private $conn;

    public function baglan() {
        // 2. Bağlantıyı kurarken 'new mysqli' kullanarak nesne tabanlı başlatıyoruz.
        $this->conn = new mysqli($this->host, $this->username, $this->password, $this->dbname);

        // 3. Hata Kontrolü: Eğer bağlantı yoksa sistemi durdur ve hatayı söyle.
        if ($this->conn->connect_error) {
            die("Veritabanı bağlantısı başarısız: " . $this->conn->connect_error);
        }

        // 4. Karakter Seti: Türkçe karakter (ş, İ, ğ vb.) sorunu yaşamamak için bu şart!
        $this->conn->set_charset("utf8mb4");

        return $this->conn;
    }
}
?>