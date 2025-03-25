<?php
/**
 * Индексация всего каталога (все товары)
 */

namespace IdImage\Ai\Types;

use IdImage\Abstracts\IndexedTypeAbstract;
use IdImage\Ai\CategoryTree;
use IdImage\Ai\ProductIndexer;
use IdImage\Ai\Similar;
use IdImage\Interfaces\IndexedTypeInterfaces;

class IndexAll extends IndexedTypeAbstract implements IndexedTypeInterfaces
{
    public function build(ProductIndexer $productIndexer, CategoryTree $categoryTree): ProductIndexer
    {
        $rootId = 0;

        $products = $productIndexer->all();
        if (!empty($products)) {
            $productIndexer->add($rootId, array_values($products));
        }

        return $productIndexer;
    }

    public function comparison(Similar $similar, ProductIndexer $productIndexer): Similar
    {
        $items = $productIndexer->items();

        $products = $items[0]['products'];

        $ids = [];
        foreach ($products as $item) {
            foreach ($item as $id) {
                $ids[$id] = $productIndexer->embedding($id);
            }
        }
        $this->comparisonCosineSimilarity->compareSimilar($similar, $ids);

        return $similar;
    }
}
