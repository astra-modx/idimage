<?php

if (!class_exists('idImageActionsProcessor')) {
    include_once __DIR__.'/actions.class.php';
}

class idImagePollProcessor extends idImageActionsProcessor implements \IdImage\Interfaces\ActionProgressBar
{
    public function stepChunk()
    {
        return 100;
    }

    public function withProgressIds()
    {
        return $this->query()->closes()->where([
            'status' => idImageClose::STATUS_PROCESSING,
        ]);
    }

    /**
     * @return array|string
     */
    public function process()
    {
        if (!$Response = $this->idimage()->operation()->lastVersion()) {
            return $this->failure('No items');
        }

        $version = $Response->json('version');
        $closes_url = $Response->json('closes_url');


        /* @var idImageVersion $object */
        if (!$Version = $this->modx->getObject('idImageVersion', ['version' => $version])) {
            $Version = $this->modx->newObject('idImageVersion');
            $Version->set('version', $version);
        }
        $Version->set('status_code', $Response->getStatus());
        $Version->set('closes_url', $closes_url);

        /*$items = $this->idimage()->extractor()->extractCloses($url);

        $pids = array_keys($items);
        $this->idImage->query()->closes()
            ->where([
                'pid:IN' => $pids,
            ])
            ->each(function (idImageClose $close) use ($items, $version) {
                $pid = $close->pid;


                $item = $items[$pid];


                $status = !empty($item['status']) ? $item['status'] : idImageClose::STATUS_FAILED;
                $total_close = !empty($item['total_close']) ? $item['total_close'] : 0;
                $min_scope = !empty($item['min_scope']) ? $item['min_scope'] : 0;
                $closes = (!empty($item['closes']) && is_array($item['closes'])) ? $item['closes'] : [];


                $close->set('status', $status);
                $close->set('total_close', $total_close);
                $close->set('min_scope', $min_scope);
                $close->set('closes', $closes);
                $close->set('version', $version);


                $this->pt();
            });*/

        $Version->save();
        return $this->success('', [
            'version' => $Version->toArray(),
        ]);
    }

}

return 'idImagePollProcessor';
