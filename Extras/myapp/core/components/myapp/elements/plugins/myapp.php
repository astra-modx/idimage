<?php
/** @var modX $modx */
/* @var array $scriptProperties */
switch ($modx->event->name) {
    case 'OnHandleRequest':
        /* @var myapp $myapp*/
        $myapp = $modx->getService('myapp', 'myapp', $modx->getOption('myapp_core_path', $scriptProperties, $modx->getOption('core_path') . 'components/myapp/') . 'model/');
        if ($myapp instanceof myapp) {
            $myapp->loadHandlerEvent($modx->event, $scriptProperties);
        }
        break;
}
return '';