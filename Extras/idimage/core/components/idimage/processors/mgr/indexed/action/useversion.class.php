<?php

use IdImage\Entities\EntityIndexed;

class idImageIndexedActionUseVersionProcessor extends modProcessor
{

    /**
     * @return array|string
     */
    public function process()
    {
        /* @var idImage $idImage */
        $idImage = $this->modx->getService('idimage', 'idImage', MODX_CORE_PATH.'components/idimage/model/');


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

return 'idImageIndexedActionUseVersionProcessor';
