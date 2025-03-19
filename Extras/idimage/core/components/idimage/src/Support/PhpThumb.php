<?php
/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 22.02.2025
 * Time: 10:34
 */

namespace IdImage\Support;

use Exception;
use modPhpThumb;
use modX;

class PhpThumb
{
    public static function makeThumbnail(modX $modx, string $source, string $target, array $options = []): string
    {
        if (!class_exists('modPhpThumb')) {
            if (file_exists(MODX_CORE_PATH.'model/phpthumb/modphpthumb.class.php')) {
                /** @noinspection PhpIncludeInspection */
                require MODX_CORE_PATH.'model/phpthumb/modphpthumb.class.php';
            } else {
                $modx->getService('phpthumb', 'modPhpThumb');
            }
        }


        $tmpPath = dirname($target);
        if (!is_dir($tmpPath)) {
            if (!mkdir($tmpPath, 0777, true)) {
                throw new Exception('Failed to create cache folder');
            }
        }

        $options = array_merge([
            'w' => 224,
            'h' => 224,
            'q' => 70,
            'zc' => '0',
            'bg' => 'FFFFFF',
            'f' => 'jpg',
        ], $options);
        $content = file_get_contents($source);
        $phpThumb = new modPhpThumb($modx);
        $phpThumb->initialize();
        $tf = tempnam(MODX_BASE_PATH, 'idimage_');
        file_put_contents($tf, $content);
        $phpThumb->setSourceFilename($tf);

        foreach ($options as $k => $v) {
            $phpThumb->setParameter($k, $v);
        }

        $output = false;

        if ($phpThumb->GenerateThumbnail() && $phpThumb->RenderOutput()) {
            //$modx->log(modX::LOG_LEVEL_INFO,'[miniShop2] phpThumb messages for .'.print_r($phpThumb->debugmessages, true));
            $output = $phpThumb->outputImageData;
        } else {
            $modx->log(modX::LOG_LEVEL_ERROR, '[miniShop2] Could not generate thumbnail for '.print_r($phpThumb->debugmessages, true));
        }

        if (file_exists($phpThumb->sourceFilename)) {
            @unlink($phpThumb->sourceFilename);
        }
        @unlink($tf);


        //$newFileName = uniqid('file_', true).'.jpg'; // Генерируем уникальное имя
        //$filePathTmp = $tmpPath.$newFileName; // Полный путь к файлу
        file_put_contents($target, $output);
        unset($output);

        return $target;
    }
}
