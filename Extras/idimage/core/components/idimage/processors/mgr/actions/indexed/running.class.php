<?php

use IdImage\Exceptions\ExceptionJsonModx;

if (!class_exists('idImageActionsProcessor')) {
    include_once __DIR__.'/../../actions.class.php';
}

class idImageActionsIndexedRunningProcessor extends idImageActionsProcessor
{
    /* @var idImageIndexed $Indexed */
    protected $Indexed;

    public function process()
    {
        $Indexed = $this->idimage()->indexed();


        $Response = $Indexed->api()->running()->send();
        if (!$Response->isOk()) {
            return $this->failure($Response->getMessage());
        }
        $Indexed->fromArray($Response->json());
        $Indexed->save();

        return $this->success('', $Indexed->toArray());
    }

}

return 'idImageActionsIndexedRunningProcessor';
