<?php

if (!class_exists('idImageActionsProcessor')) {
    include_once __DIR__.'/../actions.class.php';
}

abstract class idImageStatusProcessor extends idImageActionsProcessor implements \IdImage\Interfaces\ActionProgressBar
{

    public function stepChunk()
    {
        return 100;
    }

    public function withProgressIds()
    {
        return $this->query()->closes()->ids();
    }

    abstract public function status();

    public function process()
    {
        $newStatus = $this->status();
        if (!isset(idImageClose::$statusMap[$newStatus])) {
            return $this->failure('status '.$newStatus.' not found');
        }

        return $this->withProgressBar(function (array $ids) use ($newStatus) {
            $ids = implode(',', $ids);


            $table = $this->modx->getTableName('idImageClose');
            $sql = "UPDATE {$table} SET status = '{$newStatus}' WHERE id IN ({$ids})";
            $total = $this->modx->exec($sql);

            return $total;
        });
    }

}
