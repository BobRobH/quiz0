<?php

namespace Application\Quiz\Mapper;

use \Application\Quiz\Model;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Application\Mapper;

class Question extends Mapper\Common
{
    public function getRandom()
    {
        $resultSet = $this->select(function (Select $select) {
            $select->order(new Expression('RAND()'));
            $select->limit(1);
        });
        $row = $resultSet->current();
        $question = new Model\Question($row->id, $row->question, $row->answer);
        return $question;
    }
}