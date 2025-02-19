<?php
/** @var modX $modx */

/** @var array $scriptProperties */
/** @var idimage $idimage */
$idimage = $modx->getService('idimage', 'idImage', MODX_CORE_PATH.'components/idimage/model/', $scriptProperties);
if (!$idimage) {
    return 'Could not load idimage class!';
}

// Do your snippet code here. This demo grabs 5 items from our custom table.
$pid = $modx->getOption('pid', $scriptProperties, null);
$min_scope = $modx->getOption('min_scope', $scriptProperties, 80);

if (empty($pid)) {
    return '';
}

// Build query
$c = $modx->newQuery('idImageClose');
$c->where([
    'status' => idImageClose::STATUS_DONE,
    'pid' => $pid,
]);

/* @var idImageClose $object */
if (!$object = $modx->getObject('idImageClose', $c)) {
    return '';
}

// Iterate through items
$list = [];

$closes = $object->get('closes');
foreach ($closes as $id => $probability) {
    if ($min_scope <= $probability) {
        $list[$id] = $id;
    }
}

$modx->setPlaceholder('idimage_closes', $closes);

// Output
return implode(',', $list);

