<?php

namespace Swissup\Ajaxsearch\Helper\Mysql;

use Magento\Framework\DB\Helper\Mysql\Fulltext;
use Magento\Framework\App\ResourceConnection;

/**
 * Class for \Magento\Framework\Search\Adapter\Mysql\Query\Builder\Match.
 * Generates LIKE condition instead of MATCH(...) AGAINST(...).
 */
class Like extends Fulltext
{
    /**
     * @var array
     */
    private $scoreCondition = [];

    /**
     * @var \Swissup\Ajaxsearch\Helper\Data
     */
    private $helper;

    /**
     * @param \Swissup\Ajaxsearch\Helper\Data $helper
     * @param ResourceConnection              $resource
     */
    public function __construct(
        \Swissup\Ajaxsearch\Helper\Data $helper,
        ResourceConnection $resource
    ) {
        $this->helper = $helper;
        parent::__construct($resource);
    }

    /**
     * Generates LIKE condition instead of MATCH(...) AGAINST(...).
     * {@inheritdoc}
     */
    public function getMatchQuery(
        $columns,
        $expression,
        $mode = self::FULLTEXT_MODE_NATURAL
    ) {
        if (!$this->helper->canUseMysqlLike()) {
            return parent::getMatchQuery($columns, $expression, $mode);
        }

        $words = explode(' ', $expression);
        $words = array_map([$this, 'stripOperators'], $words);
        $words = array_filter($words);
        $words = array_slice($words, 0, $this->helper->getMysqlLikeLimit());
        $conditions = [];
        $scoreConditions = [];
        foreach (is_array($columns) ? $columns : [$columns] as $column) {
            $expr = [];
            foreach ($words as $word) {
                $expr[] = "$column LIKE \"%{$word}%\"";
            }

            $conditions[] = implode(' AND ', $expr);
            $scoreConditions[] = "CHAR_LENGTH('$expression')/(CHAR_LENGTH($column)+1)";
        }

        $query = implode(' OR ', $conditions);
        $this->scoreCondition[$query] = implode(', ', $scoreConditions);
        if (count($scoreConditions) > 1) {
            $this->scoreCondition[$query] = "GREATEST($this->scoreCondition[$query])";
        }

        return $query;
    }

    /**
     * Try to find score condition for matchQuery.
     *
     * @param  string $matchQuery
     * @return string
     */
    public function getScoreConditionForMatchQuery($matchQuery)
    {
        return isset($this->scoreCondition[$matchQuery])
            ? $this->scoreCondition[$matchQuery]
            : '';
    }

    /**
     * Remove FULLTEXT operators
     *
     * @param  string $string
     * @return string
     */
    public function stripOperators($string)
    {
        $string = ltrim($string, '+');
        $string = rtrim($string, '*');
        return $string;
    }
}
