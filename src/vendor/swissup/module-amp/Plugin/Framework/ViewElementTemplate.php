<?php
namespace Swissup\Amp\Plugin\Framework;

class ViewElementTemplate
{
    /**
     * @var \Swissup\Amp\Helper\Data
     */
    protected $helper;

    /**
     * @param \Swissup\Amp\Helper\Data $helper
     */
    public function __construct(
        \Swissup\Amp\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * Get a template file
     *
     * @param  \Magento\Framework\View\Element\Template $subject
     * @param  string $result
     * @return string
     */
    public function afterGetTemplate(
        \Magento\Framework\View\Element\Template $subject,
        string $result = null
    ) {
        if (!$this->helper->canUseAmp()) {
            return $result;
        }

        $node = $this->helper->getConfig('swissup_amp/whitelist/block_types');
        $blocks = [];
        foreach ($node as $nodeName => $blockTypes) {
            $blocks = array_merge($blocks, array_values($blockTypes));
        }

        $blockClass = $this->helper->getCleanClass($subject);
        $current = array_filter($blocks, function ($var) use ($blockClass) {
            return ($var['class'] == $blockClass);
        });

        $templateColumn = array_column($current, 'template');
        if (count($templateColumn)) {
            $result = $templateColumn[0];
        }

        return $result;
    }
}
