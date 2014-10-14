<?php

namespace Application\Quiz\Model;

class Result
{
    public $userId;
    public $questionId;
    public $questions;

    public function __construct($_userId, $_questionId, $_questions = null)
    {
        $this->userId = $_userId;
        $this->questionId = $_questionId;
        $this->questions = $_questions;
    }
}