<?php
namespace OC\CommandeBundle\DataFixtures\ORM;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OC\CommandeBundle\Entity\Tarif;

class LoadTarif implements FixtureInterface {
    
    public function load(ObjectManager $manager) {
        //Liste des tarifs à ajouter
        $tarifs = array(
            'Normal' => array(
                'prix'=>16,
                'description'=> 'Pour les plus de 12 ans et moins de 60 ans',
                'nbBillets'=>1,
                'requireBirthday'=>false
            ),
            'Senior' => array(
                'prix'=>12,
                'description'=> 'Pour les 60 ans et plus',
                'nbBillets'=>1,
                'requireBirthday'=>true
            ),
            'Enfant' => array(
                'prix'=>8,
                'description'=> 'Pour les plus de 4 ans et moins de 12 ans',
                'nbBillets'=>1,
                'requireBirthday'=>true
            ),
            'Gratuit' => array(
                'prix'=>0,
                'description'=> 'Gratuit pour les moins de 4 ans',
                'nbBillets'=>1,
                'requireBirthday'=>true
            ),
            'Réduit' => array(
                'prix'=>10,
                'description'=> 'Sur présentation des justificatifs',
                'nbBillets'=>1,
                'requireBirthday'=>false
            ),
            'Famille' => array(
                'prix'=>35,
                'description'=> 'Pour 2 adultes et 2 enfants d\'une même famille',
                'nbBillets'=>4,
                'requireBirthday'=>true
            )
        );
        foreach ($tarifs as $key => $tarif) {
            $db_tarif = new Tarif();
            $db_tarif->setTarifKey($key);
            $db_tarif->setCout($tarif['prix']);
            $db_tarif->setNbBillets($tarif['nbBillets']);
            $db_tarif->setDescription($tarif['description']);
            $db_tarif->setRequireBirthday($tarif['requireBirthday']);
            $manager->persist($db_tarif);
        }
        $manager->flush();
    }
}