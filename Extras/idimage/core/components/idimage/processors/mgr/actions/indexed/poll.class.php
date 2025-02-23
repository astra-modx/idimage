<?php

use IdImage\Entities\EntityClose;

if (!class_exists('idImageActionsProcessor')) {
    include_once __DIR__.'/../actions.class.php';
}

class idImageIndexedPollProcessor extends idImageActionsProcessor
{
    /**
     * @return array|string
     */
    public function process()
    {
        /* $this->idImage->query()->indexeds()->collection(function (idImageIndexed $indexed){

         });*/

        $Response = $this->idImage->operation()->indexed();

        $status_code = $Response->getStatus();
        $Response->items(function ($item) use ($status_code) {
            unset($item['id']);

            $version = $item['version'];
            $item['status_code'] = $status_code;
            /* @var idImageIndexed $object */
            if (!$Indexed = $this->modx->getObject('idImageIndexed', ['version' => $version])) {
                $Indexed = $this->modx->newObject('idImageIndexed');
                $Indexed->set('version', $version);
            }

            $Indexed->fromArray($item);
            $Indexed->save();
            $this->pt();
        });

        return $this->success([
            'total' => $this->total(),
        ]);
    }

}

return 'idImageIndexedPollProcessor';
