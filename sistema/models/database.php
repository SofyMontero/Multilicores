<?php
class Database {
    private $host = "localhost";
    private $port = 3306;
    private $db_name = "u633742531_Multilicores";
    private $username = "u633742531_Multilicores25";
    private $password = "Multilicores2025";
    private $charset = "utf8mb4";

    /** @var PDO|null Una sola conexión PDO por petición (Hostinger limita las concurrentes). */
    private static $pdo = null;

    // private $host = "localhost";
    // private $db_name = "multilicores";
    // private $username = "root";
    // private $password = "";
    // private $charset = "utf8mb4";

    public function connect() {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_TIMEOUT => 5,
        ];

        // localhost usa socket Unix; si el host lo bloquea (HY000/2002), se fuerza TCP.
        $hosts = [$this->host];
        if ($this->host === 'localhost') {
            $hosts[] = '127.0.0.1';
        }

        $lastError = null;
        foreach ($hosts as $host) {
            try {
                $dsn = sprintf(
                    'mysql:host=%s;port=%d;dbname=%s;charset=%s',
                    $host,
                    $this->port,
                    $this->db_name,
                    $this->charset
                );
                self::$pdo = new PDO($dsn, $this->username, $this->password, $options);
                return self::$pdo;
            } catch (PDOException $e) {
                $lastError = $e;
            }
        }

        throw new PDOException(
            'Error de conexión: ' . ($lastError ? $lastError->getMessage() : 'No se pudo conectar a MySQL'),
            (int)($lastError ? $lastError->getCode() : 0),
            $lastError
        );
    }
}
?>
