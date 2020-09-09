<?php

namespace Gudoguy\Greeting\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Gudoguy\Greeting\Helper\PrintDealHelper;

class Index extends Action {
    /**
     * @var PrintDealHelper
     */
    private $printDealHelper;
    /**
     * @var Context
     */
    private $context;

    public function __construct(Context $context, PrintDealHelper $printDealHelper) {
        parent::__construct($context);
        $this->printDealHelper = $printDealHelper;
        $this->context = $context;
    }

    public function execute() {
        var_dump($this->printDealHelper->getCategories());
    }
}
