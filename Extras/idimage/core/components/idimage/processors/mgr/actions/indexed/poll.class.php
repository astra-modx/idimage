<?php

use IdImage\Entities\EntityClose;

if (!class_exists('idImageActionsProcessor')) {
    include_once __DIR__.'/../../actions.class.php';
}

class idImageIndexedPollProcessor extends idImageActionsProcessor
{
    /**
     * @return array|string
     */
    public function process()
    {
        $Response = $this->idImage->operation()->indexed();
        $Response->items(function ($item) {
            $Entity = new \IdImage\Entities\EntityIndexed();

            $Entity->fromArray($item);

            /* @var idImageIndexed $object */
            if (!$Indexed = $this->modx->getObject('idImageIndexed', ['version' => $Entity->version()])) {
                $Indexed = $this->modx->newObject('idImageIndexed');
                $Indexed->set('version', $Entity->version());
            }

            $Indexed->fromArray($Entity->toArray());
            $Indexed->save();
            $this->pt();
        });

        return $this->success([
            'total' => $this->total(),
        ]);
    }

}

return 'idImageIndexedPollProcessor';
