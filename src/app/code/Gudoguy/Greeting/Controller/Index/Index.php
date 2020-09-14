<?php

namespace Gudoguy\Greeting\Controller\Index;

use Gudoguy\DeprintersSync\Model\Category;
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
    /**
     * @var Category
     */
    private $category;

    public function __construct(Context $context, PrintDealHelper $printDealHelper,
            Category $category) {
        parent::__construct($context);
        $this->printDealHelper = $printDealHelper;
        $this->context = $context;
        $this->category = $category;
    }

    public function execute() {
        $this->category->sync();
        //$this->category->sync();
        //echo $this->printDealHelper->getProductAttributes('b0ade72d-cb45-423c-b891-2b02e3b3c9b3');
    }
}
