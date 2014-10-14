<?php

namespace Application\Quiz\Model;

class Question
{
    public $id;
    public $question;
    public $answer;

    public function __construct($_id, $_question, $_answer)
    {
        $this->id = $_id;
        $this->question = $_question;
        $this->answer = $_answer;
    }

}