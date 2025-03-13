<?php

use IdImage\Ai\CollectionProduct;

if (!class_exists('idImageActionsProcessor')) {
    include_once __DIR__.'/../../actions.class.php';
}

class idImageIndexedProductsProcessor extends idImageActionsProcessor implements \IdImage\Interfaces\ActionProgressBar
{

    public function stepChunk()
    {
        return $this->idImage->limitIndexed();
    }

    public function withProgressIds()
    {
        $ids = $this->query()
            ->closes()
            ->leftJoin('idImageTask', 'Task', 'Task.pid=idImageClose.pid')
            ->where([
                'Task.id:IS NOT' => null,
            ])->ids('idImageClose.id as id');

        return $ids;
    }


    public function process()
    {
        $CollectionProduct = new CollectionProduct(
            $this->idImage,
            $this->idImage->minimumProbabilityScore(),
            $this->idImage->maximumProductsFound()
        );

        return $this->withProgressBar(function (array $ids) use ($CollectionProduct) {
            // Загрузка все товаров из базы для работы с ними
            $CollectionProduct->loadEmbedding();

            // Выбираем все товары с изображениями
            $closes = $this->query()->closes()->where(['id:IN' => $ids]);

            $closes->each(function (idImageClose $close) use ($CollectionProduct) {
                $pid = $close->get('pid');;


                // Получаем похожие товары
                $errors = null;
                if (!$embedding = $close->embedding()->getEmbedding()) {
                    $errors = [
                        'error' => 'No embedding',
                    ];
                } else {
                    try {
                        $Similar = $CollectionProduct->getSimilar($pid, $embedding, $CollectionProduct->getEmbedding());
                        $status = $Similar->status();
                        $close->set('similar', $Similar->getSimilar());
                        $close->set('search_scope', $this->idImage->minimumProbabilityScore());
                        $close->set('min_scope', $Similar->minValue());
                        $close->set('max_scope', $Similar->maxValue());
                        $close->set('total', $Similar->total());
                    } catch (Exception $e) {
                        $errors = [
                            'error' => $e->getMessage(),
                        ];
                    }
                }

                if ($errors) {
                    $status = idImageClose::STATUS_INVALID;
                }

                $close->set('errors', $errors);
                $close->set('status', $status);


                $close->save();

                $this->pt();
            });

            return $this->total();
        });
    }

}

return 'idImageIndexedProductsProcessor';
