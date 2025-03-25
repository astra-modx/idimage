<?php

use IdImage\Support\xPDOQueryIdImage;

if (!class_exists('idImageProductTaskProcessor')) {
    include_once __DIR__.'/../task.class.php';
}

class idImageProductIndexedProcessor extends idImageProductTaskProcessor implements \IdImage\Interfaces\ActionProgressBar
{
    public function operation(): string
    {
        return 'indexed';
    }

    public function criteria(xPDOQueryIdImage $query): void
    {
        // Только активные
        $query->where([
            'idImageClose.status' => idImageClose::STATUS_COMPLETED,
            'idImageClose.active' => true,
        ]);

        // Только если есть Embedding
        $query->innerJoin('idImageEmbedding', 'Embedding', 'Embedding.hash = idImageClose.hash');
    }

}

return 'idImageProductIndexedProcessor';
