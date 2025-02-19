<?php

/**
 * @package idimage
 */
class idImageClose extends xPDOSimpleObject
{

    const STATUS_NEW = 0;
    const STATUS_PROCESS = 1;
    const STATUS_BUILD = 3;
    const STATUS_DONE = 4;
    const STATUS_ERROR = 5;
    const STATUS_DELETED = 6;
    const STATUS_UNKNOWN = 7;


    public function save($cacheFlag = null)
    {
        if (!$this->isNew()) {
            $this->set('updatedon', time());
        } else {
            $this->set('createdon', time());
        }

        return parent::save($cacheFlag);
    }

    public function change(string $imagePath)
    {
        if ($this->isNew()) {
            return true;
        }

        if (empty($this->get('hash'))) {
            return true;
        }

        if (!$hash = $this->createHash($imagePath)) {
            return false;
        }

        return $hash != $this->get('hash');
    }

    public function createHash(string $imagePath)
    {
        if (!file_exists($imagePath)) {
            return null;
        }

        $sizes = @getimagesize($imagePath);
        $sizes['size'] = filesize($imagePath);

        return md5(json_encode($sizes));
    }
}
