<?php
/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 10.03.2025
 * Time: 18:52
 */

namespace IdImage;

use IdImage\Entites\TaskEntity;
use IdImage\Support\Response;
use idImageTask;

class TaskCollection
{
    private array $items = [];

    /* @var idImageTask[] $tasks */
    private array $tasks = [];

    public function __construct($items = null)
    {
        if (is_array($items)) {
            $this->items = $items;
        }
    }

    public function entity(idImageTask $task): \IdImage\Entites\TaskEntity
    {
        $entity = new \IdImage\Entites\TaskEntity();
        $entity->fromArray($task->toArray());

        $Close = $task->close();
        $entity->setPicturePath($Close->getTargetPath());

        $entity->setId($task->get('id'));

        if ($taskId = $Close->taskId()) {
            $entity->setTaskId($taskId);
        }

        $pid = (string)$Close->get('pid');
        $entity->setOfferId($pid);

        $entity->setOperation($task->operation());

        return $entity;
    }

    public function add($entity): TaskEntity
    {
        if ($entity instanceof idImageTask) {
            $entity = $this->entity($entity);
        }

        if (!$entity instanceof TaskEntity) {
            throw new \Exception('Не верный тип данных');
        }

        $this->items[$entity->getOfferId()] = $entity;

        return $entity;
    }

    public function toArray(): array
    {
        return $this->items;
    }

    public function each(callable $callback): self
    {
        /* @var TaskEntity $item */
        foreach ($this->items as $item) {
            $task = $this->task($item->getOfferId());
            $callback($item, $task);
        }

        return $this;
    }

    public function task($id): ?idImageTask
    {
        return $this->tasks[$id] ?? null;
    }


    public function reset()
    {
        $this->tasks = [];
        $this->items = [];

        return $this;
    }

    public function count()
    {
        return count($this->items);
    }

    public function first()
    {
        return current($this->items);
    }

    public function last()
    {
        return end($this->items);
    }

    public function isNotEmpty()
    {
        return $this->count() > 0;
    }

    public function isEmpty()
    {
        return !$this->isNotEmpty();
    }

    public function handerResponse(Response $Response)
    {
        $items = $Response->json('items');
        if (!empty($items)) {
            $items = array_combine(array_column($items, 'offer_id'), $items);
            $this->each(function (TaskEntity $entity) use ($items) {
                $item = $items[$entity->getOfferId()] ?? null;
                if ($item) {
                    $entity->setResponse($item);
                }
            });
        }
    }

    public function loadTasks(\IdImage\Support\xPDOQueryIdImage $query): array
    {
        if (!empty($this->items)) {
            // Получаем задания по ID
            $ids = [];
            foreach ($this->items as $entity) {
                $ids[] = $entity->getId();
            }

            if (!empty($ids)) {
                $query->where(['id:IN' => $ids]);
                $query->each(function (idImageTask $task) {
                    $this->tasks[$task->get('pid')] = $task;
                });
            }
        }

        return $this->tasks;
    }

    public function pids()
    {
        $pids = [];
        foreach ($this->items as $entity) {
            $pids[] = $entity->getOfferId();
        }

        return $pids;
    }

}
