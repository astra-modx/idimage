<?php

use IdImage\Ai\CollectionProduct;
use IdImage\Ai\CosineSimilarity;

/**
 * Демонстрация контроллера
 */
class CrontabControllerTests extends modCrontabController
{


    public function process()
    {
        /* @var idImage $idImage */
        $idImage = $this->modx->getService('idimage', 'idImage', MODX_CORE_PATH.'components/idimage/model/');

        $Sender = new \IdImage\Sender($idImage);


        $data = [
            'ids' => [
                696,
            ],
        ];

        /* @var modProcessorResponse $response */
        $response = $this->modx->runProcessor('actions/api/task/upload', $data, array(
            'processors_path' => MODX_CORE_PATH.'components/idimage/processors/mgr/',
        ));
        dd($response->response);

        if ($response->isError()) {
            return $response->getAllErrors();
        }

        return $response->response;


        /* @var idImageTask $Task */
        $Task = $this->modx->getObject('idImageTask', 576);


        $res = $Sender->poll($Task);
        dd($Task->toArray());


        $url = 'https://platon.site/assets/images/products/26053/b181eb18012611ed995b704d7b6583c9-ddf4ecc24f0011efb177e89c25dff007.jpg';
        $etag = '2';
        $Response = $idImage->api()->task()->create($url, $etag)->send();

        dd($Response->toArray());

        $CollectionProduct = new CollectionProduct($idImage);
        $CollectionProduct
            ->modifyQuery(function (\IdImage\Support\xPDOQueryIdImage $query) {
                return $query;
                //return $query->where(['pid' => 22]);
            })
            ->process();

        dd($CollectionProduct->data());


        /*  $CosineSimilarity = new CosineSimilarity();
          $items = $idImage->query()->embeddings()->toArray();
          $closes = [];
          $minimum = 70; // минимальное значение похожести
          $idImage->query()->embeddings()->each(function (idImageEmbedding $embedding) use ($CosineSimilarity, &$closes, $items, $minimum) {
              $pid = $embedding->get('pid');
              $vectorA = $embedding->vector();
              $status = IdImageClose::STATUS_FAILED;
              if ($vectorA) {
                  $similar = $CosineSimilarity->collection($pid, $vectorA, $items);
                  $status = IdImageClose::STATUS_COMPLETED;
              }
              $data = [
                  'offer_id' => $pid,
                  'total' => count($similar),
                  'min_scope' => $minimum,
                  'similar' => $similar,
                  'status' => $status,
              ];
          });

          $Version = $idImage->indexed()->version();
          $reader = $Version->reader();
          $is = $reader->localStorage([
              'created' => date('Y-m-d H:i:s', time()),
              'total' => [
                  'all' => count($closes),
                  'completed' => count($closes),
                  'error' => 0,
              ],
              'closes' => $closes,
          ]);
          dd($is);

          dd($fileData);*/


        /* @var idImageClose $close */
        $close = $this->modx->getObject('idImageClose', 238);

        $i = 0;
        $idImage->query()->closes()->each(function (idImageClose $close) use ($idImage, &$i) {
            $embedding = $close->embedding();
            if ($embedding && $embedding->get('received')) {
                return true;
            }

            $i++;
            $this->info('['.$i.'] Processing '.$close->get('pid'));

            $ImagePath = $close->picturePath();

            $PhpThumb = new \IdImage\Support\PhpThumb($this->modx);
            $PhpThumb->makeThumbnail($ImagePath, function ($pathTmp) use ($close, $idImage) {
                $hash = $close->get('hash');

                /* @var idImageEmbedding $Embedding */
                $pid = $close->get('pid');
                if (!$Embedding = $this->modx->getObject('idImageEmbedding', ['pid' => $pid])) {
                    $Embedding = $this->modx->newObject('idImageEmbedding');
                    $Embedding->set('pid', $pid);
                } else {
                    // Сверяем хэш изображения с хэшем в базе данных
                    if ($Embedding->get('hash') === $hash) {
                        // Если хэш не изменился, значит изображение не было обновлено
                        // Не запрашиваем новые векторы
                        return false;
                    }
                }

                // Установка хеша для сравнения изображений
                $Embedding->set('hash', $hash);


                // Получаем вектора для изображения
                $Response = $idImage->api()->ai()->embedding($pathTmp)->send();
                if ($Response->isFail()) {
                    throw new Exception($Response->getMessage());
                }

                $embedding = $Response->json('embedding');
                $Embedding->set('received', true);
                $Embedding->set('vector', $embedding);
                $Embedding->save();

                return true;
            });
        });

        $this->info('Finished');

        dd(22);

        # $Embedding = $close->embedding();

        # dd($Embedding->toArray());


        $Embedding = $close->embedding();
        dd([
            'embedding' => $Embedding->toArray(),
        ]);

        dd($Embedding->toArray());

        $Indexed = $idImage->actions()->indexed()->item()->entity();
        dd($Indexed);

        dd($Indexed->active());

        dd($Catalog->toArray());

        /* @var idImageIndexed $Indexed */
        $Indexed = $this->modx->getObject('idImageIndexed', 7);
        $Entity = $Indexed->entity()->fromArray($Indexed->toArray());

        $path = MODX_CORE_PATH.'cache/idimage/versions/';


        try {
            $data = $Entity->downloader()->read($path);

            dd(count($data));
        } catch (Exception $e) {
            dd($e->getMessage());
        }


        dd($data);


        /*  $Entity = $Indexed->entity()
              ->setDownloadLink($Indexed->get('download_link'))
              ->setVersion($Indexed->get('version'))
              ->setRun($Indexed->get('run'))
              ->setLaunch($Indexed->get('launch'))
              ->setCompleted($Indexed->get('completed'))
              ->setUpload($Indexed->get('upload'))
              ->setSize($Indexed->get('size'))
              ->setImages($Indexed->get('images'))
              ->setCloses($Indexed->get('closes'))
              ->setSealed($Indexed->get('sealed'))
              ->setStartAt($Indexed->get('start_at'))
              ->setFinishedAt($Indexed->get('finished_at'))
              ->setUploadAt($Indexed->get('upload_at'));*/


        dd($version);

        /*  $response = $idImage->runProcessor('mgr/actions/creation', [
              'steps' => true,
          ]);
  */

        $response = $idImage->runProcessor('mgr/actions/upload', [
            'ids' => [2257, 2258],
        ]);

        dd($response->response);


        $files = $idImage->query()->files();
        dd($files->toArray());


        dd($ids);

        $total = $idImage->handler()->creation($files, function (idImageClose $close) {
            dd($close->toArray());
        });


        dd($files->count());


        $Query = $idImage->handler()->creation();
        dd(22);

        dd($Query->closes()->count());
    }

}
