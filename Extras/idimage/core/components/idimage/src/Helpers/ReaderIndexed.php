<?php

namespace IdImage\Helpers;

use Exception;
use IdImage\Entities\EntityIndexed;
use ZipArchive;

/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 19.02.2025
 * Time: 23:48
 */
class ReaderIndexed
{

    private EntityIndexed $indexed;

    private $closes = null;
    private $total = null;

    public function __construct(EntityIndexed $indexed)
    {
        $this->indexed = $indexed;
    }

    public function read(string $path)
    {
        $url = $this->indexed->downloadLink();

        $pathArchiveFile = $path.basename($url);
        $pathJsonFile = $path.$this->indexed->filenameVersion();


        if (!is_dir($path)) {
            if (!mkdir($path, 0777, true)) {
                throw new Exception('Ошибка создания директории: '.$path);
            }
        }

        if (!file_exists($pathJsonFile)) {
            if (!file_exists($pathArchiveFile)) {
                $content = file_get_contents($url);
                if (!file_put_contents($pathArchiveFile, $content)) {
                    throw new Exception('Ошибка записи в архив');
                }
            }

            // распаковать zip
            $zip = new ZipArchive;
            $zip->open($pathArchiveFile);
            if (!$res = $zip->extractTo($path)) {
                throw new Exception('Ошибка распаковки');
            }
            $zip->close();
            if (!file_exists($pathJsonFile)) {
                throw new Exception('Ошибка записи в json');
            }
        }

        $content = file_get_contents($pathJsonFile);
        if (empty($content)) {
            throw new Exception('Нет данных');
        }

        $data = json_decode($content, true);

        if (!is_array($data)) {
            throw new Exception('Ошибка чтения данных');
        }
        if (!isset($data['closes'])) {
            throw new Exception('closes field is empty');
        }

        if (!is_array($data['closes'])) {
            throw new Exception('Error closes field is not array');
        }

        if (!is_array($data['total'])) {
            throw new Exception('Error closes field is not array');
        }


        $this->total = $data['total'];
        $this->closes = $data['closes'];

        return $this;
    }

    public function closes()
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

        return array_keys($this->closes());
    }

    public function totalCloses()
    {
        return is_array($this->closes) ? count($this->closes) : 0;
    }

}
