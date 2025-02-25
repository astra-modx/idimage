<?php

namespace IdImage;

use idImage;
use IdImage\Helpers\xPDOQueryIdImage;

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
        $query = $this->create('msProductFile');
        $query->select('msProductFile.id as file_id,msProductFile.product_id as id, msProductFile.url as image, msProductFile.path as path');
        $query->leftJoin('idImageClose', 'Close', 'Close.pid = msProductFile.product_id');
        $query->innerJoin('msProduct', 'msProduct', 'msProduct.id = msProductFile.product_id');


        return $query;
    }

    public function filesCriteria()
    {
        $cropSize = $this->idImage->modx->getOption('idimage_crop_size', null, 'small');

        return $this->files()->where([
            'msProduct.published' => true,
            'msProduct.deleted:!=' => true,
            'Close.pid:IS' => null,
            'msProductFile.path:LIKE' => '%/'.$cropSize.'/%',
        ]);
    }


    public function closes()
    {
        return $this->create('idImageClose');
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
