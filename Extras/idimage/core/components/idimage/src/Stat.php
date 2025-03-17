<?php

namespace IdImage;

use idImage;
use IdImage\Api\Queue;
use IdImage\Api\Indexed;
use IdImage\Support\Client;
use idImageClose;
use idImageTask;
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


        $files = $query->filesCriteria()->count();

        $stat = [
            'total' => $this->idimage->query()->closes()->count(),
            'total_tasks' => $this->idimage->query()->tasks()->count(),
            'total_tasks_pending' => $this->idimage->query()->tasks()->where(['status' => idImageTask::STATUS_PENDING])->count(),
            'total_tasks_completed' => $this->idimage->query()->tasks()->where(['status' => idImageTask::STATUS_COMPLETED])->count(),
            'total_tasks_error' => $this->idimage->query()->tasks()->where(['status' => idImageTask::STATUS_FAILED])->count(),
            'total_files' => $files,
            'total_error' => $query->closes()->where(['status' => idImageClose::STATUS_FAILED])->count(),
            'total_completed' => $query->closes()->where(['status' => idImageClose::STATUS_COMPLETED])->count(),
            'total_embedding' => $query->embeddings()->count(),
            'total_similar' => $query->closes()->where(['total:!=' => 0])->count(),
        ];

        $data['stat'] = $stat;
        $data['crontabs'] = implode(PHP_EOL, $this->crontabs());
        $this->data = $data;

        return $this;
    }

    public function crontabs()
    {
        $bin = $this->phpBin();
        $path = $this->idimage->config['corePath'];

        $tasks = [
            'creating.php' => 'Создание товаров',
            'task/creation.php' => 'Создание заданий',
            'task/upload.php' => 'Загрузка изображений в idimage.ru (опционально, если нет доступа из глобального интернета)',
            'task/received.php' => 'Отправка задания',
            'task/poll.php' => 'Получение векторов',
            'indexed.php' => 'Индексация товаров',
        ];
        $crontabs = [];
        foreach ($tasks as $file => $name) {
            $cmd = sprintf('%s %scli/%s', $bin, $path, $file);

            #$cmd = sprintf('*/5 * * * *   %s >> %s/processors/%s.log 2>&1', $cmd, $path, $file);

            $crontabs[] = '# '.$name.PHP_EOL.$cmd.PHP_EOL;
        }

        return $crontabs;
    }

    public function phpBin()
    {
        if (php_sapi_name() === 'cli') {
            // путь до интерпретатора PHP в командной строке
            $php_executable = PHP_BINARY;
        } else {
            // путь до интерпретатора PHP в веб-сервере
            $php_executable = PHP_BINDIR.'/php';
        }

        return $php_executable;
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
