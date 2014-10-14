<?php

namespace Application;

use Zend\EventManager\EventManagerInterface;

trait EventManagerAwareTrait
{
    /**
     * @var \Zend\EventManager\EventManagerInterface
     */
    protected $eventManager;

    public function setEventManager(EventManagerInterface $eventManager)
    {
        $eventManager->setIdentifiers(array(
            __CLASS__,
            get_called_class(),
        ));
        $this->eventManager = $eventManager;
        return $this;
    }

    public function getEventManager()
    {
        return $this->eventManager;
    }
}