<?php

use IdImage\Support\xPDOQueryIdImage;

if (!class_exists('idImageProductTaskProcessor')) {
    include_once __DIR__.'/../task.class.php';
}

class idImageProductUploadProcessor extends idImageProductTaskProcessor implements \IdImage\Interfaces\ActionProgressBarOperation
{
    public function operation(): string
    {
        return 'upload';
    }

    public function criteria(xPDOQueryIdImage $query): void
    {
        // Только активные
        $query->where([
            'idImageClose.status' => idImageClose::STATUS_COMPLETED,
            'idImageClose.active' => true,
            'Embedding.id' => null,
        ]);

        // Только если есть Embedding
        $query->leftJoin('idImageEmbedding', 'Embedding', 'Embedding.hash = idImageClose.hash');
    }
}

return 'idImageProductUploadProcessor';
