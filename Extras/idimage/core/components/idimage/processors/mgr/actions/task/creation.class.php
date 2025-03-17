<?php

use IdImage\Exceptions\ExceptionJsonModx;

if (!class_exists('idImageActionsProcessor')) {
    include_once __DIR__.'/../../actions.class.php';
}

class idImageTaskCreationProcessor extends idImageActionsProcessor implements \IdImage\Interfaces\ActionProgressBar
{
    public function stepChunk()
    {
        return 1000;
    }

    public function withProgressIds()
    {
        $ids = $this->query()
            ->closes()
            ->leftJoin('idImageTask', 'Task', 'Task.pid=idImageClose.pid')
            ->where([
                'idImageClose.status:!=' => idImageClose::STATUS_FAILED,
                'Task.id:IS' => null,
            ])->ids('idImageClose.id as id');

        return $ids;
    }


    public function process()
    {
        $this->canToken();

        return $this->withProgressBar(function (array $ids) {
            $closes = $this->query()->closes()->where(['id:IN' => $ids]);
            $closes->each(function (idImageClose $close) {
                // Создание задания для получения векторов
                $task = $close->createTask($this->idimage());

                if (!$task->save()) {
                    throw new ExceptionJsonModx('Не удалось сохранить задачу: '.$close->get('id'));
                }

                $this->pt();

                return true;
            });

            return $this->total();
        });
    }

}

return 'idImageTaskCreationProcessor';
