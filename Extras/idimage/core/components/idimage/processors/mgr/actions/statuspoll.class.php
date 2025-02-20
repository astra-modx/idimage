<?php

if (!class_exists('idImageActionsProcessor')) {
    include_once __DIR__.'/actions.class.php';
}

class idImageStatusPollProcessor extends idImageActionsProcessor
{
    /**
     * @return array|string
     */
    public function process()
    {
        $chunk = 100;
        if ($this->setCheckbox('count_iteration')) {
            $ids = $this->idImage->handler()->query()->where([
                'status:!=' => idImageClose::STATUS_DONE,
            ])->ids();

            return $this->success('', [
                'iterations' => array_chunk($ids, $chunk),
                'total' => count($ids),
            ]);
        }

        $ids = $this->getProperty('ids');
        if (empty($ids)) {
            return $this->success('upload', ['total' => 0]);
        }
        $ids = json_decode($ids, true);

        if (!is_array($ids)) {
            return $this->success('upload', [
                'total' => 0,
            ]);
        }
        $ids = array_filter(array_map('intval', $ids));


        $pids = $this->idImage->handler()->query(null)->where([
            'id:IN' => $ids,
            'status:!=' => idImageClose::STATUS_DONE,
        ])->ids('pid');

        // Get offers status
        $Response = $this->idImage->handler()->statusPoll($pids);

        // extract items
        $items = $this->idImage->handler()->extractorItems($Response);
        $total = 0;

        // set status
        $this->idImage->handler()->query(null)
            ->where([
                'id:IN' => $ids,
                'status:!=' => idImageClose::STATUS_DONE,
            ])
            ->each(function (idImageClose $close) use (&$total, $items) {
                if (isset($items[$close->pid])) {
                    $this->idImage->operation()->statusPoll($close, $items[$close->pid]);
                    $total++;
                }
            });

        return $this->success('', [
            'total' => $total,
        ]);
    }

}

return 'idImageStatusPollProcessor';
