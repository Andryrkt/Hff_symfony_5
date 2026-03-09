<?php

namespace App\Service\Hf\Atelier\Dit\Soumission\Ors;

/**
 * Service gérant la structure (colonnes, styles) des tableaux pour le PDF OR
 */
class OrsPdfTableConfig
{
    /**
     * Configuration pour le tableau "Situation de l'OR"
     */
    public function getHeaderSituationOr(): array
    {
        return [
            [
                'key'          => 'itv',
                'label'        => 'ITV',
                'width'        => 40,
                'header_style' => 'font-weight: 900;',
                'cell_style'   => '',
            ],
            [
                'key'          => 'libelleItv',
                'label'        => 'Libellé ITV',
                'width'        => 150,
                'header_style' => 'font-weight: bold;',
                'cell_style'   => 'text-align: left;',
            ],
            [
                'key'          => 'datePlanning',
                'label'        => 'Date pla',
                'width'        => 50,
                'header_style' => 'font-weight: bold;',
                'footer_style' => 'font-weight: bold; text-align: center;',
                'cell_style'   => 'text-align: left;',
                'type'         => 'date'
            ],
            [
                'key'          => 'nbLigAv',
                'label'        => 'Nb Lig av',
                'width'        => 50,
                'header_style' => 'font-weight: bold;',
                'footer_style' => 'font-weight: bold; text-align: center;',
                'cell_style'   => '',
            ],
            [
                'key'          => 'nbLigAp',
                'label'        => 'Nb Lig ap',
                'width'        => 50,
                'header_style' => 'font-weight: bold;',
                'footer_style' => 'font-weight: bold; text-align: center;',
                'cell_style'   => '',
            ],
            [
                'key'          => 'mttTotalAv',
                'label'        => 'Mtt Total av',
                'width'        => 80,
                'header_style' => 'font-weight: bold; text-align: center;',
                'footer_style' => 'font-weight: bold; text-align: right;',
                'cell_style'   => 'text-align: right;',
                'type'         => 'number'
            ],
            [
                'key'          => 'mttTotalAp',
                'label'        => 'Mtt total ap',
                'width'        => 80,
                'header_style' => 'font-weight: bold; text-align: center;',
                'footer_style' => 'font-weight: bold; text-align: right;',
                'cell_style'   => 'text-align: right;',
                'type'         => 'number'
            ],
            [
                'key'          => 'statut',
                'label'        => 'Statut',
                'width'        => 40,
                'header_style' => 'font-weight: bold; text-align: center;',
                'cell_style'   => 'text-align: left;',
                'styler'       => function ($value) {
                    switch ($value) {
                        case 'Supp':
                            return 'background-color: #FF0000;';
                        case 'Modif':
                            return 'background-color: #FFFF00;';
                        case 'Nouv':
                            return 'background-color: #00FF00;';
                        default:
                            return '';
                    }
                }
            ]
        ];
    }

    /**
     * Configuration pour le tableau de Récapitulation par intervention
     */
    public function getHeaderRecapitulationOR(): array
    {
        return [
            [
                'key'          => 'numeroItv',
                'label'        => 'ITV',
                'width'        => 40,
                'header_style' => 'font-weight: 900;',
                'cell_style'   => 'font-weight: 900;',
                'footer_style' => 'font-weight: 900;'
            ],
            [
                'key'          => 'montantItv',
                'label'        => 'Mtt Total',
                'width'        => 70,
                'header_style' => 'font-weight: bold; text-align: center;',
                'cell_style'   => 'text-align: right;',
                'footer_style' => 'font-weight: bold; text-align: right;',
                'type'         => 'number'
            ],
            [
                'key'          => 'montantPiece',
                'label'        => 'Mtt Pièces',
                'width'        => 60,
                'header_style' => 'font-weight: bold; text-align: center;',
                'cell_style'   => 'text-align: right;',
                'footer_style' => 'font-weight: bold; text-align: right;',
                'type'         => 'number'
            ],
            [
                'key'          => 'montantMo',
                'label'        => 'Mtt MO',
                'width'        => 60,
                'header_style' => 'font-weight: bold; text-align: center;',
                'cell_style'   => 'text-align: right;',
                'footer_style' => 'font-weight: bold; text-align: right;',
                'type'         => 'number'
            ],
            [
                'key'          => 'montantAchatLocaux',
                'label'        => 'Mtt ST',
                'width'        => 80,
                'header_style' => 'font-weight: bold; text-align: center;',
                'cell_style'   => 'text-align: right;',
                'footer_style' => 'font-weight: bold; text-align: right;',
                'type'         => 'number'
            ],
            [
                'key'          => 'montantLubrifiants',
                'label'        => 'Mtt LUB',
                'width'        => 80,
                'header_style' => 'font-weight: bold; text-align: center;',
                'cell_style'   => 'text-align: right;',
                'footer_style' => 'font-weight: bold; text-align: right;',
                'type'         => 'number'
            ],
            [
                'key'          => 'montantFraisDivers',
                'label'        => 'Mtt Autres',
                'width'        => 80,
                'header_style' => 'font-weight: bold; text-align: center;',
                'cell_style'   => 'text-align: right;',
                'footer_style' => 'font-weight: bold; text-align: right;',
                'type'         => 'number'
            ]
        ];
    }

    /**
     * Configuration pour le tableau "Pièces à faible activité"
     */
    public function getHeaderPieceFaibleActivite(): array
    {
        return [
            [
                'key'          => 'numeroItv',
                'label'        => 'ITV',
                'width'        => 40,
                'header_style' => 'font-weight: bold;',
                'cell_style'   => '',
            ],
            [
                'key'          => 'libelleItv',
                'label'        => 'Libellé ITV',
                'width'        => 150,
                'header_style' => 'font-weight: bold;',
                'cell_style'   => 'text-align: left;',
            ],
            [
                'key'          => 'constructeur',
                'label'        => 'Const',
                'width'        => 40,
                'header_style' => 'font-weight: bold;',
                'cell_style'   => '',
            ],
            [
                'key'          => 'reference',
                'label'        => 'Réfp.',
                'width'        => 40,
                'header_style' => 'font-weight: bold;',
                'cell_style'   => '',
            ],
            [
                'key'          => 'designation',
                'label'        => 'Designation',
                'width'        => 150,
                'header_style' => 'font-weight: bold;',
                'cell_style'   => 'text-align: left;',
            ],
            [
                'key'          => 'pmp',
                'label'        => 'Pmp',
                'width'        => 80,
                'header_style' => 'font-weight: bold;',
                'cell_style'   => 'text-align: right;',
                'type'         => 'number'
            ],
            [
                'key'          => 'dateDerniereCde',
                'label'        => 'Date dern cmd',
                'width'        => 50,
                'header_style' => 'font-weight: bold;',
                'cell_style'   => 'text-align: center;',
                'default_value' => 'jamais commandé',
                'type'         => 'date'
            ],
        ];
    }
}
