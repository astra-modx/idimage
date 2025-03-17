<?php

class idImageTask extends xPDOSimpleObject
{

    const STATUS_CREATED = 'created';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_PENDING = 'pending';
    // attempts
    const STATUS_UPLOAD = 'upload';
    const STATUS_FAIL = 'fail';

    static $statusMap = [
        self::STATUS_CREATED,
        self::STATUS_COMPLETED,
        self::STATUS_FAIL,
        self::STATUS_FAILED,
        self::STATUS_PENDING,
        self::STATUS_UPLOAD,
    ];

    public function save($cacheFlag = null)
    {
        if (!$this->isNew()) {
            $this->set('updatedon', time());
        } else {
            if (empty($this->get('status'))) {
                $this->set('status', self::STATUS_CREATED);
            }
            $this->set('createdon', time());
        }

        return parent::save($cacheFlag);
    }

    public function attemptsExceeded(int $limit = 5): bool
    {
        return $this->get('attempt') > $limit;
    }

    public function getErrors()
    {
        return $this->get('errors');
    }

    public function isAllowedSend(): bool
    {
        $status = $this->get('status');

        return $status === self::STATUS_CREATED;
    }

    public function status()
    {
        return $this->get('status');
    }

    public function hasStatus(string $status)
    {
        return $this->status() === $status;
    }

    public function isAllowedPoll(): bool
    {
        if (empty($this->get('task_id'))) {
            return false;
        }

        return (!$this->hasStatus(idImageTask::STATUS_UPLOAD) && !$this->hasStatus(idImageTask::STATUS_CREATED));
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

    public function taskId()
    {
        return $this->get('task_id');
    }

    public function setErrors($errors, $status = 'failed')
    {
        $error = null;
        if ($errors instanceof \IdImage\Support\Response) {
            $msg = $errors->getMessage();
            $error = [
                'msg' => $msg,
                'status' => $errors->getStatus(),
            ];
        } elseif ($errors instanceof \Throwable) {
            $error = [
                'msg' => '[Throwable] '.$errors->getMessage(),
            ];
        } elseif ($errors instanceof \Exception) {
            $error = [
                'msg' => '[Exception] '.$errors->getMessage(),
            ];
        } elseif (is_array($errors) || is_null($errors)) {
            $error = $errors;
        } elseif (is_string($errors)) {
            $error = [
                'msg' => $errors,
            ];
        }

        if (is_string($status)) {
            $this->set('status', $status);
        }
        $this->set('errors', $error);
    }

    public function sender(): \IdImage\Sender
    {
        return $this->xpdo->getService('idimage')->sender();
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

    public function close(): ?idImageClose
    {
        /* @var idImageClose $close */
        if (!$close = $this->getOne('Close')) {
            return null;
        }

        return $close;
    }
}
