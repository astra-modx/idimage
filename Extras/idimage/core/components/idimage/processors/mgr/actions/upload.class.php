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
        $chunk = $this->idImage->mode() === 'picture' ? 100 : 10;

        $Handler = $this->idImage->handler();
        $query = $Handler->query();
        if ($this->setCheckbox('count_iteration')) {
            $ids = $query->where([
                'status:!=' => idImageClose::STATUS_PROCESSING,
            ])->ids();
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


        $query->where(['id:IN' => $ids]);
        switch ($this->idImage->mode()) {
            case 'picture':
                $this->idImage->operation()->picture($query);
                break;
            case 'image':
                $query->each(function (idImageClose $close) use (&$total) {
                    $this->idImage->operation()->upload($close, true);
                    $total++;
                });
                break;
            default:
                break;
        }

        sleep(1); // otherwise it blocks due to a large number of requests

        return $this->success('upload');
    }

}

return 'idImageUploadProcessor';
