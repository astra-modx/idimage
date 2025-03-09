<?php

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

    public function picturePath()
    {
        return MODX_BASE_PATH.ltrim($this->get('picture'), '/');
    }

    public function embedding()
    {
        return $this->getOne('Embedding');
    }


    public function getEmbedding()
    {
        if (!$this->embedding()) {
            return null;
        }

        return $this->embedding()->getEmbedding();
    }

    public function attempts()
    {
        $attempt = $this->get('attempt') + 1;
        $c = $this->xpdo->newQuery($this->_class);
        $c->command('UPDATE');
        $c->set([
            'attempt' => $attempt,
        ]);
        $c->where([
            'id' => $this->get('id'),
        ]);
        $c->prepare();
        $c->stmt->execute();
    }

}
