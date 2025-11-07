<?php
// app/models/Database.php
// Wrapper sederhana untuk PDO agar query, bind, execute lebih mudah digunakan
// Menyediakan helper: query, bind, execute, resultSet, single, rowCount, lastInsertId
class Database {
    private $host = DB_HOST;
    private $user = DB_USER;
    private $pass = DB_PASS;
    private $dbname = DB_NAME;
    
    private $dbh;
    private $stmt;
    private $error;
    
    public function __construct() {
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname;
        $options = array(
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        );
        
        try {
            $this->dbh = new PDO($dsn, $this->user, $this->pass, $options);
        } catch(PDOException $e) {
            $this->error = $e->getMessage();
            // Tampilkan error koneksi untuk debugging (di environment produksi sebaiknya disimpan ke log)
            echo $this->error;
        }
    }
    
    public function query($sql) {
        // Siapkan statement SQL
        $this->stmt = $this->dbh->prepare($sql);
    }
    
    public function bind($param, $value, $type = null) {
        if(is_null($type)) {
            switch(true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }
        // Bind parameter untuk mencegah SQL injection
        $this->stmt->bindValue($param, $value, $type);
    }
    
    public function execute() {
        // Jalankan statement yang sudah dipersiapkan
        return $this->stmt->execute();
    }
    
    public function resultSet() {
        // Kembalikan semua baris sebagai objek
        $this->execute();
        return $this->stmt->fetchAll(PDO::FETCH_OBJ);
    }
    
    public function single() {
        // Kembalikan satu baris hasil query
        $this->execute();
        return $this->stmt->fetch(PDO::FETCH_OBJ);
    }
    
    public function rowCount() {
        return $this->stmt->rowCount();
    }
    
    public function lastInsertId() {
        return $this->dbh->lastInsertId();
    }
}
?>