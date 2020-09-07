<?php

namespace Swissup\Askit\Block\Question;

use Swissup\Askit\Block\Question\AbstractBlock;
use Magento\Customer\Model\Url;

class AbstractForm extends AbstractBlock
{
    protected $formId;

    /**
     * @var \Swissup\Askit\Helper\Form
     */
    protected $formHelper;

    /**
     * @var \Magento\Customer\Model\Url
     */
    protected $customerUrl;

    /**
     * @var \Magento\Framework\Url\EncoderInterface
     */
    protected $urlEncoder;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\SessionFactory $customerSessionFactory
     * @param \Swissup\Askit\Helper\Config $configHelper
     * @param \Swissup\Askit\Helper\Url $urlHelper
     * @param \Magento\Framework\Data\Helper\PostHelper $postDataHelper
     * @param \Swissup\Askit\Model\Vote\Factory $voteFactory
     * @param Url $customerUrl
     * @param \Magento\Framework\Url\EncoderInterface $urlEncoder
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
        \Swissup\Askit\Helper\Form $formHelper,
        \Magento\Customer\Model\Url $customerUrl,
        \Magento\Framework\Url\EncoderInterface $urlEncoder,
        array $data = []
    ) {
        $this->formHelper = $formHelper
            ->setFormId($this->formId);
        $this->customerUrl = $customerUrl;
        $this->urlEncoder = $urlEncoder;
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
     * Return login URL
     * @return string
     */
    public function getLoginLink()
    {
        $queryParam = $this->urlEncoder->encode(
            $this->getUrl('*/*/*', ['_current' => true])
        );
        return $this->getUrl(
            'customer/account/login/',
            [Url::REFERER_QUERY_PARAM_NAME => $queryParam]
        );
    }

    /**
     * Return register URL
     *
     * @return string
     */
    public function getRegisterUrl()
    {
        return $this->customerUrl->getRegisterUrl();
    }

    /**
     *
     * @return \Swissup\Askit\Helper\Form
     */
    public function getFormHelper()
    {
        return $this->formHelper;
    }

    protected function _prepareLayout()
    {
        $this->setChild(
            'captcha',
            $this->getLayout()->createBlock('Magento\Captcha\Block\Captcha')
                ->setFormId($this->formId)
                ->setImgWidth(230)
                ->setImgHeight(50)
        );
        return parent::_prepareLayout();
    }
}
