<?php

use IdImage\Exceptions\ExceptionJsonModx;
use IdImage\Support\ReaderIndexed;

if (!class_exists('idImageActionsProcessor')) {
    include_once __DIR__.'/../../actions.class.php';
}

class idImageApiEmbeddingProcessor extends idImageActionsProcessor implements \IdImage\Interfaces\ActionProgressBar
{
    public function stepChunk()
    {
        return 10;
    }

    public function withProgressIds()
    {
        return $this->query()->closes()->where([
            'received' => false, // Все для кого не получены вектора
        ])->ids();
    }


    public function process()
    {
        $this->canToken();

        return $this->withProgressBar(function (array $ids) {
            // Получаем данные

            $closes = $this->query()->closes()->where(['id:IN' => $ids]);

            $closes->each(function (idImageClose $close) {
                $pid = $close->get('pid');
                $hash = $close->get('hash');

                // Проверяем что получение уже было
                if ($close->get('received')) {
                    return true;
                }

                if (!$Embedding = $close->embedding()) {
                    $Embedding = $this->modx->newObject('idImageEmbedding');
                    $Embedding->set('pid', $pid);
                } else {
                    // Сверяем хэш изображения с хэшем в базе данных
                    if ($Embedding->get('hash') === $hash) {
                        // Если хэш не изменился, значит изображение не было обновлено
                        // Не запрашиваем новые векторы

                        // Устанавливаем флаг что вектора уже получены так как изображение не изменилось
                        $close->set('received', true);
                        $close->set('received_at', time());
                        $close->save();

                        return true;
                    }
                }


                // Считаем количество попыток, после которых обращение в сервис прекращается
                $close->attempts();

                $errors = null;
                if ($close->get('attempts') < 5) {
                    $embedding = null;
                    try {
                        if (!$this->idImage->isSendFile()) {
                            // Отправляем запрос на сервер с помощью url изображения
                            $url = $close->link($this->idImage->siteUrl());
                            $Response = $this->idImage->api()->ai()->embeddingUrl($url)->send();
                            if ($Response->isFail()) {
                                $Response->exception();
                            }
                            $embedding = $Response->json('embedding');
                        } else {
                            // Отправляем запрос на сервер с помощью файла изображения
                            $this->idImage->makeThumbnail($close->picturePath(), function ($pathTmp) use (&$embedding) {
                                // Получаем вектора для изображения
                                $Response = $this->idImage->api()->ai()->embedding($pathTmp)->send();
                                if ($Response->isFail()) {
                                    $Response->exception();
                                }
                                $embedding = $Response->json('embedding');
                            });
                        }
                    } catch (Exception $e) {
                        $errors = [
                            'message' => $e->getMessage(),
                        ];
                    }

                    // Установка нового хеша для сравнения изображений
                    $close->set('received', true);
                    $close->set('received_at', time());

                    $Embedding->set('hash', $hash);
                    $Embedding->set('embedding', $embedding);
                    $close->addOne($Embedding);
                } else {
                    $errors = [
                        'message' => 'Количество попыток превышено',
                    ];
                }

                $close->set('errors', $errors);
                $close->save();

                $this->pt();

                return true;
            });

            return $this->total();
        });
    }

}

return 'idImageApiEmbeddingProcessor';
