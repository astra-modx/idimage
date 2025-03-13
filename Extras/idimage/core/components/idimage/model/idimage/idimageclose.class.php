<?php

use IdImage\Entites\TaskEntity;

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
    const STATUS_UNKNOWN = 7;
    const STATUS_NOT_FOUND_SIMILAR = 8;


    static $statusMap = [
        self::STATUS_QUEUE => 'queue',
        self::STATUS_PROCESSING => 'processing',
        self::STATUS_FAILED => 'failed',
        self::STATUS_COMPLETED => 'completed',
        self::STATUS_INVALID => 'invalid',
        self::STATUS_UNKNOWN => 'unknown',
        self::STATUS_NOT_FOUND_SIMILAR => 'similar_not_found',
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

    public function picturePath(bool $absolute = true)
    {
        $path = ltrim($this->get('picture'), '/');
        if (!$absolute) {
            return $path;
        }

        return MODX_BASE_PATH.$path;
    }

    public function embedding(): idImageEmbedding
    {
        /* @var idImageEmbedding $Embedding */
        if (!$Embedding = $this->getOne('Embedding')) {
            $Embedding = $this->xpdo->newObject('idImageEmbedding');
            $Embedding->set('hash', $this->get('hash'));
            $Embedding->set('pid', $this->get('pid'));
        }

        return $Embedding;
    }

    public function task()
    {
        return $this->getOne('Task');
    }

    public function getEmbedding()
    {
        if (!$this->embedding()) {
            return null;
        }

        return $this->embedding()->getEmbedding();
    }


    public function action(\IdImage\Entites\TaskEntity $Task)
    {
        if ($Task->isReceived() && !$this->get('received')) {
            $this->set('received', true);
            $this->set('received_at', time());
        }

        if ($Task->getStatus() === TaskEntity::STATUS_COMPLETED) {
            $embedding = $this->createOrFirst();
            $embedding->set('hash', $this->get('hash'));
            $this->set('embedding', $embedding);
            $this->addOne($Embedding);
        }

        $errors = null;
        if ($Task->getStatus() === \IdImage\Entites\TaskEntity::STATUS_FAILED) {
            $errors = [
                'msg' => $Task->getMsg(),
            ];
        }

        $this->set('errors', $errors);
        $this->save();
    }


}
