<?php
namespace Swissup\Askit\Controller\Adminhtml\Message;

use Swissup\Askit\Controller\Adminhtml\AbstractMassStatus;

/**
 * Class MassEnable
 */
class MassDisable extends AbstractMassStatus
{
    /**
     * Field id
     */
    const ID_FIELD = 'main_table.id';

    /**
     * Resource collection
     *
     * @var string
     */
    protected $collectionClass = 'Swissup\Askit\Model\ResourceModel\Message\Collection';

    /**
     * item model
     *
     * @var string
     */
    protected $modelClass = 'Swissup\Askit\Model\Message';

    /**
     * item enable status
     *
     * @var boolean
     */
    protected $status = false;
}
