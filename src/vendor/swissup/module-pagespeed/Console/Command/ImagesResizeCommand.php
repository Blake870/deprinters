<?php
declare(strict_types=1);

namespace Swissup\Pagespeed\Console\Command;

use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Swissup\Pagespeed\Service\ImageResize;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Magento\Framework\ObjectManagerInterface;

class ImagesResizeCommand extends \Symfony\Component\Console\Command\Command
{
    /**
     * @var ImageResize
     */
    private $resize;

    /**
     * @var State
     */
    private $appState;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param State $appState
     * @param ImageResize $resize
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        State $appState,
        ImageResize $resize,
        ObjectManagerInterface $objectManager
    ) {
        parent::__construct();
        $this->resize = $resize;
        $this->appState = $appState;
        $this->objectManager = $objectManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('swissup:pagespeed:images:resize')
            ->setDescription('Creates resized product images and their responsive images 0.5x 0.75x 2x 3x');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->appState->setAreaCode(Area::AREA_GLOBAL);

            $generators['Custom images resized successfully'] = $this->resize->resizeCustomImages();
            $generators['Product responsive images resized successfully'] = $this->resize->resizeAllProductImages();

            foreach ($generators as $label => $generator) {
                /** @var ProgressBar $progress */
                $progress = $this->objectManager->create(ProgressBar::class, [
                    'output' => $output,
                    'max' => $generator->current()
                ]);
                $progress->setFormat(
                    "%current%/%max% [%bar%] %percent:3s%% %elapsed% %memory:6s% \t| <info>%message%</info>"
                );

                if ($output->getVerbosity() !== OutputInterface::VERBOSITY_NORMAL) {
                    $progress->setOverwrite(false);
                }

                $progress->setMessage('');
                // update every 100 iterations
                $progress->setRedrawFrequency(10);
                $progress->setBarWidth(50);
                $progress->start();

                for (; $generator->valid(); $generator->next()) {
                    $progress->setMessage($generator->key());
                    $progress->advance();
                }
                $progress->finish();

                $output->write(PHP_EOL);
                $output->writeln("<info>{$label}</info>");
            }
        } catch (\Exception $e) {
            $output->writeln("<error>{$e->getMessage()}</error>");
            // $output->writeln("<error>{$e->getTraceAsString()}</error>");
            // we must have an exit code higher than zero to indicate something was wrong
            return \Magento\Framework\Console\Cli::RETURN_FAILURE;
        }

        $output->write(PHP_EOL);
        $output->writeln("<info>Done!</info>");
    }
}
