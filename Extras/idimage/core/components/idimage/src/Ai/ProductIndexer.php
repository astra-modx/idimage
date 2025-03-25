<?php
/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 22.03.2025
 * Time: 14:53
 */

namespace IdImage\Ai;

use idImage;
use idImageClose;

class ProductIndexer
{
    private $products;
    protected array $embedding = [];
    protected array $items = [];
    /**
     * @var true
     */
    private bool $build = false;

    public function build(idImage $idImage)
    {
        if ($this->build === false) {
            $this->build = true;
            $products = [];
            $query = $idImage->query()->closes();
            $query->select('idImageClose.pid as pid, Embedding.data as data,Product.parent as parent');
            $query->innerJoin('idImageEmbedding', 'Embedding', 'Embedding.hash = idImageClose.hash');
            $query->where([
                'idImageClose.active' => true,
                'idImageClose.status' => idImageClose::STATUS_COMPLETED,
            ]);
            $query->leftJoin('msProduct', 'Product', 'Product.id = idImageClose.pid');

            $query->collection(function ($item) use (&$products) {
                $pid = (int)$item['pid'];
                $parent = (int)$item['parent'];
                $embedding = json_decode($item['data'], true);
                if (is_array($embedding) && count($embedding) === 512) {
                    $products[$parent][] = $pid;
                    $this->embedding[$pid] = [
                        'parent' => $parent,
                        'embedding' => $embedding,
                    ];
                }
            });
            $this->products = $products;
        }

        return $this;
    }


    public function add(int $parentId, array $products)
    {
        $this->items[] = [
            'parent' => $parentId,
            'products' => $products,
        ];

        return $this;
    }

    public function items()
    {
        return $this->items;
    }

    public function all()
    {
        return $this->products;
    }

    public function count()
    {
        return count($this->embedding);
    }

    public function isEmpty()
    {
        return empty($this->embedding);
    }

    public function isNotEmpty()
    {
        return !$this->isEmpty();
    }


    public function reset()
    {
        $this->items = [];

        return $this;
    }

    public function embedding(int $pid)
    {
        return $this->embedding[$pid] ?? null;
    }

}
