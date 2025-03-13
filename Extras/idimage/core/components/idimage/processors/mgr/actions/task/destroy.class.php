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
        return $this->query()->tasks()->ids();
    }

    public function process()
    {
        return $this->withProgressBar(function (array $ids) {
            $this->query()
                ->tasks()
                ->where(['id:IN' => $ids])
                ->each(function (idImageTask $close) {
                    $close->remove();
                    $this->pt();
                });

            return $this->total();
        });
    }

}

return 'idImageDestroyProcessor';
