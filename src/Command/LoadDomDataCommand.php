<?php

namespace App\Command;

use App\Entity\Dom\DomSousTypeDocument;
use App\Entity\Dom\DomCategorie;
use App\Entity\Dom\DomRmq;
use App\Entity\Dom\DomSite;
use App\Entity\Dom\DomIndemnite;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LoadDomDataCommand extends Command
{
    protected static $defaultName = 'app:load-dom-data';
    protected static $defaultDescription = 'Charge les données nécessaires pour le module DOM';

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('🔄 Chargement des données DOM...');

        try {
            // 1. Créer les sous-types de documents
            $output->writeln('1. Création des sous-types de documents...');

            $sousTypes = [
                'MISSION' => 'MISSION',
                'COMPLEMENT' => 'COMPLEMENT',
                'MUTATION' => 'MUTATION',
                'FRAIS EXCEPTIONNEL' => 'FRAIS EXCEPTIONNEL',
                'TROP PERCU' => 'TROP PERCU',
            ];

            $sousTypeEntities = [];
            foreach ($sousTypes as $code) {
                $existing = $this->em->getRepository(DomSousTypeDocument::class)->findOneBy(['codeSousType' => $code]);
                if (!$existing) {
                    $sousType = new DomSousTypeDocument();
                    $sousType->setCodeSousType($code);
                    $this->em->persist($sousType);
                    $sousTypeEntities[$code] = $sousType;
                    $output->writeln("   ✓ Créé: $code");
                } else {
                    $sousTypeEntities[$code] = $existing;
                    $output->writeln("   ✓ Existe déjà: $code");
                }
            }

            // 2. Créer les RMQ
            $output->writeln('');
            $output->writeln('2. Création des RMQ...');

            $rmqStd = $this->em->getRepository(DomRmq::class)->findOneBy(['description' => 'STD']);
            if (!$rmqStd) {
                $rmqStd = new DomRmq();
                $rmqStd->setDescription('STD');
                $this->em->persist($rmqStd);
                $output->writeln('   ✓ Créé: STD');
            } else {
                $output->writeln('   ✓ Existe déjà: STD');
            }

            $rmq50 = $this->em->getRepository(DomRmq::class)->findOneBy(['description' => '50']);
            if (!$rmq50) {
                $rmq50 = new DomRmq();
                $rmq50->setDescription('50');
                $this->em->persist($rmq50);
                $output->writeln('   ✓ Créé: 50');
            } else {
                $output->writeln('   ✓ Existe déjà: 50');
            }

            // 3. Créer les sites
            $output->writeln('');
            $output->writeln('3. Création des sites...');

            $sites = [
                'AUTRES VILLES',
                'HORS TANA MOINS DE 24H',
                'ZONES ENCLAVEES',
                'ZONES TOURISTIQUES',
                'FORT-DAUPHIN',
                'AUTRES SITE ENCLAVES',
                'HORS TANA',
                'TANA'
            ];

            $siteEntities = [];
            foreach ($sites as $nomZone) {
                $existing = $this->em->getRepository(DomSite::class)->findOneBy(['nomZone' => $nomZone]);
                if (!$existing) {
                    $site = new DomSite();
                    $site->setNomZone($nomZone);
                    $this->em->persist($site);
                    $siteEntities[$nomZone] = $site;
                    $output->writeln("   ✓ Créé: $nomZone");
                } else {
                    $siteEntities[$nomZone] = $existing;
                    $output->writeln("   ✓ Existe déjà: $nomZone");
                }
            }

            // 4. Créer les catégories
            $output->writeln('');
            $output->writeln('4. Création des catégories...');

            $categories = [
                'Agents de maitrise, employes specialises' => 'MISSION',
                'Cadre HC' => 'MISSION',
                'Chef de service' => 'MISSION',
                'Ouvriers et chauffeurs' => 'MISSION',
                'Toute Categorie' => 'MUTATION',
            ];

            $categorieEntities = [];
            foreach ($categories as $description => $sousTypeCode) {
                $existing = $this->em->getRepository(DomCategorie::class)->findOneBy(['description' => $description]);
                if (!$existing) {
                    $categorie = new DomCategorie();
                    $categorie->setDescription($description);
                    if (isset($sousTypeEntities[$sousTypeCode])) {
                        $categorie->setDomSousTypeDocumentId($sousTypeEntities[$sousTypeCode]);
                    }
                    $this->em->persist($categorie);
                    $categorieEntities[$description] = $categorie;
                    $output->writeln("   ✓ Créé: $description");
                } else {
                    $categorieEntities[$description] = $existing;
                    $output->writeln("   ✓ Existe déjà: $description");
                }
            }

            // 5. Créer quelques indemnités
            $output->writeln('');
            $output->writeln('5. Création des indemnités...');

            $indemnites = [
                ['montant' => 50000, 'site' => 'ZONES TOURISTIQUES', 'categorie' => 'Chef de service', 'rmq' => $rmqStd, 'sousType' => 'MISSION'],
                ['montant' => 15000, 'site' => 'HORS TANA MOINS DE 24H', 'categorie' => 'Chef de service', 'rmq' => $rmqStd, 'sousType' => 'MISSION'],
                ['montant' => 45000, 'site' => 'ZONES ENCLAVEES', 'categorie' => 'Chef de service', 'rmq' => $rmqStd, 'sousType' => 'MISSION'],
                ['montant' => 40000, 'site' => 'AUTRES VILLES', 'categorie' => 'Chef de service', 'rmq' => $rmqStd, 'sousType' => 'MISSION'],
                ['montant' => 45000, 'site' => 'AUTRES VILLES', 'categorie' => 'Cadre HC', 'rmq' => $rmqStd, 'sousType' => 'MISSION'],
            ];

            foreach ($indemnites as $indemniteData) {
                $indemnite = new DomIndemnite();
                $indemnite->setMontant($indemniteData['montant']);
                $indemnite->setDomSiteId($siteEntities[$indemniteData['site']]);
                $indemnite->setDomCategorieId($categorieEntities[$indemniteData['categorie']]);
                $indemnite->setDomRmqId($indemniteData['rmq']);
                $indemnite->setDomSousTypeDocumentId($sousTypeEntities[$indemniteData['sousType']]);
                $this->em->persist($indemnite);
                $output->writeln("   ✓ Créé: {$indemniteData['categorie']} - {$indemniteData['site']} - {$indemniteData['montant']}");
            }

            // Sauvegarder en base
            $this->em->flush();

            $output->writeln('');
            $output->writeln('🎉 Données DOM chargées avec succès !');
            $output->writeln('Vous pouvez maintenant tester le formulaire DOM.');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln('❌ Erreur: ' . $e->getMessage());
            $output->writeln('Stack trace:');
            $output->writeln($e->getTraceAsString());
            return Command::FAILURE;
        }
    }
}
