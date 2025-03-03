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
    protected $total = [
        'all' => 0,
        'completed' => 0,
        'error' => 0,
    ];
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
            throw new ExceptionJsonModx('Ссылка на скачивание пустая');
        }

        if (empty($pathZip)) {
            throw new ExceptionJsonModx('target is empty');
        }
    }

    function fetchZipData(string $url): ?string
    {
        $options = [
            'http' => [
                'method' => 'GET',
                'timeout' => 30,
                'header' => "User-Agent: PHP",
            ],
        ];

        $context = stream_context_create($options);

        // Открываем поток и получаем содержимое
        $data = @file_get_contents($url, false, $context);

        // Проверяем, что удалось получить данные
        if ($data === false) {
            throw new ExceptionJsonModx('Не удалось загрузить архив: '.$url.'. Файл не доступен для скачивания или доступ к нему ограничен');
        }

        // Получаем HTTP-статус из заголовков
        $httpResponseHeader = $http_response_header ?? [];
        if (!empty($httpResponseHeader)) {
            foreach ($httpResponseHeader as $header) {
                if (preg_match('/^HTTP\/\d\.\d\s+(\d+)/', $header, $matches)) {
                    $statusCode = (int)$matches[1];
                    if ($statusCode !== 200) {
                        throw new ExceptionJsonModx('Ошибка при загрузке архива: HTTP status code '.$statusCode);
                    }
                    break;
                }
            }
        }

        return $data;
    }

    public function download()
    {
        $zipData = $this->fetchZipData($this->downloadUrl);

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
                $similar = null;
                if (!empty($item['similar'])) {
                    foreach ($item['similar'] as $offer) {
                        $similar[$offer['offer_id']] = $offer['probability'];
                    }
                }
                $items[$id] = [
                    'status' => (int)$item['status'],
                    'total' => (int)$item['total_close'] ?? 0,
                    'min_scope' => (int)$item['min_scope'] ?? 0,
                    'similar' => $similar,
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
