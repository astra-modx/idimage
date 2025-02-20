<?php
/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 19.02.2025
 * Time: 23:48
 */

class idImageOperation
{
    /* @var idImage $idImage */
    private $idImage;
    /**
     * @var string
     */
    /**
     * @var array|mixed|string
     */
    private $site_url;

    public function __construct(idImage $idImage)
    {
        $this->idImage = $idImage;
        $this->site_url = rtrim($this->idImage->modx->getOption('site_url'), '/');
    }

    public function statusPoll(idImageClose $Close, array $item, $save = true)
    {
        $status = $item['status'] ?? idImageClose::STATUS_FAILED;
        $version = $item['version'] ?? 0;
        $total_close = $item['total_close'] ?? 0;
        $min_scope = $item['min_scope'] ?? 0;
        $closes = $item['closes'] ?? [];

        $Close->set('status', $status);
        $Close->set('version', $version);
        $Close->set('total_close', $total_close);
        $Close->set('min_scope', $min_scope);
        $Close->set('closes', $closes);

        if (!$save) {
            return true;
        }

        return $Close->save();
    }

    /**
     * @param  idImageClose  $Close
     * @param  idimageResponse  $response
     * @return void
     */
    public function fromResult(idImageClose $Close, idimageResponse $response, $status = null)
    {
        if (empty($status)) {
            $status = $response->isOk() ? idImageClose::STATUS_BUILD : idImageClose::STATUS_FAILED;
        }

        $Close->set('status', $status);

        $Close->set('status_code', $response->getStatus());

        // Ставим метку о доставке
        $Close->set('received', $response->isOk());

        // Пишем дату доставки
        $Close->set('received_at', time());
    }


    public function picture($query)
    {
        $offers = null;
        $query->each(function (idImageClose $close) use (&$offers) {
            if ($offer = $this->idImage->operation()->addOffer($close, false)) {
                $offers[] = $offer;
            }
        });

        if ($offers) {
            $response = $this->idImage->client()->create($offers)->send();

            $items = [];
            if ($response->isOk()) {
                $data = $response->json();
                if (!empty($data['items']) && is_array($data['items'])) {
                    foreach ($data['items'] as $item) {
                        $items[$item['offer_id']] = $item['success'];
                    }
                }
            }


            $query->each(function (idImageClose $close) use ($items, $response) {
                $status = !empty($items[$close->get('pid')]) ? idImageClose::STATUS_BUILD : idImageClose::STATUS_FAILED;
                $this->fromResult($close, $response, $status);
                $close->save();
            });
        }

        return true;
    }


    private function addOffer(idImageClose $Close, $check = false)
    {
        $picture = $Close->get('picture');
        $url = $this->site_url.'/'.ltrim($picture, '/');
        if (!$this->checkup($Close, $check)) {
            return false;
        }

        if ($this->isLocalUrl($url)) {
            $Close->set('status', idImageClose::STATUS_FAILED);
            #  return false;
        }

        $data = [
            'offer_id' => (string)$Close->get('pid'),
            'picture' => $url,
        ];

        $tags = $Close->get('tags');
        if (is_array($tags)) {
            $data['tags'] = $tags;
        }


        return $data;
    }


    public function checkup(idImageClose $Close, $check = false)
    {
        $id = $Close->get('pid');
        if ($id <= 0) {
            throw new Exception('Invalid pid, can not upload');
        }

        // Проверяем наличие файла на диске
        $picture = $Close->get('picture');
        $imagePath = MODX_BASE_PATH.$picture;
        if (!file_exists($imagePath)) {
            $Close->set('status', idImageClose::STATUS_FAILED);

            return null;
        }

        if ($check) {
            if ($Close->change($imagePath)) {
                return null;
            }
        }

        return $imagePath;
    }

    public function upload(idImageClose $Close, $check = false)
    {
        if (!$imagePath = $this->checkup($Close, $check)) {
            return false;
        }
        try {
            // Готовим изображение
            $this->idImage->makeThumbnail($imagePath, function ($path) use ($Close) {
                // Отправляем изображения в сервис
                $response = $this->idImage->client()->upload((string)$Close->pid, $path)->send();
                $this->fromResult($Close, $response);
            });
        } catch (Exception $e) {
            $Close->set('status', idImageClose::STATUS_FAILED);
        }

        return $Close->save();
    }


    function isLocalUrl($url)
    {
        $parsedUrl = parse_url($url);

        if (!$parsedUrl || !isset($parsedUrl['host'])) {
            return false; // Некорректный URL
        }

        $host = strtolower($parsedUrl['host']);

        // Проверяем, является ли хост локальным
        if ($host === 'localhost' || $host === '127.0.0.1') {
            return true;
        }

        return false;
    }


    public function create(int $id, string $imagePath)
    {
        if (!file_exists($imagePath)) {
            return false;
        }

        /* @var idImageClose $Close */
        if (!$Close = $this->idImage->modx->getObject('idImageClose', ['pid' => $id])) {
            $Close = $this->idImage->modx->newObject('idImageClose');
        }

        $Close->set('hash', $Close->createHash($imagePath));
        $Close->set('pid', $id);
        $Close->set('picture', str_ireplace(MODX_BASE_PATH, '', $imagePath));
        $Close->set('status', idImageClose::STATUS_PROCESS);

        return $Close->save();
    }
}
