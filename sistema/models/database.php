<?php
require_once __DIR__ . '/../config/database.env.php';

class Database {
    /** @var PDO|null Una sola conexión PDO por petición (Hostinger limita las concurrentes). */
    private static $pdo = null;

    /** Variante de DSN: default | ops (hoy suelen ser la misma BD). */
    private $variant;

    public function __construct(string $variant = 'default')
    {
        $this->variant = $variant === 'ops' ? 'ops' : 'default';
    }

    public function connect() {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        $cfg = multilicores_db_config($this->variant);

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_TIMEOUT => 5,
        ];

        try {
            self::$pdo = new PDO(
                self::dsn($cfg['host'], $cfg, $cfg['host'] !== 'localhost'),
                $cfg['username'],
                $cfg['password'],
                $options
            );
            return self::$pdo;
        } catch (PDOException $e) {
            // 2002: el socket de localhost no responde. 1045 en 127.0.0.1 es otro host
            // y Hostinger no autoriza al usuario por TCP, así que no se reintenta.
            $socketBloqueado = $cfg['host'] === 'localhost'
                && strpos($e->getMessage(), '[2002]') !== false;

            if (!$socketBloqueado) {
                throw new PDOException(
                    'Error de conexión: ' . $e->getMessage(),
                    (int)$e->getCode(),
                    $e
                );
            }

            self::$pdo = new PDO(
                self::dsn('127.0.0.1', $cfg, true),
                $cfg['username'],
                $cfg['password'],
                $options
            );
            return self::$pdo;
        }
    }

    private static function dsn(string $host, array $cfg, bool $tcp): string
    {
        if (!$tcp) {
            return sprintf(
                'mysql:host=%s;dbname=%s;charset=%s',
                $host,
                $cfg['dbname'],
                $cfg['charset']
            );
        }

        return sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $host,
            $cfg['port'],
            $cfg['dbname'],
            $cfg['charset']
        );
    }
}
