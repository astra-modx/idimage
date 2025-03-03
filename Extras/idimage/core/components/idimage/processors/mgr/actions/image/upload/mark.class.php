<?php

if (!class_exists('idImageActionsProcessor')) {
    include_once __DIR__.'/../../../actions.class.php';
}

class idImageActionsImageUploadMarkProcessor extends idImageActionsProcessor implements \IdImage\Interfaces\ActionProgressBar
{
    public function stepChunk()
    {
        return 1000;
    }

    public function withProgressIds()
    {
        return $this->query()->closes()->where([
            'upload' => false,
        ])->ids();
    }

    /**
     * @return array|string
     */
    public function process()
    {
        return $this->withProgressBar(function (array $ids) {
            $table = $this->modx->getTableName('idImageClose');
            $ids = implode(',', $ids);
            $this->modx->exec("UPDATE {$table} SET upload = '1'  WHERE id IN ({$ids})");
            sleep(1);

            return $this->total();
        });
    }

}

return 'idImageActionsImageUploadMarkProcessor';
