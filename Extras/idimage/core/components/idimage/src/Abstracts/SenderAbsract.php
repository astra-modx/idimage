<?php
/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 22.02.2025
 * Time: 12:48
 */

namespace IdImage\Abstracts;


use Exception;
use idImage;
use IdImage\Entites\TaskEntity;
use IdImage\Exceptions\ExceptionJsonModx;
use IdImage\Exceptions\ExceptionSender;
use IdImage\Support\Response;
use IdImage\TaskCollection;
use idImageTask;

abstract class SenderAbsract
{
    public idImage $idImage;

    const ACTION_EMBEDDING = 'embedding';
    const ACTION_UPLOAD = 'upload';
    const ACTION_INDEXED = 'indexed';

    static $actionsMap = [
        self::ACTION_EMBEDDING,
        self::ACTION_UPLOAD,
        self::ACTION_INDEXED,
    ];

    /**
     * @var string[]
     */
    protected array $statuses;

    protected array $limits = [
        self::ACTION_UPLOAD => 20,
        self::ACTION_EMBEDDING => 1000,
        self::ACTION_INDEXED => 100,
    ];

    public function __construct(\idImage $idImage)
    {
        $this->idImage = $idImage;
        $this->statuses = idImageTask::$statusMap;
    }

    public function exception($msg)
    {
        $msg = is_array($msg) ? json_encode($msg) : $msg;

        return new ExceptionSender($msg);
    }


    public function send(TaskCollection $collection): void
    {
        // Группируем

        $map = self::$actionsMap;
        $operations = [];
        foreach ($map as $k) {
            $operations[$k] = [];
        }

        // Создаем список операций
        $collection->each(function (TaskEntity $entity) use (&$operations) {
            $operations[$entity->getOperation()][] = $entity;
        });

        // Полный список удаляем
        unset($collection);


        // обработка операций
        foreach ($operations as $operation => $tasks) {
            if (empty($tasks)) {
                continue; // если нету пропускаем
            }

            // Разбиваем на чанки по операциям
            $chunks = array_chunk($tasks, $this->limit($operation));


            foreach ($chunks as $chunk) {
                if (!in_array($operation, $map)) {
                    throw new ExceptionJsonModx('Неизвестный тип запроса');
                }

                if (!method_exists($this, $operation)) {
                    throw new ExceptionJsonModx('Method not found: '.$operation);
                }

                // отправляем запрос
                $collection = new TaskCollection($chunk);
                $this->$operation($collection);
            }
        }
    }

    /**
     * Основной процесс обработки операции
     * @param  TaskCollection  $collection
     * @param  \Closure  $apiCallback
     * @param  \Closure  $handle
     * @return bool
     * @throws ExceptionJsonModx
     */
    protected function handle(
        TaskCollection $collection,
        \Closure $apiCallback,
        \Closure $handle
    ): bool {
        if ($collection->isEmpty()) {
            return false;
        }
        $api = $this->idImage->api()->task();

        $request = $apiCallback($api, $collection);


        // Отправляем данные в сервис
        /* @var Response $Response */
        $Response = $request instanceof Response ? $request : $request->send();

        // Обработка ответа
        $collection->handerResponse($Response);

        // Загрузка заданий
        $collection->loadTasks($this->idImage->query()->tasks());

        if ($Response->isFail()) {
            // Критическая ошибка автоматчиески вышибает все задания
            $msg = $Response->getMessage();
            $collection->each(function (TaskEntity $entity, ?idImageTask $task) use ($msg) {
                $task->setErrors($msg, null);
                $task->save();
            });
        } else {
            $collection->each(function (TaskEntity $entity, ?idImageTask $task) use ($handle, $collection) {
                if (!$task) {
                    $this->addError('Не найден задание для: '.$entity->getOfferId());

                    return false;
                }
                if (!$entity->exists()) {
                    $task->setErrors('Error get item back');
                } else {
                    $this->handerResponse($entity, $handle, $task);
                }

                return $task->save();
            });
        }

        return true;
    }

    /**
     * Обработка запроса
     * @param  TaskEntity  $entity
     * @param  \Closure  $handle
     * @param  idImageTask|null  $task
     * @return bool
     * @throws ExceptionJsonModx
     */
    private function handerResponse(TaskEntity $entity, \Closure $handle, ?idImageTask $task = null): bool
    {
        // обработка ответа
        // Получаем ответ
        $error = null;
        try {
            $status = $handle($task, $entity);
            if ($status === true) {
                $status = idImageTask::STATUS_COMPLETED;
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
            $status = idImageTask::STATUS_FAILED;
        }

        if (!in_array($status, $this->statuses)) {
            throw new ExceptionJsonModx('Неизвестный статус: '.$status.' taskId: '.$task->get('id'));
        }

        $task->setErrors($error);
        $task->set('status', $status);

        return true;
    }

    public function addError(string $msg)
    {
        $this->errors[] = $msg;

        return $this;
    }


    public function limit(string $operation, int $default = 20)
    {
        return $this->limits[$operation] ?? $default;
    }


}
