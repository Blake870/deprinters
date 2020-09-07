<?php

namespace Swissup\Pagespeed\Image\Optimizers;

use Spatie\ImageOptimizer\Image;

class Cwebp extends \Spatie\ImageOptimizer\Optimizers\BaseOptimizer
{
    public $binaryName = 'cwebp';

    /**
     *
     * @param  Image  $image
     * @return boolean
     */
    public function canHandle(Image $image): bool
    {
        return in_array($image->mime(), [
            'image/png',
            'image/jpeg',
        ]);
    }

    /**
     *
     * @return string
     */
    public function getCommand(): string
    {
        $optionString = implode(' ', $this->options);

        $extension = pathinfo($this->imagePath, PATHINFO_EXTENSION);
        // $outputFile = preg_replace('/' . $extension . '$/', 'webp', $this->imagePath); // old before #18
        $outputFile = preg_replace('/' . $extension . '$/', $extension . '.webp', $this->imagePath);

        return "\"{$this->binaryPath}{$this->binaryName}\" {$optionString}"
            . ' ' . escapeshellarg($this->imagePath)
            . ' -o ' . escapeshellarg($outputFile);
    }
}
