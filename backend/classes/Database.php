<?php

class Database {
    private static ?Database $instance = null;
    private PDO $pdo;

    private function __construct() {
        try {
            $this->pdo = new PDO("mysql:host=localhost;dbname=smartpantry;charset=utf8", "root", "");
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Erreur de connexion à la base de données : " . $e->getMessage());
        }
    }

    public static function getInstance(): Database {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection(): PDO {
        return $this->pdo;
    }
}

// Fonction pour enregistrer les erreurs dans un fichier log
function logError($message) {
    error_log("[" . date("Y-m-d H:i:s") . "] " . $message . "\n", 3, __DIR__ . '/../logs/errors.log');
}
?>
