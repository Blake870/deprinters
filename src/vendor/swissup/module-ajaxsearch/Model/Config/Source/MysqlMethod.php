<?php

namespace Swissup\Ajaxsearch\Model\Config\Source;

class MysqlMethod implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var string
     */
    const LIKE = 'like';

    /**
     * @var string
     */
    const FULLTEXT = 'fulltext';

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::FULLTEXT, 'label' => __('FULLTEXT (Default)')],
            ['value' => self::LIKE, 'label' => __('LIKE')],
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $result = [];
        foreach ($this->toOptionArray() as $item) {
            $result[$item['value']] = $item['label'];
        }
        return $result;
    }
}
