<?php
/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 10.03.2025
 * Time: 18:52
 */

namespace IdImage;

use idImageTask;

class TaskCollection
{
    private array $items = [];

    /* @var idImageTask[] $tasks */
    private array $tasks = [];

    public function __construct()
    {
    }

    public function add(idImageTask $task): self
    {
        $entity = new \IdImage\Entites\TaskEntity();
        $entity->fromArray($task->toArray());
        $this->tasks[$task->get('offer_id')] = $task;
        $this->items[$task->get('offer_id')] = $entity;

        return $this;
    }

    public function toArray(): array
    {
        return $this->items;
    }

    public function each(callable $callback): self
    {
        foreach ($this->items as $item) {
            $callback($item);
        }

        return $this;
    }

    public function getTask(int $id): ?idImageTask
    {
        return $this->tasks[$id] ?? null;
    }

    public function getTasks(): array
    {
        return $this->tasks;
    }

    public function reset()
    {
        $this->tasks = [];
        $this->items = [];

        return $this;
    }

}
