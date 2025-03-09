<?php

namespace IdImage;

use idImage;
use IdImage\Api\Queue;
use IdImage\Api\Indexed;
use IdImage\Support\Client;
use idImageClose;
use NumberFormatter;

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
            'php' => version_compare(PHP_VERSION, '7.4.0', '>='),
            'php_current' => phpversion(),
        ];

        $query = $this->idimage->query();

        $indexed_all = $query->closes()->where(['received' => true])->count();
        $stat = [
            'total' => $this->idimage->query()->closes()->count(),
            'embedding' => [
                'all' => $indexed_all,
                'empty' => $query->closes()->where(['received' => false])->count(),
            ],
            'indexed' => [
                'all' => $indexed_all,
                'completed' => $query->closes()->where(['status' => idImageClose::STATUS_COMPLETED])->count(),
            ],

            'total_error' => $query->closes()->where(['status' => idImageClose::STATUS_FAILED])->count(),
            'total_completed' => $query->closes()->where(['status' => idImageClose::STATUS_COMPLETED])->count(),
            'total_similar' => $query->closes()->where([
                'status' => idImageClose::STATUS_COMPLETED,
                'total:!=' => 0,
            ])->count(),
        ];

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
