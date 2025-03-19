<?php

class idImageTaskReceivedProcessor extends modProcessor
{
    public function process()
    {
        $id = (int)$this->getProperty('id');

        /* @var idImageClose $Close */
        if (!$Close = $this->modx->getObject('idImageClose', $id)) {
            throw new \IdImage\Exceptions\ExceptionJsonModx('Не удалось получить Task для id');
        }

        $Close->generateThumbnail();

        return $this->success('success');
    }
}

return 'idImageTaskReceivedProcessor';
