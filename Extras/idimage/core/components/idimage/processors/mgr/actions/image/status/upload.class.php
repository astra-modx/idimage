<?php

if (!class_exists('idImageActionsProcessor')) {
    include_once __DIR__.'/../../../actions.class.php';
}

class idImageActionsImageStatusUpload extends idImageActionsProcessor implements \IdImage\Interfaces\ActionProgressBar
{
    public function stepChunk()
    {
        return 100;
    }

    public function withProgressIds()
    {
        return $this->query()->closes()->ids();
    }

    public function process()
    {
        return $this->withProgressBar(function (array $ids) {
            $ids = implode(',', $ids);
            $table = $this->modx->getTableName('idImageClose');
            $sql = "UPDATE {$table} SET upload = '1' WHERE id IN ({$ids})";
            $total = $this->modx->exec($sql);

            return $total;
        });
    }

}

return 'idImageActionsImageStatusUpload';
