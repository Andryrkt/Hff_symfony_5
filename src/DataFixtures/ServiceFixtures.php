<?php

namespace App\DataFixtures;

use App\Entity\Service;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ServiceFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Service magasin
        $neg = new Service();
        $neg->setCode('NEG');
        $neg->setNom('MAGASIN');
        $manager->persist($neg);
        $this->addReference('service_neg', $neg);

        // Service commercial
        $com = new Service();
        $com->setCode('COM');
        $com->setNom('COMMERCIAL');
        $manager->persist($com);
        $this->addReference('service_com', $com);

        // Service atelier
        $ate = new Service();
        $ate->setCode('ATE');
        $ate->setNom('ATELIER');
        $manager->persist($ate);
        $this->addReference('service_ate', $ate);

        // Service customer support
        $csp = new Service();
        $csp->setCode('CSP');
        $csp->setNom('CUSTOMER SUPPORT');
        $manager->persist($csp);
        $this->addReference('service_csp', $csp);

        //service garantie
        $gar = new Service();
        $gar->setCode('GAR');
        $gar->setNom('GARANTIE');
        $manager->persist($gar);
        $this->addReference('service_gar', $gar);
        
        //service formation
        $for = new Service();
        $for->setCode('FOR');
        $for->setNom('FORMATION');
        $manager->persist($for);
        $this->addReference('service_for', $for);
        
        //service assurance
        $ass = new Service();
        $ass->setCode('ASS');
        $ass->setNom('ASSURANCE');
        $manager->persist($ass);
        $this->addReference('service_ass', $ass);
        
        //service energie man
        $man = new Service();
        $man->setCode('MAN');
        $man->setNom('ENERGIE MAN');
        $manager->persist($man);
        $this->addReference('service_man', $man);
        
        //service location
        $lcd = new Service();
        $lcd->setCode('LCD');
        $lcd->setNom('LOCATION');
        $manager->persist($lcd);
        $this->addReference('service_lcd', $lcd);
        
//service direction generale
$dir = new Service();
$dir->setCode('DIR');
$dir->setNom('DIRECTION GENERALE');
$manager->persist($dir);
$this->addReference('service_dir', $dir);

//service finance
$fin = new Service();
$fin->setCode('FIN');
$fin->setNom('FINANCE');
$manager->persist($fin);
$this->addReference('service_fin', $fin);

//service personnel et securite
$per = new Service();
$per->setCode('PER');
$per->setNom('PERSONNEL ET SECURITE');
$manager->persist($per);
$this->addReference('service_per', $per);

//service informatique
$inf = new Service();
$inf->setCode('INF');
$inf->setNom('INFORMATIQUE');
$manager->persist($inf);
$this->addReference('service_inf', $inf);

//service immobilier
$imm = new Service();
$imm->setCode('IMM');
$imm->setNom('IMMOBILIER');
$manager->persist($imm);
$this->addReference('service_imm', $imm);

//service transit
$tra = new Service();
$tra->setCode('TRA');
$tra->setNom('TRANSIT');
$manager->persist($tra);
$this->addReference('service_tra', $tra);

//service approvisionnement
$app = new Service();
$app->setCode('APP');
$app->setNom('APPRO');
$manager->persist($app);
$this->addReference('service_app', $app);

//service unite methode et procedures
$ump = new Service();
$ump->setCode('UMP');
$ump->setNom('UNITE METHODE ET PROCEDURES');
$manager->persist($ump);
$this->addReference('service_ump', $ump);

//service engineerie et realisations
$eng = new Service();
$eng->setCode('ENG');
$eng->setNom('ENGINEERIE ET REALISATIONS');
$manager->persist($eng);
$this->addReference('service_eng', $eng);

//service vanille
$van = new Service();
$van->setCode('VAN');
$van->setNom('VANILLE');
$manager->persist($van);
$this->addReference('service_van', $van);

//service girofle
$gir = new Service();
$gir->setCode('GIR');
$gir->setNom('GIROFLE');
$manager->persist($gir);
$this->addReference('service_gir', $gir);

//service thomson
$tho = new Service();
$tho->setCode('THO');
$tho->setNom('THOMSON');
$manager->persist($tho);
$this->addReference('service_tho', $tho);

//service tsiazompaniry
$tsi = new Service();
$tsi->setCode('TSI');
$tsi->setNom('TSIAZOMPANIRY');
$manager->persist($tsi);
$this->addReference('service_tsi', $tsi);

//service location tamatave
$ltv = new Service();
$ltv->setCode('LTV');
$ltv->setNom('LOCATION TAMATAVE');
$manager->persist($ltv);
$this->addReference('service_ltv', $ltv);

//service location fort dauphin
$lfd = new Service();
$lfd->setCode('LFD');
$lfd->setNom('LOCATION FORT DAUPHINE');
$manager->persist($lfd);
$this->addReference('service_lfd', $lfd);

//service location moramanga
$lbv = new Service();
$lbv->setCode('LBV');
$lbv->setNom('LOCATION MORAMANGA');
$manager->persist($lbv);
$this->addReference('service_lbv', $lbv);

//service mahajanga
$mah = new Service();
$mah->setCode('MAH');
$mah->setNom('MAHAJANGA');
$manager->persist($mah);
$this->addReference('service_mah', $mah);

//service nosy be
$nos = new Service();
$nos->setCode('NOS');
$nos->setNom('NOSY BE');
$manager->persist($nos);
$this->addReference('service_nos', $nos);

//service toliara
$tul = new Service();
$tul->setCode('TUL');
$tul->setNom('TOLIARA');
$manager->persist($tul);
$this->addReference('service_tul', $tul);

//service ambohimanambola
$amb = new Service();
$amb->setCode('AMB');
$amb->setNom('AMBOHIMANAMBOLA');
$manager->persist($amb);
$this->addReference('service_amb', $amb);

//service flexibles
$fle = new Service();
$fle->setCode('FLE');
$fle->setNom('FLEXIBLE');
$manager->persist($fle);
$this->addReference('service_fle', $fle);

//service tsiroanomandidy
$tsd = new Service();
$tsd->setCode('TSD');
$tsd->setNom('TSIROANOMANDIDY');
$manager->persist($tsd);
$this->addReference('service_tsd', $tsd);

//service vatomandry
$vat = new Service();
$vat->setCode('VAT');
$vat->setNom('VATOMANDRY');
$manager->persist($vat);
$this->addReference('service_vat', $vat);

//service belobaka
$blk = new Service();
$blk->setCode('BLK');
$blk->setNom('BELOBABA');
$manager->persist($blk);
$this->addReference('service_blk', $blk);

//service engineerie et realisations
$eng = new Service();
$eng->setCode('ENG');
$eng->setNom('ENGINEERIE ET REALISATIONS');
$manager->persist($eng);
$this->addReference('service_eng', $eng);

//service mobile assets
$mas = new Service();
$mas->setCode('MAS');
$mas->setNom('MOBILE ASSETS');
$manager->persist($mas);
$this->addReference('service_mas', $mas);

//service marche public
$map = new Service();
$map->setCode('MAP');
$map->setNom('MARCHE PUBLIC');
$manager->persist($map);
$this->addReference('service_map', $map);

//service administration
$adm = new Service();
$adm->setCode('ADM');
$adm->setNom('ADMINISTRATION');
$manager->persist($adm);
$this->addReference('service_adm', $adm);

//service levage dmsa
$lev = new Service();
$lev->setCode('LEV');
$lev->setNom('LEVAGE DM');
$manager->persist($lev);
$this->addReference('service_lev', $lev);

//service location rn6
$lr6 = new Service();
$lr6->setCode('LR6');
$lr6->setNom('LOCATION RN6');
$manager->persist($lr6);
$this->addReference('service_lr6', $lr6);

//service location star
$lst = new Service();
$lst->setCode('LST');
$lst->setNom('LOCATION STAR');
$manager->persist($lst);
$this->addReference('service_lst', $lst);

//service location centrale jirama
$lcj = new Service();
$lcj->setCode('LCJ');
$lcj->setNom('LOCATION CENTRALE JIRAMA');
$manager->persist($lcj);
$this->addReference('service_lcj', $lcj);

//service tsiazompaniry
$tsi = new Service();
$tsi->setCode('TSI');
$tsi->setNom('TSIAZOMPANIRY');
$manager->persist($tsi);
$this->addReference('service_tsi', $tsi);

// service solaire
$slr = new Service();
$slr->setCode('SLR');
$slr->setNom('SOLAIRE');
$manager->persist($slr);
$this->addReference('service_slr', $slr);

// service location groupes
$lgr = new Service();
$lgr->setCode('LGR');
$lgr->setNom('LOCATION GROUPES');
$manager->persist($lgr);
$this->addReference('service_lgr', $lgr);

// service location samcrette
$lsc = new Service();
$lsc->setCode('LSC');
$lsc->setNom('LOCATION SAMCRETTE');
$manager->persist($lsc);
$this->addReference('service_lsc', $lsc);

// service travel airways
$c1 = new Service();
$c1->setCode('C1');
$c1->setNom('TRAVEL AIRWAYS');
$manager->persist($c1);
$this->addReference('service_c1', $c1);

        

        $manager->flush();
    }
}
