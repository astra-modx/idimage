<?php

use IdImage\Entites\TaskEntity;

/**
 * @package idimage
 */
class idImageClose extends xPDOSimpleObject
{
    const STATUS_INVALID = 1;
    const STATUS_FAILED = 2;
    const STATUS_COMPLETED = 3;

    static $statusMap = [
        self::STATUS_FAILED => 'failed',
        self::STATUS_COMPLETED => 'completed',
        self::STATUS_INVALID => 'invalid',
    ];


    public function save($cacheFlag = null)
    {
        if (!$this->isNew()) {
            $this->set('updatedon', time());
        } else {
            $this->set('createdon', time());
        }

        if ($this->isNew() || $this->isDirty('hash')) {
            // если hash изменился, по ключам pid и hash ничего не найдено
            // то необходимо загрузить изображение в сервис
            // Сравнение hash чтобы понять надо загружать изображение по новой в сервис или нет
            if (!$this->embedding()) {
                $this->createTask = true;
                $this->taskUpload();
            }
        }

        // Создаем задание для создания векторов
        return parent::save($cacheFlag);
    }

    protected $createTask = false;

    public function isCreateTaskUpload()
    {
        return $this->createTask;
    }


    public function createHash(string $imagePath)
    {
        if (!file_exists($imagePath)) {
            return null;
        }

        return sha1_file($imagePath);
#
        #$sizes = @getimagesize($imagePath);
        #$sizes['size'] = filesize($imagePath);
#
        #return md5(json_encode($sizes));
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

    public function embedding(): ?idImageEmbedding
    {
        /* @var idImageEmbedding $Embedding */
        $Embedding = $this->getOne('Embedding');

        return $Embedding;
    }

    public function similar(): ?idImageSimilar
    {
        /* @var idImageSimilar $similar */

        $similar = $this->getOne('Similar');
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


    public function getTargetFilename()
    {
        return $this->get('hash').'.jpg';
    }

    public function getTargetPath()
    {
        // Уникальны ключ, отслеживается ли что то изменилось то картинка не будет найдена
        // запуститься генерация превью
        return MODX_ASSETS_PATH.'images/idimage/'.$this->getTargetFilename();
    }

    public function getTargetUrl()
    {
        return MODX_ASSETS_URL.'images/idimage/'.$this->getTargetFilename();
    }

    public function existsThumbnail()
    {
        return file_exists($this->getTargetPath());
    }

    public function removeThumbnail()
    {
        if ($this->existsThumbnail()) {
            return unlink($this->getTargetPath());
        }

        return false;
    }

    public function generateThumbnail()
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
        $task->set('status', idImageTask::STATUS_QUEUE);

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

    public function remove(array $ancestors = [])
    {
        if ($this->existsThumbnail()) {
            unlink($this->getTargetPath());
        }

        return parent::remove($ancestors);
    }
}
