<?php

namespace MDPlanningBundle\EventListener;

use ADesigns\CalendarBundle\Event\CalendarEvent;
use ADesigns\CalendarBundle\Entity\EventEntity;
use Doctrine\ORM\EntityManager;

class CalendarEventListener {
    private $entityManager;
    
    public function __construct(EntityManager $entityManager) {
        $this->entityManager = $entityManager;
    }
    
    public function loadEvents (CalendarEvent $calendarEvent) {
        $startDate=$calendarEvent->getStartDatetime();
        $endDate = $calendarEvent->getEndDatetime();
        $request = $calendarEvent->getRequest();
        $filter= $request->get('filter');
        
        // $companyEvents = $this->entityManager->getRepository('MDPlanningBundle:LeLouvreEvent')->findAll();
        $companyEvents = $this->entityManager->getRepository('MDPlanningBundle:LeLouvreEvent')
                          ->createQueryBuilder('le_louvre_events')
                          ->where('le_louvre_events.startDate BETWEEN :startDate and :endDate')
                          ->setParameter('startDate', $startDate->format('Y-m-d H:i:s'))
                          ->setParameter('endDate', $endDate->format('Y-m-d H:i:s'))
                          ->getQuery()->getResult();
        
        foreach($companyEvents as $companyEvent) {
            $eventEntity = new EventEntity($companyEvent->getTitle(), $companyEvent->getStartDate(), null, true);
            
            //Optional calendar event settings
            $eventEntity->setAllDay(true);
            $eventEntity->setBgColor('#FF0000');
            $eventEntity->setFgColor('#FFFFFF');
            $eventEntity->setUrl('http://google.com');
            $eventEntity->setCssClass('my-custom-css-class');
            
            //Finally, add the event to the CalendarEvent for displaying on the calendar
            $calendarEvent->addEvent($eventEntity);
        }
        
        
    }
}