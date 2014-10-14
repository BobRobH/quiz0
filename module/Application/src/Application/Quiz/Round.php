<?php

namespace Application\Quiz;

use Application\EventManagerAwareTrait;
use Application\Quiz\Model\Question;
use Application\Timer;
use Application\Vk;
use Application\Quiz\Service;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;


class Round implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    protected $_roundMaxMinutes = 9;

    /** @var  Timer */
    protected $_timer;

    /** @var  Question */
    protected $_question;

    /** @var  int */
    protected $_questionMessageId;


    /** @var  Vk\Service */
    protected function _getVkService()
    {
        return $this->getServiceLocator()->get('Vk\Service');
    }

    /** @var Service\Round  */
    protected function _getRoundService()
    {
        return $this->getServiceLocator()->get('Quiz\Service\Round');
    }

    protected function _getQuestionMapper()
    {
        return $this->getServiceLocator()->get('Quiz\Mapper\Question');
    }

    protected function _getTips()
    {
        $tips = array();

        $indexes = range(0, mb_strlen($this->_question->answer)-1);
        shuffle($indexes);
        $lastTip = str_pad('', mb_strlen($this->_question->answer), '*');

        for ($i=0; $i<count($indexes)-1; $i++) {
            $nextLetter = mb_substr($this->_question->answer, $indexes[$i], 1);
            $nextTip =
                mb_substr($lastTip, 0, $indexes[$i]) . $nextLetter .
                mb_substr($lastTip, $indexes[$i] + 1);

            $tips[] = $nextTip;

            $lastTip = $nextTip;
        }

        return $tips;
    }

    protected function _postQuestion()
    {
        $questionMapper = $this->_getQuestionMapper();
        $this->_question = $questionMapper->getRandom();
        $this->_questionMessageId =
            $this->_getVkService()->postMessage($this->_question->question);
    }

    protected function _postTip($_tipText)
    {
        $this->_getVkService()->postReply(
            $this->_questionMessageId,
            'Подсказка: ' . $_tipText
        );
    }

    protected function _saveResult($_userId)
    {
        $this->_getRoundService()->saveResult($_userId, $this->_question->id);
    }

    protected function _postResult($_userId = null)
    {
        $winnerMsg =
            "Правильный ответ: " . $this->_question->answer . "\n" .
            "\n" .
            ($_userId ? ("Победитель: @id" . $_userId) : '')
        ;
        $this->_getVkService()->changeMessage(
            $this->_questionMessageId,
            $this->_question->question . "\n" .
            "\n" .
            $winnerMsg
        );
        $this->_getVkService()->postReply($this->_questionMessageId, $winnerMsg);
    }

    protected function _processReplies()
    {
        $replies = $this->_getVkService()->getReplies($this->_questionMessageId);

        foreach ($replies as $reply) {
            if (mb_strpos(
                    mb_strtolower($reply->text),
                    mb_strtolower($this->_question->answer)
                ) !== FALSE
            ) {
                $this->_saveResult($reply->userId);
                $this->_postResult($reply->userId);
                $this->_stopRound();
                break;
            }
        }
    }

    protected function _stopRound()
    {
        $this->_timer->stop();
    }

    public function start()
    {
        $this->_timer = new Timer();
        $this->_postQuestion();
        $this->_timer->getEventManager()->attach('every10seconds', function($e) {
            $this->_processReplies();
        });
        $this->_timer->getEventManager()->attach("next{$this->_roundMaxMinutes}minutes", function($e) {
            $this->_postResult();
            $this->_stopRound();
        });

        $tips = $this->_getTips();
        for ($i=1; $i<=min(count($tips), $this->_roundMaxMinutes-1); $i++) {
            $this->_timer->getEventManager()->attach("next{$i}minutes", function($e) use($tips) {
                $eventName = $e->getName();
                $tipIndex = ((int)$eventName[4])-1;
                $this->_postTip($tips[$tipIndex]);
            });

        }

        $this->_timer->start();
    }
}