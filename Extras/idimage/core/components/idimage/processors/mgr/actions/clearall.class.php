<?php

if (!class_exists('idImageActionsProcessor')) {
    include_once __DIR__.'/actions.class.php';
}

class idImageClearAllProcessor extends idImageActionsProcessor
{

    /**
     * @return array|string
     */
    public function process()
    {
        $limit = 1000;

        $processed = 0;

        /* @var idImageClose $object */
        $q = $this->modx->newQuery('idImageClose');

        // count items
        $total = $this->modx->getCount('idImageClose', $q);
        $q->limit($limit);

        $iteration = $total / $limit;

        if ($objectList = $this->modx->getCollection('idImageClose', $q)) {
            foreach ($objectList as $object) {
                $object->remove();
            }
        }

        return $this->success('', [
            'count' => $total,
            'processed' => $processed,
            'iteration' => $iteration,
        ]);
    }

}

return 'idImageClearAllProcessor';
