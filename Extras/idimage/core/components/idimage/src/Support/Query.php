<?php

namespace IdImage\Support;

use idImage;

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
            //'Close.pid:IS' => null,
            'File.active' => true,
            'File.path:LIKE' => '%/'.$thumbnailSize.'/%',
        ]);
    }


    public function closes()
    {
        return $this->create('idImageClose');
    }

    public function similar()
    {
        return $this->create('idImageSimilar');
    }

    public function tasks()
    {
        return $this->create('idImageTask');
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
