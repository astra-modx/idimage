<?php

use IdImage\Sender;

class idImageTaskReceivedProcessor extends modProcessor
{
    public function process()
    {
        $id = (int)$this->getProperty('id');

        /* @var idImageTask $Task */
        if (!$Task = $this->modx->getObject('idImageTask', $id)) {
            throw new \IdImage\Exceptions\ExceptionJsonModx('Не удалось получить Task для id');
        }

        $Task->sender()->addTask($Task)->poll();

        return $this->success('Выполнено');
    }
}

return 'idImageTaskReceivedProcessor';
