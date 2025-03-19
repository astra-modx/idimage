<?php
/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 10.03.2025
 * Time: 18:52
 */

namespace IdImage;

use Exception;
use IdImage\Ai\CollectionProduct;
use IdImage\Support\Response;
use idImageClose;

class IndexedProducts
{
    private \idImage $idImage;
    private CollectionProduct $collection;

    public function __construct(\idImage $idImage)
    {
        $this->idImage = $idImage;

        $this->collection = new CollectionProduct(
            $this->idImage,
            $this->idImage->minimumProbabilityScore(),
            $this->idImage->maximumProductsFound()
        );
    }


    public function run(array $pids): Response
    {
        $response = new Response(200, '');
        if (empty($pids)) {
            return $response;
        }
        // Загрузка всех товаров
        $this->collection->loadEmbedding();


        $results = null;
        // Получение только $pids
        $q = $this->idImage->query()->closes();
        $q->innerJoin('idImageEmbedding', 'Embedding', 'Embedding.pid = idImageClose.pid');
        $q->where([
            'idImageClose.pid:IN' => $pids,
        ]);

        $q->each(function (idImageClose $close) use (&$results) {
            $results[] = $this->handle($close);
        });

        $response->setDecoded([
            'items' => $results,
        ]);

        return $response;
    }

    public function handle(idImageClose $close)
    {
        $pid = $close->get('pid');

        // Получаем похожие товары
        $errors = null;
        $similarData = null;
        $status = idImageClose::STATUS_COMPLETED;


        if (!$embedding = $this->collection->get($pid)) {
            $errors = 'No embedding';
        } else {
            try {
                // запускаем поиск по всей коллекции
                $Similar = $this->collection->compare($pid, $embedding);

                // получаем статус поиска
                $similarData = [
                    'total' => $Similar->total(),
                    'search_scope' => $this->idImage->minimumProbabilityScore(),
                    'min_scope' => $Similar->minValue(),
                    'max_scope' => $Similar->maxValue(),
                    'similar' => $Similar->data(),
                    'compared' => $Similar->compared(),
                ];

            } catch (Exception $e) {
                $errors = $e->getMessage();
            }
        }

        if ($errors) {
            $status = idImageClose::STATUS_FAILED;
        }

        if (!empty($errors)) {
            $errors = is_array($errors) ? $errors : [$errors];
        }

        $map = idImageClose::$statusMap;

        $status = $map[$status];
        $data = [
            'task_id' => $close->taskId(),
            'status' => $status,
            'etag' => $close->get('hash'),
            'offer_id' => $pid,
            'errors' => $errors,
            'embedding' => null,
            'similar' => $similarData,
        ];

        return $data;
    }

}
