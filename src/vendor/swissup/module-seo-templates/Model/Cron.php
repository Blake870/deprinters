<?php

namespace Swissup\SeoTemplates\Model;

use Magento\Cron\Model\Schedule;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\App\Filesystem\DirectoryList;

class Cron
{
    /**
     * Config path where stored all cron related data
     */
    const CONFIG_PATH = 'swissup_seotemplates/cron_process/';

    /**
     * 'pending' - generation is started cron job can process its part
     */
    const STATUS_PENDING = 'pending';

    /**
     * 'running' - some cron job is processing its part and current cron job has to wait
     */
    const STATUS_RUNNING = 'running';

    /**
     * 'complete' - generation completed.
     */
    const STATUS_COMPLETE = 'complete';

    /**
     * @var Generator
     */
    protected $generator;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var Cron\State
     */
    protected $jobState;

    /**
     * @param Generator                                          $generator
     * @param Cron\State                                         $jobState
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Generator $generator,
        Cron\State $jobState,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->generator = $generator;
        $this->jobState = $jobState;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Generate metadata using SEO templates
     * (it has to be previously initialized with $this::init method)
     *
     * @param  Schedule $schedule
     * @return $this
     */
    public function generate(Schedule $schedule)
    {
        $state = $this->jobState->get();
        if ($state->getStatus() != self::STATUS_PENDING) {
            return $this;
        }

        $state->setStatus(self::STATUS_RUNNING);
        $this->jobState->save($state);
        $entityType = explode(',', $state->getEntityType());
        $pageSize = $state->getPageSize();
        $curPage = $state->getCurPage();
        $this->generator
            ->setPageSize($pageSize)
            ->setCurPage($curPage)
            ->setEntityType(reset($entityType))
            ->generate();
        if ($this->generator->getNextPage()) {
            $nextPage = $this->generator->getNextPage();
        } else {
            array_shift($entityType);
            $nextPage = 1;
        }

        if (count($entityType)) {
            // save data for next cron run
            $state->setEntityType(implode(',', $entityType))
                ->setPageSize($pageSize)
                ->setCurPage($nextPage)
                ->setLastRun($schedule->getExecutedAt())
                ->setStatus(self::STATUS_PENDING);
        } else {
            // clear data; generation complete
            $state->unsEntityType()
                ->unsPageSize()
                ->unsCurPage()
                // ->unsLastRun(); do not unset last run (just for info)
                ->setStatus(self::STATUS_COMPLETE);
        }

        $this->jobState->save($state);

        return $this;
    }

    /**
     * Initialize metadata generation via cron
     *
     * @return $this
     */
    public function init()
    {
        $allTypes = array_keys($this->generator->getAvailableEntityTypes(false));
        // clear previously generated data
        $this->generator->claerTemplatesLogs($allTypes);
        $this->generator->clearGeneratedData($allTypes);
        // set initial values
        $pageSize = $this->scopeConfig
            ->getValue('swissup_seotemplates/cron/page_size');
        $state = new \Magento\Framework\DataObject([
            'entity_type' => implode(',', $allTypes),
            'page_size'=> $pageSize,
            'cur_page' => 1,
            'status' => self::STATUS_PENDING
        ]);
        $this->jobState->save($state);

        return $this;
    }
}
