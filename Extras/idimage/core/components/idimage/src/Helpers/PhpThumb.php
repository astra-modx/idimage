<?php
/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 22.02.2025
 * Time: 10:34
 */

namespace IdImage\Helpers;


use Closure;
use Exception;
use idImage;
use modPhpThumb;
use modX;

class PhpThumb
{
    private modX $modx;

    public function __construct(modX $modx)
    {
        $this->modx = $modx;
        if (!class_exists('modPhpThumb')) {
            if (file_exists(MODX_CORE_PATH.'model/phpthumb/modphpthumb.class.php')) {
                /** @noinspection PhpIncludeInspection */
                require MODX_CORE_PATH.'model/phpthumb/modphpthumb.class.php';
            } else {
                $modx->getService('phpthumb', 'modPhpThumb');
            }
        }
    }

    /* public function upload(idImageClose $Close, $check = false)
     {
         try {
             // Готовим изображение
             $this->makeThumbnail($imagePath, function ($path) use ($Close) {
                 // Отправляем изображения в сервис
                 $response = $this->idImage->client()->upload((string)$Close->pid, $path)->send();
                 $this->fromResult($Close, $response);
             });
         } catch (Exception $e) {
             $Close->set('status', idImageClose::STATUS_FAILED);
         }

         return $Close->save();
     }*/


    public function makeThumbnail(string $path, Closure $callback, array $options = [])
    {
        $options = array_merge([
            'w' => 224,
            'h' => 224,
            'q' => 50,
            'zc' => 'T',
            'bg' => '000000',
            'f' => 'jpg',
        ], $options);
        $content = file_get_contents($path);


        $phpThumb = new modPhpThumb($this->modx);
        $phpThumb->initialize();


        $tf = tempnam(MODX_BASE_PATH, 'idimage_');
        file_put_contents($tf, $content);
        $phpThumb->setSourceFilename($tf);

        foreach ($options as $k => $v) {
            $phpThumb->setParameter($k, $v);
        }

        $output = false;
        if ($phpThumb->GenerateThumbnail() && $phpThumb->RenderOutput()) {
            $this->modx->log(
                modX::LOG_LEVEL_INFO,
                '[miniShop2] phpThumb messages for .'.print_r($phpThumb->debugmessages, true)
            );
            $output = $phpThumb->outputImageData;
        } else {
            $this->modx->log(
                modX::LOG_LEVEL_ERROR,
                '[miniShop2] Could not generate thumbnail for '.print_r($phpThumb->debugmessages, true)
            );
        }

        if (file_exists($phpThumb->sourceFilename)) {
            @unlink($phpThumb->sourceFilename);
        }
        @unlink($tf);

        $pathTmp = MODX_BASE_PATH.'assets/tests.jpg';
        file_put_contents($pathTmp, $output);
        unset($output);

        try {
            $callback($pathTmp);
        } catch (Exception $e) {
            if (file_exists($pathTmp)) {
                unlink($pathTmp);
            }

            throw $e;
        }

        return true;
    }
}
