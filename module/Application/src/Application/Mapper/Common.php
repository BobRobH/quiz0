<?php

namespace Application\Mapper;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\TableGateway\Feature;
use Zend\Server\Reflection\ReflectionClass;

class Common extends AbstractTableGateway
{
    public function __construct()
    {
        $class = new \ReflectionClass($this);
        $this->table = strtolower($class->getShortName());
        $this->featureSet = new Feature\FeatureSet();
        $this->featureSet->addFeature(new Feature\GlobalAdapterFeature());
        $this->initialize();
    }
}