<?php

use IdImage\Entites\TaskEntity;

/**
 * @package idimage
 */
class idImageClose extends xPDOSimpleObject
{

    const STATUS_QUEUE = 1;
    const STATUS_PROCESSING = 2;
    const STATUS_INVALID = 3;
    const STATUS_FAILED = 4;
    const STATUS_COMPLETED = 5;
    const STATUS_UNKNOWN = 7;
    const STATUS_NOT_FOUND_SIMILAR = 8;


    static $statusMap = [
        self::STATUS_QUEUE => 'queue',
        self::STATUS_PROCESSING => 'processing',
        self::STATUS_FAILED => 'failed',
        self::STATUS_COMPLETED => 'completed',
        self::STATUS_INVALID => 'invalid',
        self::STATUS_UNKNOWN => 'unknown',
        self::STATUS_NOT_FOUND_SIMILAR => 'similar_not_found',
    ];


    public function save($cacheFlag = null)
    {
        if (!$this->isNew()) {
            $this->set('updatedon', time());
        } else {
            $this->set('createdon', time());
        }

        return parent::save($cacheFlag);
    }

    public function change(string $imagePath)
    {
        if ($this->isNew() || !$this->get('received')) {
            return true;
        }

        if (empty($this->get('hash'))) {
            return true;
        }

        if (!$hash = $this->createHash($imagePath)) {
            return false;
        }

        return $hash != $this->get('hash');
    }

    public function createHash(string $imagePath)
    {
        if (!file_exists($imagePath)) {
            return null;
        }

        $sizes = @getimagesize($imagePath);
        $sizes['size'] = filesize($imagePath);

        return md5(json_encode($sizes));
    }

    public function link(string $host)
    {
        $picture = $this->get('picture');
        if (empty($picture)) {
            return null;
        }

        return rtrim($host, '/').'/'.ltrim($picture, '/');
    }


    public function picturePath(bool $absolute = true)
    {
        $path = ltrim($this->get('picture'), '/');
        if (!$absolute) {
            return $path;
        }

        return MODX_BASE_PATH.$path;
    }

    public function embedding(): idImageEmbedding
    {
        /* @var idImageEmbedding $Embedding */
        if (!$Embedding = $this->getOne('Embedding')) {
            $Embedding = $this->xpdo->newObject('idImageEmbedding');
            $Embedding->set('hash', $this->get('hash'));
            $Embedding->set('pid', $this->get('pid'));
        }

        return $Embedding;
    }

    public function task()
    {
        return $this->getOne('Task');
    }

    public function getEmbedding()
    {
        if (!$this->embedding()) {
            return null;
        }

        return $this->embedding()->getEmbedding();
    }


    public function getProducts()
    {
        $similar = $this->get('similar');
        $products = [];

        $idImage = $this->xpdo->getService('idimage', 'idImage', MODX_CORE_PATH.'components/idimage/model/');
        $limit = $idImage->limitShowSimilarProducts();
        if (!empty($similar)) {
            if (!empty($similar) && is_array($similar)) {
                $ids = array_column($similar, 'offer_id');
                $rows = null;

                $thumbnailSize = $this->xpdo->getOption('ms2_product_thumbnail_size', null, 'small');

                $q = $this->xpdo->newQuery('msProduct');
                $q->select('msProduct.id, File.url as url');
                $q->where(array(
                    'msProduct.published' => true,
                    'msProduct.id:IN' => $ids,
                    'File.active' => true,
                    'File.path:LIKE' => '%/'.$thumbnailSize.'/%',
                ));
                $q->innerJoin('msProductFile', 'File', 'File.product_id = msProduct.id');
                if ($q->prepare() && $q->stmt->execute()) {
                    while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                        $rows[(int)$row['id']] = $row['url'];
                    }
                }

                foreach ($similar as $id => $item) {
                    $offer_id = (int)$item['offer_id'];
                    if (!isset($rows[$offer_id])) {
                        continue;
                    }
                    $image = $rows[$offer_id];

                    $products[] = [
                        'pid' => $offer_id,
                        'image' => $image,
                        'probability' => $item['probability'],
                    ];
                }

                // Сортируем массив по вероятности
                usort($products, function ($a, $b) {
                    return ($b['probability'] > $a['probability']) ? 1 : (($b['probability'] < $a['probability']) ? -1 : 0);
                });
            }
        }

        // Ограничиваем массив до 5 элементов
        $products = array_slice($products, 0, $limit);

        // Если в массиве меньше 5 элементов, заполняем недостающие значениями по умолчанию
        $default_product = [
            'pid' => 0,
            'image' => null, // Путь к изображению по умолчанию
            'probability' => 0,
        ];

        while (count($products) < $limit) {
            $products[] = $default_product;
        }

        return $products;
    }


}
