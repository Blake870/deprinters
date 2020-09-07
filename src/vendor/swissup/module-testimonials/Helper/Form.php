<?php
namespace Swissup\Testimonials\Helper;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Helper\View as CustomerViewHelper;

/**
 * Testimonials form helper
 */
class Form extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Customer\Helper\View
     */
    protected $customerViewHelper;

    /**
     * Testimonials form data
     */
    protected $data;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param CustomerViewHelper $customerViewHelper
     * @param \Magento\Framework\Session\Generic $testimonialSession
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        CustomerViewHelper $customerViewHelper,
        \Magento\Framework\Session\Generic $testimonialSession
    ) {
        $this->customerSession = $customerSession;
        $this->customerViewHelper = $customerViewHelper;
        $this->data = $testimonialSession->getFormData(true);
        parent::__construct($context);
    }

    /**
     * Get user name
     *
     * @return string
     */
    public function getUserName()
    {
        if (!empty($this->data['name'])) {
            return $this->data['name'];
        }
        if (!$this->customerSession->isLoggedIn()) {
            return '';
        }
        /**
         * @var \Magento\Customer\Api\Data\CustomerInterface $customer
         */
        $customer = $this->customerSession->getCustomerDataObject();

        return trim($this->customerViewHelper->getCustomerName($customer));
    }

    /**
     * Get user email
     *
     * @return string
     */
    public function getUserEmail()
    {
        if (!empty($this->data['email'])) {
            return $this->data['email'];
        }
        if (!$this->customerSession->isLoggedIn()) {
            return '';
        }
        /**
         * @var CustomerInterface $customer
         */
        $customer = $this->customerSession->getCustomerDataObject();

        return $customer->getEmail();
    }

    /**
     * Get company
     *
     * @return string
     */
    public function getCompany()
    {
        return $this->data['company'];
    }

    /**
     * Get website
     *
     * @return string
     */
    public function getWebsite()
    {
        return $this->data['website'];
    }

    /**
     * Get twitter
     *
     * @return string
     */
    public function getTwitter()
    {
        return $this->data['twitter'];
    }

    /**
     * Get facebook
     *
     * @return string
     */
    public function getFacebook()
    {
        return $this->data['facebook'];
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->data['message'];
    }

    /**
     * Get rating
     *
     * @return string
     */
    public function getRating()
    {
        return isset($this->data['rating']) ? $this->data['rating'] : -1;
    }
}
