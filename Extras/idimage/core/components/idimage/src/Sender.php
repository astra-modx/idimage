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
use idImageSimilar;
use idImageTask;

class Sender extends SenderAbsract
{
    public function upload(TaskCollection $collection): bool
    {
        return $this->handle(
            $collection,
            function (ApiInterfaces $api, TaskCollection $collection) {
                $collection->loadTasks($this->idImage->query()->tasks());

                $collection->each(function (TaskEntity $entity, ?idImageTask $task, $key) use ($collection) {
                    $error = null;
                    $imagePath = $entity->getPicturePath();

                    if (!file_exists($imagePath)) {
                        $Close = $task->close();
                        $response = $Close->generateThumbnail();
                        if ($response !== true) {
                            $error = 'Файл не существует: '.$imagePath;
                        }
                    } else {
                        $size = @getimagesize($imagePath);
                        if ($size[0] !== 224 || $size[1] !== 224) {
                            $error = 'Неверный размер изображения, должно быть 224х224';
                        }

                        if ($size['mime'] !== 'image/jpeg') {
                            $error = 'Неверный формат изображения, должно быть jpeg';
                        }
                    }

                    if ($error) {
                        $collection->forget($key);
                        $task->setErrors($error);
                        $task->save();
                    }
                });

                return $api->upload($collection, true);
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


                $Close = $task->close();

                // Записываем task по которому будем синхронизироваться
                /*   $task = $response['task_id'] ?? null;
                   if (empty($task)) {
                       throw $this->exception('task empty task_id');
                   }
                   if (strlen($task) != 32) {
                       throw $this->exception('task_id должно быть длиной 32 символов');
                   }
                   $Close->set('task_id', $response['task_id']);


                   if (!$Close->save()) {
                       throw $this->exception('Не удалось сохранить task_id: '.$Close->get('id'));
                   }
                */


                // Создаем запись для векторов
                $dataEmbedding = !empty($response['embedding']) ? $response['embedding'] : null;
                if ($dataEmbedding) {
                    // Создаем
                    if (!$embedding = $Close->embedding()) {
                        $embedding = $this->idImage->modx->newObject('idImageEmbedding');
                        $embedding->set('pid', $Close->get('pid')); // Помечаем чтобы от какого товара загрузили
                        $embedding->set('hash', $Close->get('hash'));
                    }

                    $embedding->set('data', $dataEmbedding);
                    if (!$embedding->save()) {
                        throw $this->exception('Не удалось сохранить вектора для изображения taskId: '.$entity->getId());
                    }
                }


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
                    $indexed = $this->idImage->indexer();

                    // Все товары
                    $results = $indexed::comparison(
                        $this->idImage,
                        $indexed->indexerTypeDefault(), //  тип индексации
                        $indexed->similar(),
                        $collection->pids()
                    );

                    return $indexed->response($results);
                }
            },
            function (idImageTask $task, TaskEntity $entity) {
                $response = $entity->getResponse();

                $offer_id = (int)$response['offer_id'] ?? null;
                if (empty($offer_id)) {
                    throw $this->exception('Не удалось получить offer_id');
                }

                if ($offer_id != $entity->getOfferId()) {
                    throw $this->exception('Не совпадает offer_id c entity'.$entity->getOfferId().' return '.$offer_id);
                }


                if ($task->get('pid') !== $offer_id) {
                    throw $this->exception('Не совпадает pid c entity'.$task->get('pid').' return '.$offer_id);
                }

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


                /* @var idImageSimilar $similar */
                if (!$similar = $this->idImage->modx->getObject('idImageSimilar', ['pid' => $offer_id])) {
                    $similar = $this->idImage->modx->newObject('idImageSimilar');
                    $similar->set('pid', $offer_id);
                }


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
