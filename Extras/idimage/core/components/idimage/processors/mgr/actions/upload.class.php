<?php

if (!class_exists('idImageActionsProcessor')) {
    include_once __DIR__.'/actions.class.php';
}

class idImageUploadProcessor extends idImageActionsProcessor
{

    /**
     * @return array|string
     */
    public function process()
    {
        $chunk = 10;

        $Handler = $this->idImage->handler();
        $query = $Handler->query();
        if ($this->setCheckbox('count_iteration')) {
            $ids = $query->ids();
            $total = count($ids);
            $ids = array_chunk($ids, $chunk);

            return $this->success('', [
                'iterations' => $ids,
                'total' => $total,
            ]);
        }

        $ids = $this->getProperty('ids');

        if (empty($ids)) {
            return $this->success('upload', [
                'total' => 0,
            ]);
        }

        $ids = json_decode($ids, true);

        if (!is_array($ids)) {
            return $this->success('upload', [
                'total' => 0,
            ]);
        }
        $ids = array_filter(array_map('intval', $ids));


        $query
            ->where([
                'id:IN' => $ids,
            ])
            ->each(function (idImageClose $close) use (&$total) {
                $this->idImage->operation()->upload($close, true);
                $total++;
            });


        return $this->success('upload');
    }

}

return 'idImageUploadProcessor';
