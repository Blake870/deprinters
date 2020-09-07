<?php

namespace Swissup\Askit\Block\Question\Answer;

use Swissup\Askit\Block\Question\AbstractBlock;
use Swissup\Askit\Api\Data\MessageInterface;

class View extends AbstractBlock
{
    protected $answer;

    /**
     * @var \Zend_Filter_Interface
     */
    protected $templateProcessor;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\SessionFactory $customerSessionFactory
     * @param \Swissup\Askit\Helper\Config $configHelper
     * @param \Swissup\Askit\Helper\Url $urlHelper
     * @param \Magento\Framework\Data\Helper\PostHelper $postDataHelper
     * @param \Swissup\Askit\Model\Vote\Factory $voteFactory
     * @param \Zend_Filter_Interface $templateProcessor
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\SessionFactory $customerSessionFactory,
        \Swissup\Askit\Helper\Config $configHelper,
        \Swissup\Askit\Helper\Url $urlHelper,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        \Swissup\Askit\Model\Vote\Factory $voteFactory,
        \Zend_Filter_Interface $templateProcessor,
        array $data = []
    ) {
        $this->templateProcessor = $templateProcessor;

        parent::__construct(
            $context,
            $customerSessionFactory,
            $configHelper,
            $urlHelper,
            $postDataHelper,
            $voteFactory,
            $data
        );
    }

    /**
     *
     * @param  string $string
     * @return string
     */
    public function filterOutputHtml($string)
    {
        return $this->templateProcessor->filter($string);
    }

    public function setAnswer($answer)
    {
        $this->answer = $answer;
        return $this;
    }

    public function getAnswer()
    {
        return $this->answer;
    }
}
