<?php

namespace App\Model;

use App\Contract\DatabaseConnectionInterface;
use App\Model\Traits\ConversionModel;

class Model
{
  use ConversionModel;

  private $connect;

  public function __construct(DatabaseConnectionInterface $connect)
  {
    $this->connect = $connect;
  }


  public function recuperationIdMateriel($numParc = '', $numSerie = '')
  {
    // Utilisation de requêtes préparées pour éviter les injections SQL
    $conditions = [];
    $params = [];

    if (!empty($numParc) && $numParc !== '0') {
      $conditions[] = "mmat_recalph = ?";
      $params[] = $numParc;
    }

    if (!empty($numSerie) && $numSerie !== '0') {
      $conditions[] = "mmat_numserie = ?";
      $params[] = $numSerie;
    }

    $whereClause = '';
    if (!empty($conditions)) {
      $whereClause = ' AND ' . implode(' AND ', $conditions);
    }

    $statement = "SELECT mmat_nummat as num_matricule
                     FROM mat_mat
                     WHERE MMAT_ETSTOCK IN ('ST','AT', '--')
                     AND TRIM(MMAT_AFFECT) IN ('IMM','LCD', 'SDO', 'VTE')
                     " . $whereClause;

    $result = $this->connect->executeQuery($statement, $params);
    $data = $this->connect->fetchResults($result);

    return $this->convertirEnUtf8($data);
  }
}
