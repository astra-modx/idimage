<?php

use IdImage\Entities\EntityIndexed;

if (!class_exists('idImageActionsProcessor')) {
    include_once __DIR__.'/../../../actions.class.php';
}

class idImageIndexedUpdateProductsProcessor extends idImageActionsProcessor implements \IdImage\Interfaces\ActionProgressBar
{
    /* @var idImageIndexed $Indexed */
    protected $Indexed;

    public function stepChunk()
    {
        return 100;
    }

    public function reader()
    {
        if (!$this->Indexed = $this->modx->getObject('idImageIndexed', ['use_version' => true])) {
            throw new Exception('Индекс не найден');
        }
        $Entity = $this->Indexed->entity()->fromArray($this->Indexed->toArray());


        $versionJson = $this->Indexed->versionJson();
        if (!$this->Indexed->versionJsonExists()) {
            throw new Exception("Error json {$versionJson}");
        }

        return $Entity->readerIndexed()->read($versionJson);
    }


    public function withProgressIds()
    {
        $offersKeys = $this->reader()->offersKeys();

        return !empty($offersKeys) ? $offersKeys : array_keys($offersKeys);
    }


    public function process()
    {
        return $this->withProgressBar(function (array $ids) {
            $items = $this->reader()->items();
            $closes = $this->query()->closes()->where(['pid:IN' => $ids]);

            $closes->each(function (idImageClose $close) use ($items) {
                $pid = $close->get('pid');

                $total = $min_scope = 0;
                $closes = [];
                $status = idImageClose::STATUS_FAILED;
                if (isset($items[$pid])) {
                    $item = $items[$pid];
                    $status = $item['status'];
                    if (!isset($statusServiceMap[$status])) {
                        $status = idImageClose::STATUS_FAILED;
                    }
                    $min_scope = !empty($item['min_scope']) ? $item['min_scope'] : 0;
                    $closes = (!empty($item['closes']) && is_array($item['closes'])) ? $item['closes'] : [];
                    $total = count($closes);
                }


                $close->set('status', idImageClose::STATUS_COMPLETED);
                $close->set('status_service', $status);
                $close->set('total', $total);
                $close->set('min_scope', $min_scope);
                $close->set('closes', $closes);
                $close->set('version', $this->Indexed->get('version'));


                $close->save();
                $this->pt();
            });

            return $this->total();
        });
    }


    public function process2()
    {
        $id = $this->getProperty('id');

        /* @var idImageIndexed $Indexed */
        if (!$Indexed = $this->modx->getObject('idImageIndexed', $id)) {
            return $this->failure('Индекс не найден');
        }

        $Entity = $Indexed->entity()->fromArray($Indexed->toArray());


        $versionJson = $Indexed->versionJson();
        if (!$Indexed->versionJsonExists()) {
            throw new Exception("Error json {$versionJson}");
        }

        $Reader = $Entity->readerIndexed()->read($versionJson);

        if (!$items = $Reader->closes()) {
            throw new Exception("Error read json closes {$versionJson}");
        }

        $total = $Reader->totalCloses();
        $pids = $Reader->offersKeys();
        $statusServiceMap = $idImage->statusMapService();

        $version = $Entity->version();
        $idImage->query()->closes()
            ->where([
                'pid:IN' => $pids,
            ])
            ->each(function (idImageClose $close) use ($items, $version, $statusServiceMap) {
                $pid = $close->pid;


                $item = $items[$pid];

                $status = $item['status'];
                if (!isset($statusServiceMap[$status])) {
                    $status = idImageClose::STATUS_FAILED;
                }


                $min_scope = !empty($item['min_scope']) ? $item['min_scope'] : 0;
                $closes = (!empty($item['closes']) && is_array($item['closes'])) ? $item['closes'] : [];
                $total = count($closes);

                $close->set('status', idImageClose::STATUS_COMPLETED);
                $close->set('status_service', $status);
                $close->set('total', $total);
                $close->set('min_scope', $min_scope);
                $close->set('closes', $closes);
                $close->set('version', $version);

                $close->save();
            });


        // Ставим метку использования
        $Indexed->set('use_version', true);

        if ($Indexed->save()) {
            $Indexed->unUseVersion();
        }

        return $this->success('', [
            'total' => $total,
        ]);
    }

}

return 'idImageIndexedUpdateProductsProcessor';
