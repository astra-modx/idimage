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
        $Response = $this->idImage->handler()->statusPoll();
        $items = $this->idImage->handler()->extractorItems($Response);
        $total = 0;


        $this->idImage->handler()->query(null)
            ->where([
                'pid:IN' => array_keys($items),
            ])
            ->each(function (idImageClose $close) use (&$total, $items) {
                if (isset($items[$close->pid])) {
                    $item = $items[$close->pid];
                    $this->idImage->operation()->statusPoll($close, $item);
                }
                $total++;
            });

        return $this->success('', [
            'total' => $total,
        ]);
    }

}

return 'idImageStatusPollProcessor';
