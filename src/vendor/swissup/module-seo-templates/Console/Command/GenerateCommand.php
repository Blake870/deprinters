<?php

namespace Swissup\SeoTemplates\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Swissup\SeoTemplates\Model\Generator;
use Swissup\SeoTemplates\Model\Cron;

class GenerateCommand extends Command
{
    const INPUT_OPTION_PAGE_SIZE = 'page-size';

    const INPUT_OPTION_FORCE = 'force';

    /**
     * @var Generator
     */
    protected $generator;

    /**
     * @var Cron\State
     */
    protected $jobState;

    /**
     * @param \Magento\Framework\App\State $appState
     * @param Cron\State                   $jobState
     */
    public function __construct(
        \Magento\Framework\App\State $appState,
        Cron\State $jobState
    ) {
        $this->jobState = $jobState;
        // Fix for earlier versions of Magento 2.2.x.
        // Emulate Admin Area when getting instance of Template model.
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->generator = $appState->emulateAreaCode(
            \Magento\Framework\App\Area::AREA_ADMINHTML,
            [$objectManager, 'get'],
            [Generator::class]
        );

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->addOption(
            self::INPUT_OPTION_PAGE_SIZE,
            null,
            InputOption::VALUE_REQUIRED,
            'Number of items to process per iteration.'
        );

        $this->addOption(
            self::INPUT_OPTION_FORCE,
            null,
            InputOption::VALUE_NONE,
            'Force generate even when respective Cron job is running.'
        );

        $this->setName('swissup:seotemplates:generate')
            ->setDescription('(Re)Generate metadata based using templates of Swissup SEO Templates');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $isForced = $input->getOption(self::INPUT_OPTION_FORCE);
        $state = $this->jobState->get();
        if ($state->getStatus() === Cron::STATUS_PENDING
            || $state->getStatus() === Cron::STATUS_RUNNING
        ) {
            $output->writeln('There is active Cron job `swissup_seotemplates_generate`. It generates metadata currently.');
            $output->writeln('Please wait till it ends...');

            return;
        }

        $pageSize = (int) $input->getOption(self::INPUT_OPTION_PAGE_SIZE);
        if ($pageSize) {
            $this->generator->setPageSize($pageSize);
        }

        $types = $this->generator->getAvailableEntityTypes(false);
        $this->generator->claerTemplatesLogs(array_keys($types));
        $output->writeln('Template logs cleared.');
        $this->generator->clearGeneratedData(array_keys($types));
        $output->writeln('Old generated metadata cleared.');
        foreach ($types as $code => $name) {
            $size = $this->generator->getCollectionForEntityType($code)->getSize();
            $output->writeln("{$name} - {$size} items.");
            $types[$code] = [
                'name' => $name,
                'size' => $size
            ];
        }

        // generate
        $pageSize = $this->generator->getPageSize();
        $output->writeln("Items to process per iteration - {$pageSize}.");
        foreach ($types as $entityType => $type) {
            $count = $type['size'];
            $lastPage = $pageSize ? (ceil($count / $pageSize)) : 1;
            $progressBar = new ProgressBar($output, $count);
            $progressBar->setFormat("Metadata for {$type['name']} - [%bar%] %percent:3s%%" . PHP_EOL);
            $progressBar->start();
            $curPage = 1;
            do {
                $this->generator
                    ->setPageSize($pageSize)
                    ->setCurPage($curPage)
                    ->setEntityType($entityType)
                    ->generate();
                $progressBar->advance($pageSize);
                if ($this->generator->getNextPage() == $curPage) {
                    $curPage = null;
                    $output->write(PHP_EOL);
                    $output->writeln("Generate suddenly stopped at page number {$curPage}...");
                } else {
                    $curPage = $this->generator->getNextPage();
                }

            } while ($curPage);
        }

        // complete
        $output->writeln('Done.');
    }
}
