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
$pid = $modx->getOption('pid', $scriptProperties, null);
$max_scope = $modx->getOption('max_scope', $scriptProperties, 100);
$min_scope = $modx->getOption('min_scope', $scriptProperties, 80);

if (empty($pid)) {
    return '';
}

// Build query
$c = $modx->newQuery('idImageClose');
$c->where([
    'status' => idImageClose::STATUS_COMPLETED,
    'pid' => $pid,
]);

/* @var idImageClose $object */
if (!$object = $modx->getObject('idImageClose', $c)) {
    return '';
}

// Iterate through items

$i = 0;
$results = [];
$similar = $object->get('similar');
if (!empty($similar) && is_array($similar)) {
    arsort($similar);

    foreach ($similar as $id => $probability) {
        if ($pid === $id || $max_scope < $probability) {
            continue;
        }
        if ($min_scope < $probability) {
            $i++;
            $results[$id] = $id;
            if ($i >= $limit) {
                break;
            }
        }
    }
}
$modx->setPlaceholder('idimage_similar', $results);

// Output
return implode(',', $results);

