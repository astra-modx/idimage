<?php

/**
 * @package idimage
 */
class idImageClose extends xPDOSimpleObject
{

    const STATUS_CREATE = 0;
    const STATUS_QUEUE = 1;
    const STATUS_COMPLETED = 2;
    const STATUS_FAILED = 3;
    const STATUS_NO_LAUNCH = 4;
    const STATUS_REVIEW = 5;
    const STATUS_PROCESSING = 6; # review
    const STATUS_UNKNOWN = 7;
    const STATUS_INVALID = 8;
    const STATUS_DELETED = 9;
    const STATUS_UPLOAD = 10;

    static $statusMap = [
        self::STATUS_CREATE => 'create',
        self::STATUS_QUEUE => 'queue',
        self::STATUS_COMPLETED => 'completed',
        self::STATUS_FAILED => 'failed',
        self::STATUS_NO_LAUNCH => 'no_launch',
        self::STATUS_REVIEW => 'review',
        self::STATUS_PROCESSING => 'processing',
        self::STATUS_UNKNOWN => 'unknown',
        self::STATUS_INVALID => 'invalid',
        self::STATUS_DELETED => 'deleted',
        self::STATUS_UPLOAD => 'upload',
    ];


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
        if ($this->isNew() || !$this->get('received')) {
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

    public function url()
    {
        // Использование временной ссылки
        $picture_cloud = $this->get('picture_cloud');
        if (!empty($picture_cloud)) {
            return $picture_cloud;
        }

        $picture = $this->get('picture');
        if (empty($picture)) {
            return null;
        }

        $site_url = rtrim($this->xpdo->getOption('site_url'), '/');

        return $site_url.'/'.ltrim($picture, '/');
    }

    public function offerId()
    {
        return (string)$this->get('pid');
    }

    public function picturePath()
    {
        return MODX_BASE_PATH.ltrim($this->get('picture'), '/');
    }
}
