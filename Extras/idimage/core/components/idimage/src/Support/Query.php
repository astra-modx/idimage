<?php

namespace IdImage\Support;

use idImage;
use idImageTask;

/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 19.02.2025
 * Time: 23:48
 */
class Query
{
    private idImage $idImage;

    public function __construct(idImage $idImage)
    {
        $this->idImage = $idImage;
    }

    public function create($class)
    {
        return new xPDOQueryIdImage($this->idImage->modx, $class);
    }

    public function files()
    {
        $query = $this->create('msProduct');
        $query->select('File.id as file_id,File.product_id as id, File.url as image, File.path as path');
        //$query->leftJoin('idImageClose', 'Close', 'Close.pid = msProductFile.product_id');
        $query->innerJoin('msProductFile', 'File', 'File.product_id = msProduct.id');

        return $query;
    }

    public function filesCriteria()
    {
        $thumbnailSize = $this->idImage->modx->getOption('ms2_product_thumbnail_size', null, 'small');

        return $this->files()->where([
            'msProduct.published' => true,
            'msProduct.deleted:!=' => true,
            'File.rank' => 0,
            'File.active' => true,
            'File.path:LIKE' => '%/'.$thumbnailSize.'/%',
        ]);
    }


    public function closes()
    {
        return $this->create('idImageClose');
    }

    public function closesEmbedding()
    {
        return $this->closes()->innerJoin('idImageEmbedding', 'Embedding', 'Embedding.pid = idImageClose.pid');
    }

    public function similar()
    {
        return $this->create('idImageSimilar');
    }

    public function tasks()
    {
        return $this->create('idImageTask');
    }

    public function tasksExecuteAt()
    {
        return $this->tasks()->where([
            'execute_at:>=' => time(),
            'AND:execute_at:!=' => null,
        ]);
    }

    public function tasksQueue()
    {
        $query = $this->tasks()
            ->where([
                'status:IN' => [
                    idImageTask::STATUS_PENDING,
                    idImageTask::STATUS_CREATED,
                    idImageTask::STATUS_RETRY, // Повторны статус по кол-ву ошибок
                ],
            ])
            ->andCondition(array(
                'execute_at:<=' => time(), // Только если время исполнения настало
                'OR:execute_at:=' => null, // Только если время исполнения настало
            ));

        return $query;
    }

    public function embeddings()
    {
        return $this->create('idImageEmbedding')->select($this->idImage->modx->getSelectColumns('idImageEmbedding', 'idImageEmbedding', ''));
    }

    public function indexeds()
    {
        return $this->create('idImageIndexed');
    }

    public function closesCriteria()
    {
        return $this->closes()->where([
            'active' => 1,
        ]);
    }


}
