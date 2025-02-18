<?php
/** @var xPDOTransport $transport */
/** @var array $options */
/** @var modX $modx */
if ($transport->xpdo) {
    $modx =& $transport->xpdo;

    $dev = MODX_BASE_PATH . 'Extras/idimage/';
    /** @var xPDOCacheManager $cache */
    $cache = $modx->getCacheManager();
    if (file_exists($dev) && $cache) {
        if (!is_link($dev . 'assets/components/idimage')) {
            $cache->deleteTree(
                $dev . 'assets/components/idimage/',
                ['deleteTop' => true, 'skipDirs' => false, 'extensions' => []]
            );
            symlink(MODX_ASSETS_PATH . 'components/idimage/', $dev . 'assets/components/idimage');
        }
        if (!is_link($dev . 'core/components/idimage')) {
            $cache->deleteTree(
                $dev . 'core/components/idimage/',
                ['deleteTop' => true, 'skipDirs' => false, 'extensions' => []]
            );
            symlink(MODX_CORE_PATH . 'components/idimage/', $dev . 'core/components/idimage');
        }
    }
}

return true;