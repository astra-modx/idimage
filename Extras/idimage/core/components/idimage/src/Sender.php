<?php
/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 10.03.2025
 * Time: 18:52
 */

namespace IdImage;

use IdImage\Abstracts\SenderAbsract;
use IdImage\Entites\TaskEntity;
use IdImage\Interfaces\ApiInterfaces;
use idImageTask;

class Sender extends SenderAbsract
{
    public function embedding(TaskCollection $collection): bool
    {
        return $this->handle(
            $collection,
            function (ApiInterfaces $api, TaskCollection $collection) {
                return $api->embedding($collection);
            },
            function (idImageTask $task, TaskEntity $entity) {
                $response = $entity->getResponse();

                if ($response['status'] === 'pending') {
                    return idImageTask::STATUS_PENDING;
                }

                $close = $task->close();

                // Создаем запись для векторов
                $dataEmbedding = !empty($response['embedding']) ? $response['embedding'] : null;

                if ($dataEmbedding) {
                    $embedding = $close->embedding(true);
                    $embedding->set('data', $dataEmbedding);
                    if (!$embedding->save()) {
                        throw $this->exception('Не удалось сохранить вектора для изображения taskId: '.$entity->getId());
                    }
                }

                return idImageTask::STATUS_COMPLETED;
            }
        );
    }

    public function upload(TaskCollection $collection): bool
    {
        return $this->handle(
            $collection,
            function (ApiInterfaces $api, TaskCollection $collection) {
                return $api->upload($collection);
            },
            function (idImageTask $task, TaskEntity $entity) {
                $response = $entity->getResponse();
                // Получаем только ссылку на изображение
                if ($response['status'] === 'failed') {
                    $errors = '';
                    if (!empty($response['errors'])) {
                        if (is_array($response['errors'])) {
                            $errors = json_encode($errors);
                        }
                    }
                    throw $this->exception('error upload: '.$errors);
                }

                if (empty($response['task_id'])) {
                    throw $this->exception('task empty task_id');
                }

                // Записываем task по которому будем синхронизироваться


                $Close = $task->close();
                $Close->set('upload', true);
                $Close->set('task_id', $response['task_id']);
                $Close->save();

                // Если удалось загрузить ставим статус на CREATED для добавления в очередь на обработку
                return idImageTask::STATUS_COMPLETED;
            }
        );
    }

    public function indexed(TaskCollection $collection): bool
    {
        return $this->handle(
            $collection,
            function (ApiInterfaces $api, TaskCollection $collection) {
                // Получение векторов из сервиса
                if ($this->idImage->isIndexedService()) {
                    return $api->similar($collection);
                } else {
                    $IndexedProducts = new IndexedProducts($this->idImage);

                    return $IndexedProducts->run($collection->pids());
                }
            },
            function (idImageTask $task, TaskEntity $entity) {
                $response = $entity->getResponse();
                if ($response['status'] === 'pending') {
                    return idImageTask::STATUS_PENDING;
                }

                if ($response['status'] === 'failed') {
                    throw $this->exception($response['errors']);
                }

                // Проверка что результат похожих товаров есть
                $dataSimilar = (!empty($response['similar']) && is_array($response['similar'])) ? $response['similar'] : null;
                if (!$dataSimilar) {
                    return idImageTask::STATUS_PENDING;
                }

                // Получаем результаты индексации похожих товаров
                $close = $task->close();
                $similar = $close->similar(true);
                $min_scope = (int)$dataSimilar['min_scope'] ?? 0;
                $total = (int)$dataSimilar['total'] ?? 0;
                $compared = (int)$dataSimilar['compared'] ?? 0;

                $data = (is_array($dataSimilar['similar']) && !empty($dataSimilar['similar'])) ? $dataSimilar['similar'] : null;

                $similar->set('total', $total);
                $similar->set('data', $data);
                $similar->set('min_scope', $min_scope);
                $similar->set('compared', $compared);

                if (!$similar->save()) {
                    throw $this->exception('Не удалось сохранить похожие для изображения taskId: '.$entity->getId());
                }

                return idImageTask::STATUS_COMPLETED;
            }
        );
    }


}
