<?php

use IdImage\Exceptions\ExceptionJsonModx;
use IdImage\Support\ReaderIndexed;

class idImageVersion extends xPDOSimpleObject
{

    public function save($cacheFlag = null)
    {
        if (!$this->isNew()) {
            $this->set('updatedon', time());
        } else {
            $this->set('createdon', time());
        }

        return parent::save($cacheFlag);
    }

    public function downloadLink()
    {
        return !empty($this->get('download_link')) ? $this->get('download_link') : null;
    }

    public function reader()
    {
        return new ReaderIndexed($this->path(), $this->zip(), $this->downloadLink());
    }


    public function isDownload()
    {
        return ($this->get('download') && $this->exists());
    }


    public function exists()
    {
        return file_exists($this->path());
    }


    public function zip()
    {
        return $this->filePath('.zip');
    }

    public function path()
    {
        return $this->filePath('.json');
    }


    public function filePath(string $ext)
    {
        $ext = ltrim($ext, '.');
        $v = $this->get('version');
        $filename = "closes.v{$v}.{$ext}";
        $idImage = $this->xpdo->getService('idimage', 'idImage', MODX_CORE_PATH.'components/idimage/model/');
        $path = $idImage->config['extract_path'];
        if (empty($path)) {
            throw new ExceptionJsonModx("setting idimage_extract_path is not set");
        }
        if (!is_dir($path)) {
            if (!mkdir($path, 0777, true)) {
                throw new ExceptionJsonModx('Error creating directory: '.$path);
            }
        }

        return rtrim($path, '/').'/'.$filename;

        return null;
    }

}
