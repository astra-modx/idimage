<?php

if (!class_exists('idImageActionsProcessor')) {
    include_once __DIR__.'/../../actions.class.php';
}

class idImageDestroyProcessor extends idImageActionsProcessor implements \IdImage\Interfaces\ActionProgressBar
{

    public function stepChunk()
    {
        return 100;
    }

    public function withProgressIds()
    {
        return $this->query()->closes()->ids();
    }

    public function process()
    {
        return $this->withProgressBar(function (array $ids) {
            $this->query()
                ->closes()
                ->where(['id:IN' => $ids])
                ->each(function (idImageClose $close) {
                    $close->remove();
                    $this->pt();
                });

            return $this->total();
        });
    }

}

return 'idImageDestroyProcessor';
