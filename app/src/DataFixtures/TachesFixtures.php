<?php

namespace App\DataFixtures;

use App\Entity\Taches;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TachesFixtures extends Fixture
{
    public const TACHE_1_REFERENCE = 'tache-1';
    public const TACHE_2_REFERENCE = 'tache-2';
    public const TACHE_3_REFERENCE = 'tache-3';
    public const TACHE_4_REFERENCE = 'tache-4';   
    public const TACHE_5_REFERENCE = 'tache-5';
    public const TACHE_6_REFERENCE = 'tache-6';
    
    public function load(ObjectManager $manager): void
    {
        // Création des tâches
        $tache1 = new Taches();
        $tache1->setTitre('Nettoyage physique des ordinateurs');
        $tache1->setId(1);
        $manager->persist($tache1);

        $tache2 = new Taches();
        $tache2->setTitre('Mettre à jour SAP');
		$tache2->setId(2);
        $manager->persist($tache2);

        $tache3 = new Taches();
        $tache3->setTitre('Analyse antivirus');
		$tache3->setId(3);
        $manager->persist($tache3);

        $tache4 = new Taches();
        $tache4->setTitre('Défragmentation du disque dur');
		$tache4->setId(4);
        $manager->persist($tache4);

        $tache5 = new Taches();
        $tache5->setTitre('Analyse des pare-feu');
		$tache5->setId(5);
        $manager->persist($tache5);

        $tache6 = new Taches();
        $tache6->setTitre('Nettoyer les fichiers temporaires');
		$tache6->setId(6);
        $manager->persist($tache6);

        // Définition des références pour les tâches
        $this->addReference(self::TACHE_1_REFERENCE, $tache1);
        $this->addReference(self::TACHE_2_REFERENCE, $tache2);
        $this->addReference(self::TACHE_3_REFERENCE, $tache3);
        $this->addReference(self::TACHE_4_REFERENCE, $tache4);
        $this->addReference(self::TACHE_5_REFERENCE, $tache5);
        $this->addReference(self::TACHE_6_REFERENCE, $tache6);

        $manager->flush();
    }
}
