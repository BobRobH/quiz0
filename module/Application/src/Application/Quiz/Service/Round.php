<?php

namespace Application\Quiz\Service;

use Application\Quiz\Model;
use Application\Quiz\Mapper;

class Round
{
    /** @var Mapper\Result */
    protected $_resultMapper;

    protected function _getResultMapper()
    {
        if (!$this->_resultMapper) {
            $this->_resultMapper = new Mapper\Result();
        }
        return $this->_resultMapper;
    }

    public function saveResult($_userId, $_questionId)
    {
        $result = new Model\Result($_userId, $_questionId);
        $this->_getResultMapper()->save($result);
    }
}
