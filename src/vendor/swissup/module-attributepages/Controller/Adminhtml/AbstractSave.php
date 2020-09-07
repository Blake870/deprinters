<?php
namespace Swissup\Attributepages\Controller\Adminhtml;

use Magento\Backend\App\Action\Context;
use \Magento\Framework\App\Filesystem\DirectoryList;

abstract class AbstractSave extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Model\Layout\Update\ValidatorFactory
     */
    protected $validatorFactory;
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $fileSystem;
    /**
     * @var \Magento\Framework\Filesystem\Io\File
     */
    protected $ioFile;
    /**
     * upload model
     *
     * @var \Swissup\Attributepages\Model\Upload
     */
    protected $uploadModel;
    /**
     * Generic session
     *
     * @var \Magento\Framework\Session\Generic
     */
    protected $attrpageSession;
    /**
     * @param Context $context
     * @param \Magento\Framework\View\Model\Layout\Update\ValidatorFactory $validatorFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Filesystem $fileSystem
     * @param \Magento\Framework\Filesystem\Io\File $ioFile
     * @param \Magento\Framework\Session\Generic $attrpageSession
     * @param \Swissup\Attributepages\Model\Upload $uploadModel
     */
    public function __construct(
        Context $context,
        \Magento\Framework\View\Model\Layout\Update\ValidatorFactory $validatorFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Filesystem $fileSystem,
        \Magento\Framework\Filesystem\Io\File $ioFile,
        \Magento\Framework\Session\Generic $attrpageSession,
        \Swissup\Attributepages\Model\Upload $uploadModel
    ) {
        parent::__construct($context);
        $this->validatorFactory = $validatorFactory;
        $this->storeManager = $storeManager;
        $this->fileSystem = $fileSystem;
        $this->ioFile = $ioFile;
        $this->attrpageSession = $attrpageSession;
        $this->uploadModel = $uploadModel;
    }

    /**
     * Validate post data
     *
     * @param array $data
     * @return bool Return FALSE if some item is invalid
     */
    protected function _validatePostData($data)
    {
        $errorNo = true;
        if (!empty($data['layout_update_xml'])) {
            /** @var $validatorCustomLayout \Magento\Framework\View\Model\Layout\Update\Validator */
            $validatorCustomLayout = $this->validatorFactory->create();
            if (!$validatorCustomLayout->isValid($data['layout_update_xml'])) {
                $errorNo = false;
            }
            foreach ($validatorCustomLayout->getMessages() as $message) {
                $this->messageManager->addError($message);
            }
        }

        return $errorNo;
    }

    /**
     * get base image dir
     *
     * @return string
     */
    public function getBaseDir($path, $directoryCode = DirectoryList::MEDIA)
    {
        return $this->fileSystem
            ->getDirectoryWrite($directoryCode)
            ->getAbsolutePath($path);
    }
}
