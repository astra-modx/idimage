<?php

class idImageIndexed extends xPDOSimpleObject
{
    public function entity()
    {
        return new \IdImage\Entities\EntityIndexed();
    }

    public function deactivate()
    {
        if (!$this->isNew()) {
            $this->xpdo->exec("UPDATE {$this->_table} SET active = '0'  WHERE id != ".$this->get('id'));
        }
    }

    public function unUseVersion()
    {
        if (!$this->isNew()) {
            $this->xpdo->exec("UPDATE {$this->_table} SET use_version = '0'  WHERE id != ".$this->get('id'));
        }
    }

    public function filename()
    {
        if (!$url = $this->get('download_link')) {
            return null;
        }

        return pathinfo($url, PATHINFO_FILENAME);
    }

    public function filePath()
    {
        if ($filename = $this->filename()) {
            $idImage = $this->xpdo->getService('idimage', 'idImage', MODX_CORE_PATH.'components/idimage/model/');
            $path = $idImage->config['extract_path'];
            if (empty($path)) {
                throw new Exception("setting idimage_extract_path is not set");
            }
            if (!is_dir($path)) {
                if (!mkdir($path, 0777, true)) {
                    throw new Exception('Error creating directory: '.$path);
                }
            }

            return rtrim($path, '/').'/'.$filename;
        }

        return null;
    }

    public function versionZip()
    {
        if ($path = $this->filePath()) {
            return $path.'.zip';
        }

        return null;
    }

    public function versionJson()
    {
        if ($path = $this->filePath()) {
            return $path.'.json';
        }

        return null;
    }

    public function versionJsonExists()
    {
        return ($file = $this->versionJson()) ? file_exists($file) : false;
    }
}
