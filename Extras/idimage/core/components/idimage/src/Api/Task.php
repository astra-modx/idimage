<?php
/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 24.02.2025
 * Time: 11:50
 */

namespace IdImage\Api;

use CURLFile;
use IdImage\Abstracts\ApiAbstract;
use IdImage\Entites\TaskEntity;
use IdImage\Exceptions\ExceptionJsonModx;
use IdImage\Interfaces\ApiInterfaces;
use IdImage\TaskCollection;

class Task extends ApiAbstract implements ApiInterfaces
{
    # public function createUrl(string $pictureUrl, string $etag, bool $restart = false)
    public function received(TaskCollection $collection)
    {
        $items = [];
        $collection->each(function (TaskEntity $entity) use (&$items) {
            $items[] = [
                'offer_id' => $entity->getOfferId(),
            ];

            return true;
        });

        return $this->client->post('ai/task/create', [
            'items' => $items,
        ]);
    }

    public function upload(TaskCollection $collection, bool $force = false)
    {
        $postFields = [];
        $i = 0;

        $collection->each(function (TaskEntity $entity) use (&$postFields, &$i) {
            $offerId = $entity->getOfferId();
            $imagePath = $entity->getPicturePath();
            $postFields["files[$i]"] = new CURLFile($imagePath, 'image/jpeg', basename($imagePath));
            $postFields["file_ids[$i]"] = $offerId; // Добавляем ID
            $i++;
        });

        if ($force) {
            $postFields['force'] = true; // Быстрое получение векторов
        }

        return $this->client->post('/ai/upload', $postFields)->setHeaders([
            'Accept: application/json',
        ]);
    }

    public function embedding(TaskCollection $collection)
    {
        $ids = [];
        $collection->each(function (TaskEntity $entity) use (&$ids) {
            if (!$taskId = $entity->getTaskId()) {
                throw new ExceptionJsonModx('No offer id');
            }
            $ids[] = $taskId;

            return true;
        });

        return $this->client->post("/ai/task", [
            'ids' => $ids,
            'embedding' => true,
            'similar' => false,
        ]);
    }

    public function similar(TaskCollection $collection)
    {
        $ids = [];
        $collection->each(function (TaskEntity $entity) use (&$ids) {
            if (!$taskId = $entity->getTaskId()) {
                throw new ExceptionJsonModx('No offer task_id: '.$entity->getId());
            }
            $ids[] = $taskId;

            return true;
        });

        return $this->client->post("/ai/task", [
            'ids' => $ids,
            'embedding' => false,
            'similar' => true,
        ]);
    }

    public function completed($ids)
    {
        return $this->client->post("/ai/task/completed", [
            'ids' => $ids,
        ]);
    }

    public function stat()
    {
        return $this->client->post("/ai/task/stat");
    }

}
