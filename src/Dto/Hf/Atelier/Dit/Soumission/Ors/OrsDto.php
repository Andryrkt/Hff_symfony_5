<?php

namespace App\Dto\Hf\Atelier\Dit\Soumission\Ors;


class OrsDto
{
    public string $numeroDit;
    public int $numeroOr;
    public ?string $observation = null;
    public ?int $numeroVersion = null;
    public ?string $statut = null;
    public bool $pieceFaibleActiviteAchat = false;
    public ?int $numeroDevis = null;
    public string $estPieceSortieMagasin = 'NON';
    public string $estPieceAchatLocaux = 'NON';
    public string $estPiecePol = 'NON';
    public $dateDemande;
    public $emailDemandeur;
    public $pieceJoint01;
    public $pieceJoint02;
    public $pieceJoint03;
    public array $pieceJoint04 = [];
    // comparaison de l'OR avant par l'OR qu'on va soumettre
    public array $orsAvantDtos = [];
    public array $orsApresDtos = [];
    // récapitulation de l'OR
    public array $orsParInterventionDtos = [];
    public array $totalOrsParIntervention = [];
    // pièces faibles achat
    public array $pieceFaibleAchatDtos = [];
}
