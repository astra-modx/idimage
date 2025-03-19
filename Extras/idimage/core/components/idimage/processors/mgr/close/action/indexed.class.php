<?php

class idImageTaskIndexedProcessor extends modProcessor
{
    public function process()
    {
        $id = (int)$this->getProperty('id');

        /* @var idImageClose $Close */
        if (!$Close = $this->modx->getObject('idImageClose', $id)) {
            throw new \IdImage\Exceptions\ExceptionJsonModx('Не удалось получить Task для id');
        }

        $task = $Close->taskIndexed();
        $result = $task->send();

        return $result ? $this->success('success') : $this->failure('error');
    }
}

return 'idImageTaskIndexedProcessor';
