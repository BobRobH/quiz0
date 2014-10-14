<?php

namespace Application\Quiz\Mapper;

use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\TableGateway\Feature;
use Application\Quiz\Model;
use Application\Mapper;

class Result extends Mapper\Common
{
    protected  function _getThisWhereTop($_predicate)
    {
        $resultSet = $this->select(function (Select $select) use($_predicate) {
            $select->columns(array('user_id', 'questions' => new Expression('count(user_id)')));
            $select->group('user_id');
            $select->order(array('questions' => 'DESC'));
            $select->where($_predicate);
            $select->limit(53);
        });

        $results = array();

        foreach ($resultSet as $r) {
            $results[] = new Model\Result($r->user_id, null, $r->questions);
        }

        return $results;
    }

    /**
     * @return Model\Result[]
     */
    public function getThisWeekTop()
    {
        return $this->_getThisWhereTop('WEEKOFYEAR(added_time)=WEEKOFYEAR(NOW())');
    }

    public function getThisMonthTop()
    {
        return $this->_getThisWhereTop('MONTH(added_time)=MONTH(NOW())');
    }

    public function getThisYearTop()
    {
        return $this->_getThisWhereTop('YEAR(added_time)=YEAR(NOW())');
    }

    public function save(Model\Result $result)
    {
        $this->insert(array(
            'user_id' => $result->userId,
            'question_id' => $result->questionId,
        ));
    }
}
