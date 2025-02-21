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
        if (!$items = $this->idImage->handler()->lastVersion()) {
            return $this->failure('No items');
        }
        $items = $this->idImage->handler()->extractorItems($items);
        $total = 0;

        // set status
        $this->idImage->handler()->query(null)
            ->where([
                'status:!=' => idImageClose::STATUS_COMPLETED,
            ])
            ->each(function (idImageClose $close) use (&$total, $items) {
                $pid = $close->pid;
                if (isset($items[$pid])) {
                    $this->idImage->operation()->statusPoll($close, $items[$pid]);
                    $total++;
                }
            });


        return $this->success('', [
            'total' => $total,
        ]);
    }

}

return 'idImageStatusPollProcessor';
