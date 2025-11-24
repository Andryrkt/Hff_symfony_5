<?php

namespace App\DataFixtures\Admin;

use App\Entity\Admin\AgenceService\AgenceServiceIrium;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class AgenceServiceIriumFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['prod'];
    }
    public function load(ObjectManager $manager): void
    {
        $agenceServices = [
            // Antananarivo 01
            ['agence' => 'agence_antanarivo', 'service' => 'service_neg', 'code' => '01-NEG', 'societe' => 'HF', 'codeSage' => 'AB11', 'responsable' => 'Prisca'],
            ['agence' => 'agence_antanarivo', 'service' => 'service_neg', 'code' => '01-NEG', 'societe' => 'HF', 'codeSage' => 'AB21', 'responsable' => 'Prisca'],
            ['agence' => 'agence_antanarivo', 'service' => 'service_com', 'code' => '01-COM', 'societe' => 'HF', 'codeSage' => 'AB51', 'responsable' => 'Paul'],
            ['agence' => 'agence_antanarivo', 'service' => 'service_ate', 'code' => '01-ATE', 'societe' => 'HF', 'codeSage' => 'AC11', 'responsable' => 'Jaona'],
            ['agence' => 'agence_antanarivo', 'service' => 'service_csp', 'code' => '01-CSP', 'societe' => 'HF', 'codeSage' => 'AC12', 'responsable' => 'Jaona'],
            ['agence' => 'agence_antanarivo', 'service' => 'service_gar', 'code' => '01-GAR', 'societe' => 'HF', 'codeSage' => 'AC14', 'responsable' => 'Jaona'],
            ['agence' => 'agence_antanarivo', 'service' => 'service_for', 'code' => '01-FOR', 'societe' => 'HF', 'codeSage' => 'AC16', 'responsable' => ''],
            ['agence' => 'agence_antanarivo', 'service' => 'service_ass', 'code' => '01-ASS', 'societe' => 'HF', 'codeSage' => 'AG11', 'responsable' => 'Olivier'],
            ['agence' => 'agence_antanarivo', 'service' => 'service_fle', 'code' => '01-FLE', 'societe' => 'HF', 'codeSage' => 'AB41', 'responsable' => ''],
            ['agence' => 'agence_antanarivo', 'service' => 'service_mas', 'code' => '01-MAS', 'societe' => 'HF', 'codeSage' => 'AC17', 'responsable' => ''],
            ['agence' => 'agence_antanarivo', 'service' => 'service_map', 'code' => '01-MAP', 'societe' => 'HF', 'codeSage' => 'AB71', 'responsable' => ''],

            // Cessna Ivato 02
            ['agence' => 'agence_cessna_ivato', 'service' => 'service_neg', 'code' => '02-NEG', 'societe' => 'HF', 'codeSage' => 'CB21', 'responsable' => 'Daniel'],
            ['agence' => 'agence_cessna_ivato', 'service' => 'service_ate', 'code' => '02-ATE', 'societe' => 'HF', 'codeSage' => 'CC11', 'responsable' => 'Daniel'],
            ['agence' => 'agence_cessna_ivato', 'service' => 'service_lcd', 'code' => '02-LCD', 'societe' => 'HF', 'codeSage' => 'CC121', 'responsable' => 'Daniel'],

            // Fort dauphin 20
            ['agence' => 'agence_fort_dauphin', 'service' => 'service_neg', 'code' => '20-NEG', 'societe' => 'HF', 'codeSage' => 'FB21', 'responsable' => ''],
            ['agence' => 'agence_fort_dauphin', 'service' => 'service_ate', 'code' => '20-ATE', 'societe' => 'HF', 'codeSage' => 'FC11', 'responsable' => ''],

            // Ambatovy 30
            ['agence' => 'agence_ambatovy', 'service' => 'service_neg', 'code' => '30-NEG', 'societe' => 'HF', 'codeSage' => 'BB21', 'responsable' => 'Prisca'],
            ['agence' => 'agence_ambatovy', 'service' => 'service_ate', 'code' => '30-ATE', 'societe' => 'HF', 'codeSage' => 'BC11', 'responsable' => 'Njara'],
            ['agence' => 'agence_ambatovy', 'service' => 'service_man', 'code' => '30-MAN', 'societe' => 'HF', 'codeSage' => 'BC15', 'responsable' => 'Njara'],
            ['agence' => 'agence_ambatovy', 'service' => 'service_fle', 'code' => '30-FLE', 'societe' => 'HF', 'codeSage' => 'BB41', 'responsable' => ''],

            // Tamatave 40
            ['agence' => 'agence_tamatave', 'service' => 'service_neg', 'code' => '40-NEG', 'societe' => 'HF', 'codeSage' => 'MB21', 'responsable' => ''],
            ['agence' => 'agence_tamatave', 'service' => 'service_ate', 'code' => '40-ATE', 'societe' => 'HF', 'codeSage' => 'MC11', 'responsable' => ''],
            ['agence' => 'agence_tamatave', 'service' => 'service_tho', 'code' => '40-THO', 'societe' => 'HF', 'codeSage' => 'MC13', 'responsable' => ''],
            ['agence' => 'agence_tamatave', 'service' => 'service_lcd', 'code' => '40-LCD', 'societe' => 'HF', 'codeSage' => 'MC121', 'responsable' => ''],
            ['agence' => 'agence_tamatave', 'service' => 'service_fle', 'code' => '40-FLE', 'societe' => 'HF', 'codeSage' => 'MB41', 'responsable' => ''],
            ['agence' => 'agence_tamatave', 'service' => 'service_lev', 'code' => '40-LEV', 'societe' => 'HF', 'codeSage' => 'MC14', 'responsable' => ''],

            // rental 50
            ['agence' => 'agence_rental', 'service' => 'service_neg', 'code' => '50-NEG', 'societe' => 'HF', 'codeSage' => 'RB21', 'responsable' => ''],
            ['agence' => 'agence_rental', 'service' => 'service_com', 'code' => '50-COM', 'societe' => 'HF', 'codeSage' => 'RB51', 'responsable' => ''],
            ['agence' => 'agence_rental', 'service' => 'service_ate', 'code' => '50-ATE', 'societe' => 'HF', 'codeSage' => 'RC11', 'responsable' => ''],
            ['agence' => 'agence_rental', 'service' => 'service_lcd', 'code' => '50-LCD', 'societe' => 'HF', 'codeSage' => 'RC21', 'responsable' => ''],
            ['agence' => 'agence_rental', 'service' => 'service_ltv', 'code' => '50-LTV', 'societe' => 'HF', 'codeSage' => 'RC22', 'responsable' => ''],
            ['agence' => 'agence_rental', 'service' => 'service_lfd', 'code' => '50-LFD', 'societe' => 'HF', 'codeSage' => 'RC23', 'responsable' => ''],
            ['agence' => 'agence_rental', 'service' => 'service_lbv', 'code' => '50-LBV', 'societe' => 'HF', 'codeSage' => 'RC24', 'responsable' => ''],
            ['agence' => 'agence_rental', 'service' => 'service_lr6', 'code' => '50-LR6', 'societe' => 'HF', 'codeSage' => 'RC25', 'responsable' => ''],
            ['agence' => 'agence_rental', 'service' => 'service_lst', 'code' => '50-LST', 'societe' => 'HF', 'codeSage' => 'RC26', 'responsable' => ''],
            ['agence' => 'agence_rental', 'service' => 'service_lsc', 'code' => '50-LSC', 'societe' => 'HF', 'codeSage' => 'RC27', 'responsable' => ''],

            // pneu - outil - lub 60
            ['agence' => 'agence_pneu_outil_lub', 'service' => 'service_neg', 'code' => '60-NEG', 'societe' => 'HF', 'codeSage' => 'PB21', 'responsable' => ''],
            ['agence' => 'agence_pneu_outil_lub', 'service' => 'service_ate', 'code' => '60-ATE', 'societe' => 'HF', 'codeSage' => 'PC11', 'responsable' => ''],


            // Administration 80
            ['agence' => 'agence_administration', 'service' => 'service_dir', 'code' => '80-DIR', 'societe' => 'HF', 'codeSage' => 'DA11', 'responsable' => 'Charles'],
            ['agence' => 'agence_administration', 'service' => 'service_fin', 'code' => '80-FIN', 'societe' => 'HF', 'codeSage' => 'DA12', 'responsable' => 'Patrick'],
            ['agence' => 'agence_administration', 'service' => 'service_per', 'code' => '80-PER', 'societe' => 'HF', 'codeSage' => 'DA13', 'responsable' => 'Tahina'],
            ['agence' => 'agence_administration', 'service' => 'service_inf', 'code' => '80-INF', 'societe' => 'HF', 'codeSage' => 'DA14', 'responsable' => 'Olivier'],
            ['agence' => 'agence_administration', 'service' => 'service_imm', 'code' => '80-IMM', 'societe' => 'HF', 'codeSage' => 'DA15', 'responsable' => ''],
            ['agence' => 'agence_administration', 'service' => 'service_tra', 'code' => '80-TRA', 'societe' => 'HF', 'codeSage' => 'DA16', 'responsable' => ''],
            ['agence' => 'agence_administration', 'service' => 'service_app', 'code' => '80-APP', 'societe' => 'HF', 'codeSage' => 'DA17', 'responsable' => ''],
            ['agence' => 'agence_administration', 'service' => 'service_ump', 'code' => '80-UMP', 'societe' => 'HF', 'codeSage' => 'DA18', 'responsable' => ''],

            //Commercial energie 90
            ['agence' => 'agence_comm_energie', 'service' => 'service_com', 'code' => '90-COM', 'societe' => 'HF', 'codeSage' => 'EB51', 'responsable' => ''],
            ['agence' => 'agence_comm_energie', 'service' => 'service_lcd', 'code' => '90-LCD', 'societe' => 'HF', 'codeSage' => 'EC11', 'responsable' => ''],
            ['agence' => 'agence_comm_energie', 'service' => 'service_eng', 'code' => '90-ENG', 'societe' => 'HF', 'codeSage' => 'ED10', 'responsable' => ''],

            // energie durable 91
            ['agence' => 'agence_energie_durable', 'service' => 'service_tsi', 'code' => '91-TSI', 'societe' => 'HF', 'codeSage' => 'OD32', 'responsable' => ''],
            ['agence' => 'agence_energie_durable', 'service' => 'service_tsd', 'code' => '91-TSD', 'societe' => 'HF', 'codeSage' => '', 'responsable' => ''],
            ['agence' => 'agence_energie_durable', 'service' => 'service_vat', 'code' => '91-VAT', 'societe' => 'HF', 'codeSage' => '', 'responsable' => ''],
            ['agence' => 'agence_energie_durable', 'service' => 'service_blk', 'code' => '91-BLK', 'societe' => 'HF', 'codeSage' => 'OD33', 'responsable' => ''],
            ['agence' => 'agence_energie_durable', 'service' => 'service_eng', 'code' => '91-ENG', 'societe' => 'HF', 'codeSage' => 'OD10', 'responsable' => ''],
            ['agence' => 'agence_energie_durable', 'service' => 'service_slr', 'code' => '91-SLR', 'societe' => 'HF', 'codeSage' => '', 'responsable' => ''],

            // energie jirama 92
            ['agence' => 'agence_energie_jirama', 'service' => 'service_mah', 'code' => '92-MAH', 'societe' => 'HF', 'codeSage' => 'TD11', 'responsable' => ''],
            ['agence' => 'agence_energie_jirama', 'service' => 'service_nos', 'code' => '92-NOS', 'societe' => 'HF', 'codeSage' => 'TD12', 'responsable' => ''],
            ['agence' => 'agence_energie_jirama', 'service' => 'service_tul', 'code' => '92-TUL', 'societe' => 'HF', 'codeSage' => 'TD16', 'responsable' => ''],
            ['agence' => 'agence_energie_jirama', 'service' => 'service_amb', 'code' => '92-AMB', 'societe' => 'HF', 'codeSage' => 'TD31', 'responsable' => ''],
            ['agence' => 'agence_energie_jirama', 'service' => 'service_lcj', 'code' => '92-LCJ', 'societe' => 'HF', 'codeSage' => 'TD32', 'responsable' => ''],
            ['agence' => 'agence_energie_jirama', 'service' => 'service_tsi', 'code' => '92-TSI', 'societe' => 'HF', 'codeSage' => 'TD33', 'responsable' => ''],

            // travel airways c1
            ['agence' => 'agence_travel_airways', 'service' => 'service_c1', 'code' => 'C1-C1', 'societe' => 'TA', 'codeSage' => '', 'responsable' => ''],


            // Ajoutez ici les autres agences/services selon le même modèle...
        ];

        foreach ($agenceServices as $data) {
            $agenceService = new AgenceServiceIrium();
            $agenceService->setAgence($this->getReference($data['agence']))
                ->setService($this->getReference($data['service']))
                ->setCode($data['code'])
                ->setSociete($data['societe'])
                ->setCodeSage($data['codeSage'])
                ->setResponsable($data['responsable']);

            $manager->persist($agenceService);

            // Génération de référence unique avec codeSage
            $referenceKey = sprintf(
                'agence_service_%s_%s_%s',
                str_replace('agence_', '', $data['agence']),
                strtolower(str_replace('service_', '', $data['service'])),
                $data['codeSage'] // rend la référence unique
            );

            $this->addReference($referenceKey, $agenceService);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            AgenceFixtures::class,
            ServiceFixtures::class,
        ];
    }
}
