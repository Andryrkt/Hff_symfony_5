<?php

namespace App\Model;

use App\Contract\DatabaseConnectionInterface;
use Psr\Log\LoggerInterface;

class DatabaseSqlServer28 implements DatabaseConnectionInterface
{
    private $dsn;
    private $user;
    private $password;
    private $conn;
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->dsn = $_ENV['DB_DNS_SQLSERV'];
        $this->user = $_ENV['DB_USERNAME_SQLSERV'];
        $this->password = $_ENV['DB_PASSWORD_SQLSERV'];
        $this->logger = $logger;
    }

    // Établir la connexion
    public function connect()
    {
        try {
            $this->conn = odbc_connect($this->dsn, $this->user, $this->password);
            if (!$this->conn) {
                throw new \Exception("ODBC Connection failed: " . odbc_errormsg());
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage()); // Log de l'erreur avec Symfony
            throw $e; // Relancer l'exception pour une gestion ultérieure
        }
    }

    // Exécuter une requête SQL
    public function executeQuery(string $sql)
    {
        try {
            $result = odbc_exec($this->conn, $sql);
            if (!$result) {
                throw new \Exception("ODBC Query failed: " . odbc_errormsg($this->conn));
            }
            return $result;
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage()); // Log de l'erreur avec Symfony
            throw $e; // Relancer l'exception pour une gestion ultérieure
        }
    }

     // Méthode pour récupérer les résultats d'une requête
    public function fetchResults($result)
    {
        $rows = [];
        if ($result) {
            while ($row = odbc_fetch_array($result)) {
                $rows[] = $row;
            }
        }
        return $rows;
    }

    // Préparer et exécuter une requête SQL avec des paramètres
    public function prepareAndExecute(string $sql, array $params)
    {
        try {
            $stmt = odbc_prepare($this->conn, $sql);
            if (!$stmt) {
                throw new \Exception("ODBC Prepare failed: " . odbc_errormsg($this->conn));
            }
            if (!odbc_execute($stmt, $params)) {
                throw new \Exception("ODBC Execute failed: " . odbc_errormsg($this->conn));
            }
            return $stmt;
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage()); // Log de l'erreur avec Symfony
            throw $e; // Relancer l'exception pour une gestion ultérieure
        }
    }

    // Fermer la connexion
    public function close()
    {
        if ($this->conn && is_resource($this->conn)) {
            odbc_close($this->conn);
            $this->logger->info("Connexion SQL Server fermée."); // Log d'info avec Symfony
        } else {
            $this->logger->warning("La connexion SQL Server n'est pas établie."); // Log d'avertissement avec Symfony
        }
    }

    // Obtenir la connexion actuelle
    public function getConnexion()
    {
        return $this->conn;
    }
}
