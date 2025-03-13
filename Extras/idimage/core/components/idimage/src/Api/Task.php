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
                'etag' => $entity->getEtag(),
                'picture' => $entity->getPicture(),
            ];

            return true;
        });

        return $this->client->post('ai/task/create', [
            'items' => $items,
        ]);
    }

    public function upload(TaskCollection $collection)
    {
        $postFields = [];
        $i = 0;

        $collection->each(function (TaskEntity $entity) use (&$postFields, &$i) {
            $offerId = $entity->getOfferId();
            $imagePath = $entity->getTmpPath();

            $size = @getimagesize($imagePath);
            if ($size[0] !== 224 || $size[1] !== 224) {
                throw new ExceptionJsonModx('Неверный размер изображения, должно быть 224х224');
            }

            if ($size['mime'] !== 'image/jpeg') {
                throw new ExceptionJsonModx('Неверный формат изображения, должно быть jpeg');
            }

            $postFields["files[$i]"] = new CURLFile($imagePath, 'image/jpeg', basename($imagePath));
            $postFields["file_ids[$i]"] = $offerId; // Добавляем ID
            $i++;
        });

        return $this->client->post('/ai/upload', $postFields)->setHeaders([
            'Accept: application/json',
        ]);
    }

    public function poll(TaskCollection $collection)
    {
        $ids = [];
        $collection->each(function (TaskEntity $entity) use (&$ids) {
            $taskId = $entity->getTaskId();
            if (empty($taskId)) {
                throw new ExceptionJsonModx('No task id');
            }

            $ids[] = $taskId;

            return true;
        });

        return $this->client->post("/ai/task", [
            'ids' => $ids,
        ]);
    }

}
