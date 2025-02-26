<?php

use IdImage\Entities\EntityIndexed;

class idImageIndexedActionUseProcessor extends modProcessor
{

    /**
     * @return array|string
     */
    public function process()
    {
        $id = $this->getProperty('id');


        /* @var idImageIndexed $Indexed */
        if (!$Indexed = $this->modx->getObject('idImageIndexed', $id)) {
            return $this->failure('Индекс не найден');
        }

        $Indexed->unUseVersion();
        $Indexed->set('use_version', true);
        $Indexed->save();


        return $this->success('', $Indexed->toArray());
    }

}

return 'idImageIndexedActionUseProcessor';
