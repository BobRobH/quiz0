<?php

namespace Application;

use Application\Quiz\Mapper\Result;
use Application\Quiz\Round;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

class Quiz implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    protected $_minutesBeforeTopResults = 0.5;
    protected $_minutesBetweenRounds = 1;

    protected $_postWeekResultsEveryRounds = 30;
    protected $_postMonthResultsEveryRounds = 90;
    protected $_postYearResultsEveryRounds = 180;

    protected $_resultMapper;
    protected $_vkService;

    protected function _getResultMapper()
    {
        if (!$this->_resultMapper) {
            $this->_resultMapper = new Quiz\Mapper\Result();
        }
        return $this->_resultMapper;
    }

    protected function _getVkService()
    {
        if (!$this->_vkService) {
            $this->_vkService = new Vk\Service();
        }
        return $this->_vkService;
    }

    /**
     * @return \Application\Quiz\Round
     */
    protected function _getRoundService()
    {
        return $this->getServiceLocator()->get('Quiz\Round');
    }

    private function _getListResultsText($_results)
    {
        $i = 1;
        $resultsText = '';
        foreach ($_results as $r) {
            $resultsText .= $i++ . ". @id" . $r->userId . " - " . $r->questions . "\n";
        }
        return $resultsText;
    }

    private function _postWeekResults()
    {
        $results = $this->_getResultMapper()->getThisWeekTop();
        $this->_getVkService()->postMessage(
            "Рейтинг этой недели: \n\n" .
            $this->_getListResultsText($results)
        );
    }

    private function _postMonthResults()
    {
        $results = $this->_getResultMapper()->getThisMonthTop();
        $this->_getVkService()->postMessage(
            "Рейтинг этого месяца: \n\n" .
            $this->_getListResultsText($results)
        );
    }

    private function _postYearResults()
    {
        $results = $this->_getResultMapper()->getThisYearTop();
        $this->_getVkService()->postMessage(
            "Рейтинг этого года: \n\n" .
            $this->_getListResultsText($results)
        );
    }

    public function start()
    {
        $round = $this->_getRoundService();

        for ($i=1; ; $i++) {
            $round->start();

            sleep(60 * $this->_minutesBeforeTopResults);

            if ($i % $this->_postYearResultsEveryRounds == 0) {
                $this->_postYearResults();

            } else if ($i % $this->_postMonthResultsEveryRounds == 0) {
                $this->_postMonthResults();

            } else if ($i % $this->_postWeekResultsEveryRounds == 0) {
                $this->_postWeekResults();
            }

            sleep(60 * ($this->_minutesBetweenRounds - $this->_minutesBeforeTopResults));
        }
    }
}