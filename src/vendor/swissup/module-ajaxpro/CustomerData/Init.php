<?php

namespace Swissup\Ajaxpro\CustomerData;

use Magento\Framework;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Message\ManagerInterface as MessageManager;
use Magento\Customer\CustomerData\SectionSourceInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Swissup\Ajaxpro\CustomerData\AbstractCustomerData;

class Init extends AbstractCustomerData implements SectionSourceInterface
{
    /**
     * {@inheritdoc}
     */
    public function getSectionData()
    {
        $return = ['reinit' => $this->getBlockHtml('ajaxpro.init', ['default'])];
        $this->flushLayouts();

        return $return;
    }
}
