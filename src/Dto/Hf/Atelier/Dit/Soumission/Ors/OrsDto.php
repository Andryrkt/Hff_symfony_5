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
    public $pieceJoint01;
    public $pieceJoint02;
    public $pieceJoint03;
    public array $pieceJoint04 = [];
    public array $orsParInterventionDtos = [];
    public array $totalOrsParIntervention = []; // TODO: à revoir
    public array $pieceFaibleAchatDtos = [];
    public ?int $numeroDevis = null;
    public string $estPieceSortieMagasin = 'NON';
    public string $estPieceAchatLocaux = 'NON';
    public string $estPiecePol = 'NON';
    public $dateDemande;
}
