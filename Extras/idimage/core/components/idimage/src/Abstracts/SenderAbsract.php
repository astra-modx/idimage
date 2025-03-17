<?php
/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 22.02.2025
 * Time: 12:48
 */

namespace IdImage\Abstracts;


use idImage;
use IdImage\Entites\TaskEntity;
use IdImage\Exceptions\ExceptionJsonModx;
use IdImage\TaskCollection;
use idImageTask;

abstract class SenderAbsract
{
    public idImage $idImage;

    const ACTION_POLL = 'poll';
    const ACTION_RECEIVED = 'received';
    const ACTION_UPLOAD = 'upload';
    protected TaskCollection $collection;

    public function __construct(\idImage $idImage)
    {
        $this->idImage = $idImage;
        $this->create();
    }

    public function create()
    {
        $this->collection = new TaskCollection();

        return $this;
    }

    public function addTask(idImageTask $task)
    {
        $this->collection->add($task);

        return $this;
    }

    protected function handle(string $action, \Closure $handle, \Closure $collection = null): bool
    {
        $api = $this->idImage->api()->task();

        $collection = $collection ?? $this->collection;
        switch (true) {
            case $action === self::ACTION_UPLOAD;
                $request = $api->upload($collection);
                break;
            case $action === self::ACTION_RECEIVED;
                $request = $api->received($collection);
                break;
            case $action === self::ACTION_POLL;
                $request = $api->poll($collection);
                break;
            default:
                throw new ExceptionJsonModx('Неизвестный тип запроса: '.$action);
        }

        // Отправляем данные в сервис
        $Response = $request->send();

        if ($action === self::ACTION_UPLOAD) {
            $this->unlinkTmpFiles();
        }

        if ($Response->isFail()) {
            // Критическая ошибка автоматчиески вышибает все задания
            $msg = $Response->getMessage();

            $this->collection->each(function (TaskEntity $entity) use ($msg) {
                $task = $this->getTask($entity->getOfferId());
                $task->setErrors($msg, null);
                $task->save();
            });
        } else {
            $items = $Response->json('items');
            // назначить ключем offer_id
            $items = array_combine(array_column($items, 'offer_id'), $items);
            // перебор коллекции задач

            $statuses = idImageTask::$statusMap;


            $this->collection->each(function (TaskEntity $entity) use ($handle, $items, $statuses) {
                // Получаем ответ
                $item = $items[$entity->getOfferId()] ?? null;

                if (!empty($item)) {
                    // Заполняем ответ в задачу
                    $entity->fromArray($item);

                    $task = $this->getTask($entity->getOfferId());

                    $status = $handle($task, $entity);

                    if (!in_array($status, $statuses)) {
                        throw new ExceptionJsonModx('Неизвестный статус: '.$status.' taskId: '.$task->get('id'));
                    }

                    $task->set('status', $status);
                    $task->save();
                } else {
                    throw new ExceptionJsonModx('Не удалось найти задачу с ID: '.$entity->getOfferId());
                }
            });
        }


        $this->collection->reset();

        return true;
    }

    public function unlinkTmpFiles()
    {
        $this->collection->each(function (TaskEntity $entity) {
            if ($path = $entity->getTmpPath()) {
                if (file_exists($path)) {
                    unlink($path);
                }
            }
        });
    }

    public function getTask(int $id)
    {
        return $this->collection()->getTask($id);
    }

    public function collection()
    {
        return $this->collection;
    }

}
