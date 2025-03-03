<?php

use IdImage\Exceptions\ExceptionJsonModx;

if (!class_exists('idImageActionsProcessor')) {
    include_once __DIR__.'/../../actions.class.php';
}

class idImageIndexedUpdateProductsProcessor extends idImageActionsProcessor implements \IdImage\Interfaces\ActionProgressBar
{


    public function stepChunk()
    {
        return 100;
    }


    public function withProgressIds()
    {
        $Version = $this->indexed()->version();

        $reader = $Version->reader();


        // Если еще не загружен файл с данными версии, то загрузить его
        if (!$Version->isDownload()) {
            $reader->download();
            // Ставим флаг загрузки файла с данными версии
            $Version->set('download', true);
            $Version->save();
        }


        if (!$Version->exists()) {
            throw new ExceptionJsonModx("Error json {$Version->path()}");
        }

        $offersKeys = $reader->read()->offersKeys();

        return !empty($offersKeys) ? $offersKeys : array_keys($offersKeys);
    }


    public function process()
    {
        return $this->withProgressBar(function (array $ids) {
            // Получаем данные
            $items = $this->indexed()->version()->reader()->read()->items();

            $closes = $this->query()->closes()->where(['pid:IN' => $ids]);

            $closes->each(function (idImageClose $close) use ($items) {
                $pid = $close->get('pid');

                $total = $min_scope = 0;
                $similar = [];
                $status = idImageClose::STATUS_FAILED;
                if (isset($items[$pid])) {
                    $item = $items[$pid];
                    $status = $item['status'];
                    if (!isset($statusServiceMap[$status])) {
                        $status = idImageClose::STATUS_FAILED;
                    }
                    $min_scope = !empty($item['min_scope']) ? $item['min_scope'] : 0;
                    $similar = (!empty($item['similar']) && is_array($item['similar'])) ? $item['similar'] : [];
                    $total = count($similar);
                }


                $close->set('status', idImageClose::STATUS_COMPLETED);
                $close->set('status_service', $status);
                $close->set('total', $total);
                $close->set('min_scope', $min_scope);
                $close->set('similar', $similar);
                $close->set('version', $this->Indexed->get('version'));


                $close->save();
                $this->pt();
            });

            return $this->total();
        });
    }

}

return 'idImageIndexedUpdateProductsProcessor';
