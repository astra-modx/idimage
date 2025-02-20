<?php

if (!class_exists('idImageActionsProcessor')) {
    include_once __DIR__.'/actions.class.php';
}

class idImageBulkProcessor extends idImageActionsProcessor
{

    /**
     * @return array|string
     */
    public function process()
    {
        $total = $this->idImage->handler()->bulk();

        return $this->success('', [
            'total' => $total,
        ]);
    }

}

return 'idImageBulkProcessor';
