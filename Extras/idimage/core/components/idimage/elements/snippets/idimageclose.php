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
$min_scope = $modx->getOption('min_scope', $scriptProperties, 80);
$status = $modx->getOption('status', $scriptProperties, idImageClose::STATUS_COMPLETED);

if (empty($pid)) {
    return '';
}

// Build query
$c = $modx->newQuery('idImageClose');
$c->where([
    'pid' => $pid,
]);

/* @var idImageSimilar $object */
if (!$object = $modx->getObject('idImageSimilar', $c)) {
    return '';
}

// Iterate through items

$i = 0;
$results = [];
$similar = $object->getProducts();


$modx->setPlaceholder('idimage_similar', $similar);

if (!empty($similar) && is_array($similar)) {
    arsort($similar);

    foreach ($similar as $id => $item) {
        $probability = $item['probability'];
        $pid = (int)$item['pid'];
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
}


if (empty($results)) {
    // empty
    return '';
}

usort($results, function ($a, $b) {
    return ($b['probability'] > $a['probability']) ? 1 : (($b['probability'] < $a['probability']) ? -1 : 0);
});

// offer_id
$ids = array_column($results, 'pid');

// Output
return implode(',', $ids);

