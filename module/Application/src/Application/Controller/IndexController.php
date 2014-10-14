<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Application\Quiz;
use Application\Timer;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\Application;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\Controller\AbstractConsoleController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractConsoleController implements EventManagerAwareInterface
{
    /**
     * @return \Application\Quiz
     */
    protected function _getQuizService()
    {
        return $this->getServiceLocator()->get('Quiz');
    }

    public function startAction()
    {
        $quiz = $this->_getQuizService();
        $quiz->start();

        return '123';
    }
}
