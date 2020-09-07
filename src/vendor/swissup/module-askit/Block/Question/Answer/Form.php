<?php

namespace Swissup\Askit\Block\Question\Answer;

use Swissup\Askit\Block\Question\AbstractForm;
use Swissup\Askit\Api\Data\MessageInterface;

class Form extends AbstractForm
{
    protected $formId = 'swissup_askit_new_answer_form';

    protected $question;

    public function setQuestion($question)
    {
        $this->question = $question;
        return $this;
    }

    public function getQuestion()
    {
        return $this->question;
    }

    public function getNewAnswerAction()
    {
        return $this->getUrl('askit/answer/save');
    }
}
