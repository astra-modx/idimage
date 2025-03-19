<?php

use IdImage\Entites\TaskEntity;

/**
 * @package idimage
 */
class idImageClose extends xPDOSimpleObject
{

    const STATUS_QUEUE = 1;
    const STATUS_INVALID = 3;
    const STATUS_FAILED = 4;
    const STATUS_COMPLETED = 5;
    const STATUS_NOT_FOUND_SIMILAR = 8;

    static $statusMap = [
        self::STATUS_QUEUE => 'queue',
        self::STATUS_FAILED => 'failed',
        self::STATUS_COMPLETED => 'completed',
        self::STATUS_INVALID => 'invalid',
        self::STATUS_NOT_FOUND_SIMILAR => 'similar_not_found',
    ];


    public function save($cacheFlag = null)
    {
        if (!$this->isNew()) {
            $this->set('updatedon', time());
        } else {
            $this->set('createdon', time());
        }

        if (!$this->isNew() && $this->isDirty('hash')) {
            // remove thumb or hash changed
            $this->removeThumbnail();
        }

        if ($this->isDirty('upload') && $this->get('upload') === false) {
            // create task
            $this->taskUpload();
        }

        // Создаем задание для создания векторов
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


    public function picturePath(bool $absolute = true)
    {
        $path = ltrim($this->get('picture'), '/');
        if (!$absolute) {
            return $path;
        }

        return MODX_BASE_PATH.$path;
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

    public function embedding(bool $create = false): ?idImageEmbedding
    {
        /* @var idImageEmbedding $Embedding */
        if (!$Embedding = $this->getOne('Embedding')) {
            if (!$create) {
                return null;
            }
            $Embedding = $this->xpdo->newObject('idImageEmbedding');
            $Embedding->set('hash', $this->get('hash'));
            $Embedding->set('pid', $this->get('pid'));
        }

        return $Embedding;
    }

    public function similar(bool $create = false): ?idImageSimilar
    {
        /* @var idImageSimilar $similar */
        if (!$similar = $this->getOne('Similar')) {
            if (!$create) {
                return null;
            }
            $similar = $this->xpdo->newObject('idImageSimilar');
            $similar->set('pid', $this->get('pid'));
        }

        return $similar;
    }


    public function getProductsSlice()
    {
        if (!$products = $this->getProducts()) {
            $products = [];
        }

        $limit = $this->service()->limitShowSimilarProducts();
        // Ограничиваем массив до 5 элементов
        $products = array_slice($products, 0, $limit);

        // Если в массиве меньше 5 элементов, заполняем недостающие значениями по умолчанию
        $default_product = [
            'pid' => 0,
            'image' => null, // Путь к изображению по умолчанию
            'probability' => 0,
        ];

        while (count($products) < $limit) {
            $products[] = $default_product;
        }

        return $products;
    }

    public function getProducts()
    {
        if (!$similar = $this->similar(false)) {
            return null;
        }

        return $similar->getProducts();
    }


    public function service(): idImage
    {
        /* @var idImage $idImage */
        $idImage = $this->xpdo->getService('idimage', 'idImage', MODX_CORE_PATH.'components/idimage/model/');

        return $idImage;
    }


    public function getPicturePath()
    {
        return MODX_BASE_PATH.$this->picture;
    }

    public function pid()
    {
        return $this->get('pid');
    }


    public function getTargetPath()
    {
        return MODX_ASSETS_PATH.'images/idimage/'.$this->get('pid').'.jpg';
    }

    public function compareHash(string $hash): bool
    {
        return $this->get('hash') === $hash;
    }

    public function existsThumbnail()
    {
        return file_exists($this->getTargetPath());
    }

    public function removeThumbnail()
    {
        if ($this->existsThumbnail()) {
            $this->set('upload', false);

            return unlink($this->getTargetPath());
        }

        return false;
    }

    public function generateThumbnail(bool $reset = true)
    {
        $source = $this->getPicturePath();

        if (!file_exists($source)) {
            $msg = 'File source not found: '.$source;
            $this->setErrors($msg);

            return $msg;
        }

        $idImage = $this->xpdo->getService('idimage', 'idImage', MODX_CORE_PATH.'components/idimage/model/');
        // generate thumbnail
        $target = $idImage->makeThumbnail($this->getPicturePath(), $this->getTargetPath());
        if (!file_exists($target)) {
            $msg = 'Failed to create target thumbnail: '.$target;
            $this->setErrors($msg);

            return $msg;
        }

        if ($reset) {
            $this->set('upload', false); // flag that image is uploaded
        }

        return true;
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


    public function status()
    {
        return $this->get('status');
    }

    public function taskEmbedding()
    {
        return $this->createTask('embedding');
    }

    public function taskIndexed()
    {
        return $this->createTask('indexed');
    }

    public function taskUpload(): ?idImageTask
    {
        return $this->createTask('upload');
    }

    public function createTask(string $operation, $execute_at = null): ?idImageTask
    {
        $pid = $this->get('pid');
        /* @var idImageTask $task */
        $criteria = [
            'pid' => $pid,
            'operation' => $operation,
            'status:!=' => idImageTask::STATUS_COMPLETED,
        ];
        if ($task = $this->xpdo->getObject('idImageTask', $criteria)) {
            return $task;
        }
        /* @var idImageTask $task */
        $task = $this->xpdo->newObject('idImageTask');
        $task->set('pid', $this->get('pid'));
        $task->set('operation', $operation);
        $task->set('status', idImageTask::STATUS_CREATED);

        if (is_int($execute_at)) {
            // Отложенное исполнение
            $task->set('execute_at', $execute_at);
        }
        if (!$task->save()) {
            throw new \RuntimeException('Не удалось создать задание для обработки изображения. pid:'.$pid);
        }

        return $task;
    }

    public function taskId()
    {
        return $this->get('task_id') ?? null;
    }
}
