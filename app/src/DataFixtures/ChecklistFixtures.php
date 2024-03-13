<?php

namespace App\DataFixtures;

use App\Entity\Checklists;
use App\Entity\Taches;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ChecklistFixtures extends Fixture implements DependentFixtureInterface
{
	
	    public function getDependencies()
    {
        return [
            TachesFixtures::class,
        ];
    }
	
    public function load(ObjectManager $manager): void
    {
        // Récupération des références aux tâches créées dans les fixtures précédentes
        $task1 = $this->getReference(TachesFixtures::TACHE_1_REFERENCE);
        $task2 = $this->getReference(TachesFixtures::TACHE_2_REFERENCE);
        $task3 = $this->getReference(TachesFixtures::TACHE_3_REFERENCE);
        $task4 = $this->getReference(TachesFixtures::TACHE_4_REFERENCE);
        $task5 = $this->getReference(TachesFixtures::TACHE_5_REFERENCE);
        $task6 = $this->getReference(TachesFixtures::TACHE_6_REFERENCE);
        
        // Création d'une nouvelle checklist
        $checklist = new Checklists(); // Correction du nom de la classe Checklists
        $checklist->setTitre('Maintenance préventive');
        $checklist->setId(1);

        // Ajout des tâches à la checklist
        $checklist->addTache($task1);
        $checklist->addTache($task2);
        $checklist->addTache($task3);

        // Persist et flush
        $manager->persist($checklist);

        // Création d'une nouvelle checklist
        $checklist2 = new Checklists(); // Correction du nom de la classe Checklists
        $checklist2->setTitre('Vérification de sécurité'); // Correction du titre
        $checklist->setId(2);

        // Ajout des tâches à la checklist
        $checklist2->addTache($task4);
        $checklist2->addTache($task5);
        $checklist2->addTache($task6);

        // Persist et flush
        $manager->persist($checklist2);

        $manager->flush();
    }
}
