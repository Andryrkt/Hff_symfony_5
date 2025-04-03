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
      if($numParc === '' || $numParc === '0' || $numParc === null){
        $conditionNumParc = "";
      } else {
        $conditionNumParc = "and mmat_recalph = '" . $numParc ."'";
      }

      if($numSerie === '' || $numSerie === '0' || $numSerie === null){
        $conditionNumSerie = "";
      } else {
        $conditionNumSerie = "and mmat_numserie = '" . $numSerie . "'";
      }

        $statement = "SELECT
        mmat_nummat as num_matricule
        from mat_mat
        where  MMAT_ETSTOCK in ('ST','AT', '--')
        and trim(MMAT_AFFECT) in ('IMM','LCD', 'SDO', 'VTE')
        ".$conditionNumParc."
        ".$conditionNumSerie."
        ";

      
        $result = $this->connect->executeQuery($statement);


        $data = $this->connect->fetchResults($result);


        return $this->convertirEnUtf8($data);
    }
}