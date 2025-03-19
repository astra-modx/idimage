<?php

if (!class_exists('idImageActionsProcessor')) {
    include_once __DIR__.'/../../actions.class.php';
}

abstract class idImageProductTaskProcessor extends idImageActionsProcessor implements \IdImage\Interfaces\ActionProgressBarOperation
{
    public function stepChunk()
    {
        return $this->idImage->limitCreation();
    }

    public function criteria()
    {
        $operation = $this->operation();
        $criteria = [
            $operation => false,
        ];

        return $criteria;
    }

    public function withProgressIds()
    {
        return $this->query()->closes()->where($this->criteria())->ids();
    }

    /**
     * @return array|string
     */
    public function process()
    {
        return $this->withProgressBar(function (array $ids) {
            // Выбираем все товары с изображениями
            $closes = $this->query()->closes()->where(['id:IN' => $ids]);
            $closes->each(function (idImageClose $close) {
                $close->createTask($this->operation());

                return true;
            });

            return $closes->totalIteration();
        });
    }

}

