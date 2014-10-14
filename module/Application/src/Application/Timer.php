<?php

namespace Application;

use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerInterface;

class Timer
{
    /**
     * @var \Zend\EventManager\EventManagerInterface
     */
    protected $eventManager;

    protected $_isStopped = false;

    protected function _triggerEvent($_eventName)
    {
        if (!$this->_isStopped) {
            $this->getEventManager()->trigger($_eventName, $this);
        }
    }


    public function getEventManager()
    {
        if (!$this->eventManager) {
            $this->eventManager = new EventManager(array(
                __CLASS__,
                get_called_class(),
            ));
        }
        return $this->eventManager;
    }

    /**
     * @return boolean
     */
    public function stop()
    {
        $this->_isStopped = true;
    }

    public function start()
    {
        $secondIntervals = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 15, 30);
        $minuteIntervals = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 15, 30, 60);

        for ($i=0; !$this->_isStopped; $i++) {
            sleep(1);

            foreach ($secondIntervals as $interval) {
                if ($i % $interval == 0) {
                    if ($i / $interval == 1) {
                        $this->_triggerEvent("next{$interval}seconds");
                    }
                    $this->_triggerEvent("every{$interval}seconds");
                }
            }

            if ($i % 60 == 0) {
                foreach ($minuteIntervals as $interval) {
                    if ($i % ($interval * 60) == 0) {
                        if ($i / ($interval *60) == 1) {
                            $this->_triggerEvent("next{$interval}minutes");
                        }
                        $this->_triggerEvent("every{$interval}minutes");
                    }
                }
            }
        }
    }

}