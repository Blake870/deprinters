<?php

namespace Swissup\SeoTemplates\Model\Rule\Condition;

class Category extends \Magento\Rule\Model\Condition\Product\AbstractProduct
{
    /**
     * Customize default operator input by type mapper for some types
     *
     * @return array
     */
    public function getDefaultOperatorInputByType()
    {
        if (null === $this->_defaultOperatorInputByType) {
            parent::getDefaultOperatorInputByType();
            $this->_defaultOperatorInputByType['category'] = ['()', '!()', '==', '!='];
        }
        return $this->_defaultOperatorInputByType;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(\Magento\Framework\Model\AbstractModel $model)
    {
        $attrCode = $this->getAttribute();
        if ('category_ids' == $attrCode) {
            return $this->validateAttribute($model->getId());
        }

        return false;
    }
}
