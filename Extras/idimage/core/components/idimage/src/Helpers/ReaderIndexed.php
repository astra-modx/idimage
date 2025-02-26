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

    public function download(string $source, string $target)
    {
        $zipData = file_get_contents($source);

        if (!file_put_contents($target, $zipData)) {
            throw new Exception('Ошибка записи в архив');
        }

        // распаковать zip
        $zip = new ZipArchive;
        $dir = dirname($target);
        if ($zip->open($target) === true) {
            if (!$zip->extractTo($dir)) {
                throw new Exception('Error extracting archive to directory '.$target);
            }
            $zip->close();
        } else {
            throw new Exception('Failed to open ZIP archive');
        }

        return $this;
    }

    public function read(string $source)
    {
        if (!file_exists($source)) {
            throw new Exception("File $source not found");
        }

        $content = file_get_contents($source);
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
