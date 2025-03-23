<?php

namespace IdImage\Support;

use CURLFile;
use idImage;
use IdImage\Exceptions\ExceptionJsonModx;
use idImageSimilar;
use modX;
use PDO;

/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 19.02.2025
 * Time: 23:48
 */
class SimilarExtractor
{
    private ?array $items = null;
    private ?array $products = null;
    /**
     * @var array|bool|float|mixed
     */
    private int $total = 0;
    private idImage $idimage;
    /**
     * @var array|bool|float|mixed
     */
    private ?int $pid = null;

    public function __construct(idImage $idimage)
    {
        $this->idimage = $idimage;
    }

    public function load(idImageSimilar $similar)
    {
        $this->products = null;
        $this->items = $similar->allSimilar();
        $this->total = $similar->get('total');
        $this->pid = $similar->get('pid');

        return $this;
    }

    public function total()
    {
        return $this->total;
    }

    public function best(int $min_scope = 70, int $max_scope = 100, int $limit = 10)
    {
        if (!$this->items) {
            return $this;
        }

        $i = 0;
        $results = null;
        foreach ($this->items as $id => $item) {
            $probability = $item['probability'];
            $pid = (int)$item['offer_id'];
            if ($pid === $id || ($max_scope !== 100 && $probability > $max_scope)) {
                continue;
            }

            if ($min_scope < $probability) {
                $i++;
                $results[] = [
                    'pid' => $pid,
                    'probability' => $probability,
                ];
                if ($i >= $limit) {
                    break;
                }
            }
        }

        if ($results) {
            $this->products = $this->sortSimilar($results);
        }

        return $this;
    }

    public function products()
    {
        return !empty($this->products) ? $this->products : null;
    }

    public function probabilityProducts()
    {
        if ($this->isEmpty()) {
            return [];
        }
        $items = [];
        foreach ($this->products as $product) {
            $items[$product['pid']] = $product['probability'];
        }

        return $items;
    }

    public function ids()
    {
        if ($this->isEmpty()) {
            return [];
        }

        return array_column($this->products(), 'pid');
    }

    public function sortSimilar(array $products)
    {
        // Сортируем массив по вероятности
        usort($products, function ($a, $b) {
            return ($b['probability'] > $a['probability']) ? 1 : (($b['probability'] < $a['probability']) ? -1 : 0);
        });

        return $products;
    }

    public function images(): array
    {
        if (!$similar = $this->products()) {
            return [];
        }
        $products = [];
        $rows = null;

        $thumbnailSize = $this->idimage->modx->getOption('ms2_product_thumbnail_size', null, 'small');

        $q = $this->idimage->modx->newQuery('msProduct');
        $q->select('msProduct.id, File.url as url');
        $q->where(array(
            'msProduct.published' => true,
            'msProduct.id:IN' => $this->ids(),
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
            $offer_id = (int)$item['pid'];
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

        return $this->sortSimilar($products);
    }

    public function slice()
    {
        $limit = $this->idimage->limitShowSimilarProducts();
        if (!$products = $this->images()) {
            $products = [];
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

    public function isNotEmpty()
    {
        return $this->count() > 0;
    }

    public function isEmpty()
    {
        return !$this->isNotEmpty();
    }

    public function count()
    {
        if (empty($this->products)) {
            return 0;
        }

        return count($this->products);
    }

}
