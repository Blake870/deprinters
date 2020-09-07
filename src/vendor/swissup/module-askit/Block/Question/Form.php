<?php

namespace Swissup\Askit\Block\Question;

use Swissup\Askit\Block\Question\AbstractForm;
use Swissup\Askit\Api\Data\MessageInterface;

class Form extends AbstractForm
{
    protected $formId = 'swissup_askit_new_question_form';

    public function isSuitablePageType()
    {
        $type = $this->getItemTypeId();
        $types = [MessageInterface::TYPE_CATALOG_PRODUCT,
            MessageInterface::TYPE_CATALOG_CATEGORY,
            MessageInterface::TYPE_CMS_PAGE
        ];
        if (!in_array($type, $types)) {
            return false;
        }

        return true;
    }
}
