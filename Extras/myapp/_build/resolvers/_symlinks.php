<?php
/** @var xPDOTransport $transport */
/** @var array $options */
/** @var modX $modx */
if ($transport->xpdo) {
    $modx =& $transport->xpdo;

    $dev = MODX_BASE_PATH . 'Extras/myapp/';
    /** @var xPDOCacheManager $cache */
    $cache = $modx->getCacheManager();
    if (file_exists($dev) && $cache) {
        if (!is_link($dev . 'assets/components/myapp')) {
            $cache->deleteTree(
                $dev . 'assets/components/myapp/',
                ['deleteTop' => true, 'skipDirs' => false, 'extensions' => []]
            );
            symlink(MODX_ASSETS_PATH . 'components/myapp/', $dev . 'assets/components/myapp');
        }
        if (!is_link($dev . 'core/components/myapp')) {
            $cache->deleteTree(
                $dev . 'core/components/myapp/',
                ['deleteTop' => true, 'skipDirs' => false, 'extensions' => []]
            );
            symlink(MODX_CORE_PATH . 'components/myapp/', $dev . 'core/components/myapp');
        }
    }
}

return true;