<?php

namespace Swissup\SeoTemplates\Model\Cron;

use Magento\Framework\DataObject;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\App\Filesystem\DirectoryList;

class State
{
    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * @var string
     */
    protected $filename;

    public function __construct(
        \Magento\Framework\Filesystem $filesystem,
        $filename = 'swissup_seotemplates_generate.json'
    ) {
        $this->filesystem = $filesystem;
        $this->filename = $filename;
    }

    /**
     * Read run data from file and return as object.
     *
     * @return DataObject
     */
    public function get()
    {
        $varDirectory = $this->filesystem->getDirectoryRead(DirectoryList::VAR_DIR);
        try {
            $content = $varDirectory->readFile($this->filename);
            $data = is_string($content) ? json_decode($content, true) : [];
        } catch (FileSystemException $e) {
            // There is no such file.
            $data = [];
        }

        return new DataObject($data);
    }

    /**
     * Write run data object into file.
     *
     * @param  DataObject $object
     * @return
     */
    public function save(DataObject $object)
    {
        $varDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $file = $varDirectory->openFile($this->filename, 'w');
        if ($file->lock()) {
            try {
                $file->flush();
                $file->write($object->toJson());
            } catch (FileSystemException $e) {
                $file->unlock();
                throw new $e;
            }

            $file->unlock();
        }
    }
}
