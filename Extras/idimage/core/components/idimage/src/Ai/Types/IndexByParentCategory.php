<?php
/**
 * Индексация по родительской категории
 */

namespace IdImage\Ai\Types;

use IdImage\Abstracts\IndexedTypeAbstract;
use IdImage\Ai\CategoryTree;
use IdImage\Ai\ProductIndexer;
use IdImage\Ai\Similar;
use IdImage\Interfaces\IndexedTypeInterfaces;

class IndexByParentCategory extends IndexedTypeAbstract implements IndexedTypeInterfaces
{
    public function build(ProductIndexer $productIndexer, CategoryTree $categoryTree): ProductIndexer
    {
        $productIndexer->reset();
        // Сначала добавляем товары из самой родительской категории
        $products = $productIndexer->all();
        foreach ($products as $parent => $arrays) {
            $productIndexer->add($parent, $arrays);
        }

        return $productIndexer;
    }

    public function comparison(Similar $similar, ProductIndexer $productIndexer): Similar
    {
        $items = $productIndexer->items();

        $parent = $similar->getParent();


        $products = $items;

        $ids = [];
        foreach ($products as $item) {
            if ($parent === $item['parent']) {
                foreach ($item['products'] as $id) {
                    $ids[$id] = $productIndexer->embedding($id);
                }
            }
        }

        $this->comparisonCosineSimilarity->compareSimilar($similar, $ids);

        return $similar;
    }

}
