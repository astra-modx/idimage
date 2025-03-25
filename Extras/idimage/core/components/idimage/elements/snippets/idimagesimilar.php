<?php
/** @var modX $modx */

/** @var array $scriptProperties */
/** @var idimage $idimage */
$idimage = $modx->getService('idimage', 'idImage', MODX_CORE_PATH.'components/idimage/model/', $scriptProperties);
if (!$idimage) {
    return 'Could not load idimage class!';
}

// Do your snippet code here. This demo grabs 5 items from our custom table.
$limit = $modx->getOption('limit', $scriptProperties, 10);
$pid = $modx->getOption('pid', $scriptProperties, $modx->resource->id, true);
$max_scope = $modx->getOption('max_scope', $scriptProperties, 100);
$min_scope = $modx->getOption('min_scope', $scriptProperties, 70);

$ids = [];
$products = [];
$similar = [];
$probabilityProducts = [];
if (!empty($pid)) {
    /* @var idImageSimilar $object */
    if ($object = $modx->getObject('idImageSimilar', ['pid' => $pid])) {
        $extractor = $idimage->extractor();
        $extractor->load($object)->best($min_scope, $max_scope, $limit);

        // Получаем лучшие похожие
        if ($extractor->isNotEmpty()) {
            // Получаем id товаров
            $ids = $extractor->ids();
            $products = $extractor->products();
            $probabilityProducts = $extractor->probabilityProducts();
        }
    }
}
// Iterate through items


// empty
$ids = !empty($ids) ? implode(',', $ids) : '';


$modx->setPlaceholder('idimage.ids', $ids);
$modx->setPlaceholder('idimage.products', $products);
$modx->setPlaceholder('idimage.probability', $probabilityProducts);

// Output
return '';

