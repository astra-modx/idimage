<?php
/** @var modX $modx */
/* @var array $scriptProperties */
switch ($modx->event->name) {
    case 'OnHandleRequest':
        /* @var idimage $idimage*/
        $idimage = $modx->getService('idimage', 'idimage', $modx->getOption('idimage_core_path', $scriptProperties, $modx->getOption('core_path') . 'components/idimage/') . 'model/');
        if ($idimage instanceof idimage) {
            $idimage->loadHandlerEvent($modx->event, $scriptProperties);
        }
        break;
}
return '';