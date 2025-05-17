<?php
require_once 'config.php';
class Conexao {
    private static $pdo = null;

    private function __construct() {}

    private static function verificaExtensao() {
        if (!extension_loaded('pdo_pgsql')) {
            throw new Exception('Extensão pdo_pgsql não está habilitada.');
        }
    }

    public static function getInstance() {
        if (self::$pdo === null) {
            self::verificaExtensao();
            try {
                self::$pdo = new PDO(
                    'pgsql:host=' . HOST . ';port=' . PORT . ';dbname=' . DBNAME,
                    USER,
                    PASSWORD
                );
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                throw new Exception('Erro ao conectar no banco: ' . $e->getMessage());
            }
        }
        return self::$pdo;
    }

    public static function isConectado() {
        return self::$pdo !== null;
    }
}
