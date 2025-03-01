<?php

namespace IdImage\Support;

use IdImage\Exceptions\ExceptionJsonModx;
use ZipArchive;

/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 19.02.2025
 * Time: 23:48
 */
class ReaderIndexed
{

    protected $closes = null;
    protected $total = 0;
    /**
     * @var string|null
     */
    private $path;
    /**
     * @var string|null
     */
    private $pathZip;
    private $downloadUrl;

    public function __construct(string $path, string $pathZip, $downloadUrl = null)
    {
        $this->path = $path;
        $this->pathZip = $pathZip;
        $this->downloadUrl = $downloadUrl;

        if (empty($this->downloadUrl)) {
            throw new ExceptionJsonModx('source is empty');
        }

        if (empty($pathZip)) {
            throw new ExceptionJsonModx('target is empty');
        }
    }

    public function download()
    {
        $zipData = file_get_contents($this->downloadUrl);

        if (!file_put_contents($this->pathZip, $zipData)) {
            throw new ExceptionJsonModx('Ошибка записи в архив');
        }

        // распаковать zip
        $zip = new ZipArchive;
        $dir = dirname($this->pathZip);
        if ($zip->open($this->pathZip) === true) {
            if (!$zip->extractTo($dir)) {
                throw new ExceptionJsonModx('Error extracting archive to directory '.$this->pathZip);
            }
            $zip->close();
        } else {
            throw new ExceptionJsonModx('Failed to open ZIP archive');
        }

        return $this;
    }

    public function read()
    {
        if (!file_exists($this->path)) {
            throw new ExceptionJsonModx("File {$this->path} not found");
        }

        $content = file_get_contents($this->path);
        if (empty($content)) {
            throw new ExceptionJsonModx('Нет данных');
        }

        $data = json_decode($content, true);

        if (!is_array($data)) {
            throw new ExceptionJsonModx('Ошибка чтения данных');
        }
        if (!isset($data['closes'])) {
            throw new ExceptionJsonModx('closes field is empty');
        }

        if (!is_array($data['closes'])) {
            throw new ExceptionJsonModx('Error closes field is not array');
        }

        if (!is_array($data['total'])) {
            throw new ExceptionJsonModx('Error closes field is not array');
        }


        $this->total = $data['total'];
        $this->closes = $data['closes'];

        return $this;
    }

    public function items()
    {
        $items = null;

        if ($this->closes) {
            foreach ($this->closes as $item) {
                $id = (int)$item['offer_id'];
                $closes = null;
                if (!empty($item['closes'])) {
                    foreach ($item['closes'] as $offer) {
                        $closes[$offer['offer_id']] = $offer['probability'];
                    }
                }
                $items[$id] = [
                    'status' => (int)$item['status'],
                    'total' => (int)$item['total_close'] ?? 0,
                    'min_scope' => (int)$item['min_scope'] ?? 0,
                    'closes' => $closes,
                ];
            }
        }

        return $items;
    }

    public function offersKeys()
    {
        if (!$this->closes) {
            return [];
        }

        return array_keys($this->items());
    }

    public function totalCloses()
    {
        return is_array($this->closes) ? count($this->closes) : 0;
    }

}
