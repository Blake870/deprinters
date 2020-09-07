<?php

namespace Swissup\Ajaxsearch\Model\Adapter\Mysql;

class ScoreBuilder extends \Magento\Framework\Search\Adapter\Mysql\ScoreBuilder
{
    /**
     * @var \Swissup\Ajaxsearch\Helper\Mysql\Like
     */
    private $likeHelper;

    /**
     * @param \Swissup\Ajaxsearch\Helper\Mysql\Like $likeHelper
     */
    public function __construct(
        \Swissup\Ajaxsearch\Helper\Mysql\Like $likeHelper
    ) {
        $this->likeHelper = $likeHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function addCondition($score, $useWeights = true) {
        $condition = $this->likeHelper->getScoreConditionForMatchQuery($score);
        if ($condition) {
            $score = $condition;
        }

        return parent::addCondition($score, $useWeights);
    }
}
