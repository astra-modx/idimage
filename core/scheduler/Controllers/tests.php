<?php

/**
 * Демонстрация контроллера
 */
class CrontabControllerTests extends modCrontabController
{


    public function process()
    {
        /* @var modProcessorResponse $response */
        $response = $this->modx->runProcessor('actions/statuspoll', [], array(
            'processors_path' => MODX_CORE_PATH.'components/idimage/processors/mgr/',
        ));
        if ($response->isError()) {
            return $response->getAllErrors();
        }

        dd($response->response);

        dd($item);

        return $response->response;


        /* @var idImage $idImage */
        $idImage = $this->modx->getService('idimage', 'idImage', MODX_CORE_PATH.'components/idimage/model/');
        $Response = $idImage->handler()->lastVersion();
        $data = $Response->json();
        if (!empty($data['closes_url'])) {
            $content = file_get_contents($data['closes_url']);
            $data = json_decode($content, true);
            dd($data);
        }
        dd($Response->toArray());

        $Response = $idImage->handler()->statusPoll([170]);

        $items = $idImage->handler()->extractorItems($Response);
        dd($items);

        dd($Response->toArray());


        $handler = $idImage->handler();

        $query = $handler->queryStatusProgress();

        dd($query->ids());

        // Скачивание
        //$query = $handler->uploads();


        $action = 'statusPoll';

        $this->info('action: '.$action);
        $total = 0;
        switch ($action) {
            case 'create':
                $total = $Hander->bulk(function ($total, $success) {
                    $this->info('Iteration: '.$total.' success: '.$success);
                });
                break;
            case 'upload':
                $total = $Hander->uploads(function ($total, $success) {
                    $this->info('Iteration: '.$total.' success: '.$success);
                });
                break;
            case 'statusPoll':
                $total = $Hander->statusPoll(function ($total, $success) {
                    $this->info('Iteration: '.$total.' success: '.$success);
                });
                break;
            default:
                break;
        }


        $this->info('Completed total: '.$total);

        dd(22);


        $this->info('run Process');

        $i = 0;
        $total = $Hander->bulk(function () use ($i) {
            $i++;
            $this->info('Iteration: '.$i);
        });

        $this->info('tests');
        $this->info('Total: '.$total);

        dd(22);


        $q = $this->modx->newQuery('msProduct');
        $q->select('msProduct.id as id, Data.image as image');
        $q->innerJoin('msProductData', 'Data', 'Data.id = msProduct.id');
        $q->where([
            'Data.image:!=' => '',
        ]);
        if ($q->prepare() && $q->stmt->execute()) {
            while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                $id = (int)$row['id'];
                $picture = $row['image'];
                $source = MODX_BASE_PATH.ltrim($picture, '/');


                /* @var idImageClose $Close */
                if (!$Close = $this->modx->getObject('idImageClose', ['resource_id' => $id])) {
                    $Close = $this->modx->newObject('idImageClose');
                }

                $hash = $Close->createHash($source);
                $Close->set('resource_id', $id);
                $Close->set('hash', $hash);
                $Close->set('picture', $picture);


                // Проверяем изменения
                if ($Close->change($source)) {
                    // Готовим изображение
                    $idImage->makeThumbnail($source, function ($path) use ($Client, $id, $Close) {
                        // Отправляем изображения в сервис
                        $response = $Client->upload($id, $path)->send();

                        // Записываем ответ
                        $Close->set('status', $response->getStatus());

                        // Ставим метку о доставке
                        $Close->set('received', $response->isOk());

                        // Пишем дату доставки
                        $Close->set('received_at', time());
                    });
                }

                dd($Close->toArray());

                $Close->set('parent', '');


                $source = MODX_BASE_PATH.ltrim($picture, '/');


                $Close->save();
            }
        }


        dd(22);

        /* @var msProduct $object */
        $q = $this->modx->newQuery('msProductData');
        $q->where(array(
            'class_key' => 'msProduct',
        ));
        $q->innerJoin('msProduct', '');
        if ($objectList = $this->modx->getCollection('msProduct', $q)) {
            $i = 0;
            foreach ($objectList as $product) {
                $offer_id = $product->get('id');
                $Data = $object->getOne('Data');

                if (!empty($Data->image)) {
                    $i++;

                    $source = MODX_BASE_PATH.ltrim($Data->image, '/');

                    if ($idImage->change($source)) {
                        $idImage->makeThumbnail($source, function ($path) use ($Client, $offer_id) {
                            $response = $Client->upload($offer_id, $path)->send();

                            dd($response->json());

                            if (!$response->isOk()) {
                                dd($response->json());
                            }
                        });
                    }

                    dd(222);
                    //      $hash = $idImage->hash($source);
                }
                /*

                $Parent = $object->getOne('Parent');
                 $Data = $object->getOne('Data');

                 $pagetitle = $Parent->get('pagetitle');
                 $tags = [
                     $pagetitle,
                 ];
                 $thumb = $siteUrl.''.ltrim($Data->thumb, '/');

                 $Item = $this->modx->newObject('idimageItem');

                 $Item->set('picture', $thumb);
                 $Item->set('resource_id', $object->id);
                 $Item->set('tags', $tags);
                 $Item->save();*/
            }
        }
    }

}
