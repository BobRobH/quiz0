<?php

namespace Application\Vk\Model;

class Message
{
    public $id;
    public $userId;
    public $text;

    public function __construct($_text)
    {
        $this->text = $_text;
    }
}
