<?php

namespace App\Model;

use App\Contract\DatabaseConnectionInterface;
use Psr\Log\LoggerInterface;

class DatabaseInformix implements DatabaseConnectionInterface
{
    private $dsn;
    private $user;
    private $password;
    private $conn;
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->dsn = $_ENV['DB_DNS_INFORMIX'];
        $this->user = $_ENV['DB_USERNAME_INFORMIX'];
        $this->password = $_ENV['DB_PASSWORD_INFORMIX'];
        $this->logger = $logger;
    }

    // Méthode pour établir la connexion à la base de données
    public function connect()
    {
        try {
            if (!$this->dsn || !$this->user || !$this->password) {
                throw new \Exception("Les variables d'environnement DB_DNS_INFORMIX, DB_USERNAME_INFORMIX ou DB_PASSWORD_INFORMIX ne sont pas définies.");
            }

            $this->conn = odbc_connect($this->dsn, $this->user, $this->password);
            if (!$this->conn) {
                throw new \Exception("ODBC Connection failed: " . odbc_errormsg());
            }
            return $this->conn;
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage()); // Utilisation du logger Symfony
            throw $e; // Relance l'exception pour être gérée par Symfony
        }
    }

    // Méthode pour exécuter une requête SQL
    public function executeQuery(string $query)
    {
        try {
            if (!$this->conn) {
                throw new \Exception("La connexion à la base de données n'est pas établie.");
            }

            $result = odbc_exec($this->conn, $query);
            if (!$result) {
                throw new \Exception("ODBC Query failed: " . odbc_errormsg($this->conn));
            }
            return $result;
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage()); // Utilisation du logger Symfony
            throw $e; // Relance l'exception pour être gérée par Symfony
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

    // Méthode pour fermer la connexion à la base de données
    public function close()
    {
        if ($this->conn) {
            odbc_close($this->conn);
            $this->logger->info("Connexion fermée."); // Utilisation du logger Symfony
        } else {
            $this->logger->warning("La connexion à la base de données n'est pas établie."); // Utilisation du logger Symfony
        }
    }
}
