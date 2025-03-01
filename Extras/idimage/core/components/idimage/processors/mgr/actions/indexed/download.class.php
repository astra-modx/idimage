<?php

use IdImage\Exceptions\ExceptionJsonModx;
use IdImage\Support\ReaderIndexed;

if (!class_exists('idImageActionsProcessor')) {
    include_once __DIR__.'/../../actions.class.php';
}

class idImageIndexedUpdateProductsProcessor extends idImageActionsProcessor
{
    /* @var idImageIndexed $Indexed */
    protected $Indexed;

    public function process()
    {
        $Indexed = $this->idimage()->indexed();

        $Version = $Indexed->version();

        if (!$target = $Indexed->versionZip()) {
            return $this->failure('Индекс не доступен для скачивания');
        }
        $Reader = new ReaderIndexed();

        $Reader->download($Version->downloadLink(), $Indexed->versionZip());
        if (!file_exists($target)) {
            throw new ExceptionJsonModx("Error download {$target}");
        }

        if (!$Indexed->versionJsonExists()) {
            throw new ExceptionJsonModx("Error json {$Indexed->versionJson()}");
        }

        return $this->success($Indexed->toArray());
    }

}

return 'idImageIndexedUpdateProductsProcessor';
