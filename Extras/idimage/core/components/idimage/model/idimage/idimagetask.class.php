<?php

class idImageTask extends xPDOSimpleObject
{

    const STATUS_CREATED = 'created';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_PENDING = 'pending';
    // attempts
    const STATUS_UPLOAD = 'upload';
    const STATUS_RETRY = 'retry';

    static $statusMap = [
        self::STATUS_CREATED,
        self::STATUS_COMPLETED,
        self::STATUS_FAILED,
        self::STATUS_PENDING,
        self::STATUS_UPLOAD,
        self::STATUS_RETRY,
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


        /*if ($this->isDirty('status')) {
            if ($this->operation() === 'embedding' && $this->status() === self::STATUS_PENDING) {
                // После отправки
                $this->set('execute_at', $this->addExecuteAt());
            }
        }*/

        $save = parent::save($cacheFlag);

        if ($save) {
            $this->action($this->operation(), $this->status());
        }

        return $save;
    }

    public function addExecuteAt(int $minutes = 1): int
    {
        return strtotime(date('Y-m-d H:i:s', strtotime('+'.$minutes.' minutes', time())));
    }

    public function action(string $operation, string $status): bool
    {
        if ($status !== self::STATUS_COMPLETED) {
            return false;
        }

        $newOperation = null;
        $execute_at = null;
        switch ($operation) {
            case 'upload':
                $newOperation = \IdImage\Sender::ACTION_EMBEDDING;
                $execute_at = $this->addExecuteAt();
                break;
            #case 'embedding':
            #    $newOperation = \IdImage\Sender::ACTION_INDEXED;
            #    break;
            default:
                break;
        }

        if ($newOperation) {
            if ($close = $this->close()) {
                $close->createTask($newOperation, $execute_at);
            }
        }

        return true;
    }

    public function attemptsExceeded($limit = null): bool
    {
        $limit = is_int($limit) ? $limit : $this->service()->attemptLimit();

        return $this->get('attempt') > $limit;
    }


    public function attemptFailureExceeded(): bool
    {
        $limit = $this->service()->attemptFailureLimit();

        return $this->get('attempt_failure') > $limit;
    }


    public function attemptsFailure()
    {
        $attempt = $this->get('attempt_failure') + 1;
        $this->set('attempt_failure', $attempt);
    }


    public function getErrors()
    {
        return $this->get('errors');
    }

    public function isAllowedSend(): bool
    {
        $status = $this->get('status');

        return ($status === self::STATUS_CREATED || $status === self::STATUS_FAILED || $status === self::STATUS_PENDING);
    }

    public function status()
    {
        return $this->get('status');
    }

    public function operation()
    {
        return $this->get('operation');
    }

    public function hasStatus(string $status)
    {
        return $this->status() === $status;
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
        return $this->close()->get('task_id');
    }

    public function setErrors($errors, $status = 'failed')
    {
        $error = null;
        if (!is_null($errors)) {
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
        }
        $this->set('errors', $error);

        return true;
    }

    public function sender(): \IdImage\Sender
    {
        return $this->xpdo->getService('idimage')->sender();
    }


    public function close(): ?idImageClose
    {
        /* @var idImageClose $close */
        if (!$close = $this->getOne('Close')) {
            return null;
        }

        return $close;
    }


    public function service(): idImage
    {
        /* @var idImage $idImage */
        $idImage = $this->xpdo->getService('idimage', 'idImage', MODX_CORE_PATH.'components/idimage/model/');

        return $idImage;
    }

    public function send()
    {
        $sender = $this->service()->sender();

        $collection = new \IdImage\TaskCollection();
        $collection->add($this);

        //$this->attempts();

        return $sender->send($collection);
    }

    public function completed(): bool
    {
        return $this->status() === self::STATUS_COMPLETED;
    }

    public function resetAttempt()
    {
        $this->set('attempt', 0);
    }

    /**
     * Вернет true если время отправления задания не задано или время выполнения задания наступило
     * @return bool
     */
    public function isExecute()
    {
        $execute_at = $this->get('execute_at');
        if (is_null($execute_at)) {
            return true;
        }

        return strtotime($execute_at) < time();
    }

    public function executeTime()
    {
        $execute_at = $this->get('execute_at');
        if (is_null($execute_at)) {
            return true;
        }

        return date('H:i:s', strtotime($execute_at));
    }

}
