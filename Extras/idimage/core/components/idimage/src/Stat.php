<?php

namespace IdImage;

use idImage;
use IdImage\Api\Queue;
use IdImage\Api\Indexed;
use IdImage\Support\Client;
use idImageClose;

/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 19.02.2025
 * Time: 23:48
 */
class Stat
{

    private idImage $idimage;

    public $data;


    public function __construct(idImage $idImage)
    {
        $this->idimage = $idImage;
    }

    public function process()
    {
        $modx = $this->idimage->modx;
        $data = [
            'enable' => !empty($modx->getOption('idimage_enable')),
            'token' => !empty($modx->getOption('idimage_token')),
            'cloud' => $this->idimage->isCloudUpload(),
            'validate_site_url' => $this->idimage->validateSiteUrl(),
            'zip' => (class_exists('ZipArchive') && extension_loaded('zip')),
            'php' => version_compare(PHP_VERSION, '7.4.0', '>='),
            'php_current' => phpversion(),
        ];

        $stat = [
            'total' => $this->idimage->query()->closes()->count(),
            'queue' => $this->idimage->query()->closes()->where(['status' => idImageClose::STATUS_QUEUE])->count(),
            'send' => $this->idimage->query()->closes()->where(['status' => idImageClose::STATUS_PROCESSING])->count(),
            'error' => $this->idimage->query()->closes()->where(['status' => idImageClose::STATUS_FAILED])->count(),
            'completed' => $this->idimage->query()->closes()->where(['status' => idImageClose::STATUS_COMPLETED])->count(),
            'closes' => $this->idimage->query()->closes()->where([
                'status' => idImageClose::STATUS_COMPLETED,
                'total:!=' => 0,
            ])->count(),
            'cloud_queue' => 0,
            'cloud_upload' => 0,
        ];
        if ($this->idimage->isCloudUpload()) {
            $stat['cloud_queue'] = $this->idimage->query()->closes()->where(['upload' => false])->count();
            $stat['cloud_upload'] = $this->idimage->query()->closes()->where(['upload' => true])->count();
        }

        $data['stat'] = $stat;
        $this->data = $data;

        return $this;
    }

    public function tpl()
    {
        $props = $this->toArray();

        $tplFile = $this->idimage->config['corePath'].'elements/pages/help.tpl';

        if (!file_exists($tplFile)) {
            return null;
        }
        $tpl = file_get_contents($tplFile);

        $uniqid = uniqid();
        $chunk = $this->idimage->modx->newObject('modChunk', array('name' => "{tmp}-{$uniqid}"));
        $chunk->setCacheable(false);
        $output = $chunk->process($props, $tpl);

        return $output;
    }

    public function toArray()
    {
        return $this->data;
    }
}
