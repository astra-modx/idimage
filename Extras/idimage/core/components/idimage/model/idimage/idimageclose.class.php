<?php

use IdImage\Helpers\xPDOQueryIdImage;

/**
 * @package idimage
 */
class idImageClose extends xPDOSimpleObject
{

    const STATUS_QUEUE = 1;
    const STATUS_PROCESSING = 2;
    const STATUS_INVALID = 3;
    const STATUS_FAILED = 4;
    const STATUS_COMPLETED = 5;
    const STATUS_DELETED = 6;
    const STATUS_UNKNOWN = 7;

    const STATUS_SERVICE_MANUAL = 1;
    const STATUS_SERVICE_QUEUE = 2;
    const STATUS_SERVICE_PENDING = 3;
    const STATUS_SERVICE_RUNNING = 4;
    const STATUS_SERVICE_WAITING = 5;
    const STATUS_SERVICE_FAILED = 6;
    const STATUS_SERVICE_COMPLETED = 7;

    static $statusMap = [
        self::STATUS_QUEUE => 'queue',
        self::STATUS_PROCESSING => 'processing',
        self::STATUS_FAILED => 'failed',
        self::STATUS_COMPLETED => 'completed',
        self::STATUS_INVALID => 'invalid',
        self::STATUS_UNKNOWN => 'unknown',
        self::STATUS_DELETED => 'deleted',
    ];

    static $statusServiceMap = [
        self::STATUS_SERVICE_MANUAL => 'manual',
        self::STATUS_SERVICE_QUEUE => 'queue',
        self::STATUS_SERVICE_PENDING => 'pending',
        self::STATUS_SERVICE_RUNNING => 'running',
        self::STATUS_SERVICE_WAITING => 'waiting',
        self::STATUS_SERVICE_FAILED => 'failed',
        self::STATUS_SERVICE_COMPLETED => 'completed',
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

    public function uploadLink()
    {
        // Использование временной ссылки
        return $this->get('upload_link');
    }

    public function link(string $host)
    {
        $picture = $this->get('picture');
        if (empty($picture)) {
            return null;
        }

        return rtrim($host, '/').'/'.ltrim($picture, '/');
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
