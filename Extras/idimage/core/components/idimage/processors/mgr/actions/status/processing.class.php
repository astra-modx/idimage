<?php

if (!class_exists('idImageActionsProcessor')) {
    include_once __DIR__.'/../actions.class.php';
}

class idImageStatusProcessing extends idImageActionsProcessor
{

    /**
     * @return array|string
     */
    public function process()
    {
        $newStatus = idImageClose::STATUS_PROCESSING;
        $table = $this->modx->getTableName('idImageClose');

        $sql = "UPDATE {$table} SET status = '{$newStatus}'";
        $total = $this->modx->exec($sql);


        return $this->success('', [
            'total' => $total,
        ]);
    }

}

return 'idImageStatusProcessing';
