<?php
/**
 * Индексация товаров первого уровня
 */

namespace IdImage\Ai\Types;

use IdImage\Abstracts\IndexedTypeAbstract;
use IdImage\Ai\CategoryTree;
use IdImage\Ai\ProductIndexer;
use IdImage\Ai\Similar;
use IdImage\Interfaces\IndexedTypeInterfaces;

class IndexFirstLevelCategory extends IndexedTypeAbstract implements IndexedTypeInterfaces
{
    public function build(ProductIndexer $productIndexer, CategoryTree $categoryTree): ProductIndexer
    {
        $productIndexer->reset();
        $categories = $categoryTree->all();
        $prods = $productIndexer->all();
        $root = array_shift($categories);
        foreach ($root['children'] as $category => $child) {
            $ids = [$category];
            $ids = !empty($child['children']) ? array_merge($ids, $categoryTree->getChildrenIds($child['children'])) : $ids;

            $products = [];
            foreach ($ids as $id) {
                if (array_key_exists($id, $prods)) {
                    $products = array_merge($products, $prods[$id]);
                }
            }

            $productIndexer->add($category, $products);
        }

        return $productIndexer;
    }

    public function comparison(Similar $similar, ProductIndexer $productIndexer): Similar
    {
        $items = $productIndexer->items();

        $parent = $similar->getParent();

        $rootLevel = $this->getSecondLevelParent($parent);
        $parentId = (int)$rootLevel['id'];

        $ids = [];
        foreach ($items as $item) {
            if ($parentId === $item['parent']) {
                foreach ($item['products'] as $id) {
                    $ids[$id] = $productIndexer->embedding($id);
                }
            }
        }


        $this->comparisonCosineSimilarity->compareSimilar($similar, $ids);

        return $similar;
    }
}
