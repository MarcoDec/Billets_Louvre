<?php
namespace MDPlanningBundle\DataFixtures\ORM;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use MDPlanningBundle\Entity\LeLouvreEvent;

class LoadLeLouvreEvent implements FixtureInterface {
    
    
    public function load(ObjectManager $manager) {
        // Ajoute dans le calendrier les 15 prochains jours
        $curDate = new \Datetime();
        
        $leLouvreEvent = new LeLouvreEvent();
        $leLouvreEvent->setStartDate($curDate);
        $leLouvreEvent->setEndDate($curDate);
        $leLouvreEvent->setTitle('Evenement du : '.$curDate->format('Y-m-d'));
        $leLouvreEvent->setDescription('Evenement créer par Fixture de Symfony');
        $leLouvreEvent->setIsOpen(true);
        
        $manager->persist($leLouvreEvent);
        
        $manager->flush();
    }

}