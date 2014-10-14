<?php

namespace Application\Vk\Model;

class Reply
{
    public $id;
    public $userId;
    public $text;

    public function __construct($_id, $_userId, $_text)
    {
        $this->id = $_id;
        $this->userId = $_userId;
        $this->text = $_text;
    }

}