<?php

/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 19.02.2025
 * Time: 23:48
 */

if (!class_exists('idImageResponse')) {
    include_once MODX_CORE_PATH.'components/idimage/model/idimageresponse.class.php';
}

class idImageClient
{
    /* @var null|array $data */
    private $data = null;

    /* @var string $token */
    private $token;

    /* @var string $apiUrl */
    private $apiUrl;

    /* @var string $method */
    private $url;

    private $headers = [];
    private $method = 'post';

    public function __construct(modX $modx)
    {
        $this->token = $modx->getOption('idimage_token', null, null);
        if (empty($this->token)) {
            throw new Exception('Token not set, setting idimage_token');
        }
        $this->apiUrl = $modx->getOption('idimage_api_url', null, null);
        if (empty($this->apiUrl)) {
            throw new Exception('apiUrl not set, setting idimage_api_url');
        }
    }

    public function offer(int $offerId, string $picture)
    {
        return $this->setUrl('images')
            ->setHeaders([
                'Content-Type: application/json',
            ])
            ->setData([
                'items' => [
                    [
                        'picture' => $picture,
                        'offer_id' => (string)$offerId,
                    ],
                ],
            ]);
    }

    public function upload(int $offerId, string $imagePath)
    {
        $size = @getimagesize($imagePath);

        if ($size[0] !== 224 || $size[1] !== 224) {
            throw new Exception('Неверный размер изображения, должно быть 224х224');
        }

        if ($size['mime'] !== 'image/jpeg') {
            throw new Exception('Неверный формат изображения, должно быть jpeg');
        }


        return $this->setUrl('images/service/upload')
            ->setHeaders([
                'Accept: application/json',
            ])
            ->setData([
                'offer_id' => $offerId,
                'image' => new CURLFile($imagePath, 'image/jpeg', basename($imagePath)),
            ]);
    }

    public function statusPoll(int $limit = 10)
    {
        return $this->get('images')->setData(['limit' => $limit]);
    }

    public function statusPollOffer(int $offerId)
    {
        return $this->get("images")->setData([
            'offers' => [
                $offerId,
            ],
        ]);
    }

    public function reindex()
    {
        return $this->setUrl("images/service/reindex");
    }

    public function upVersion()
    {
        return $this->setUrl("images/service/upVersion");
    }

    public function get(string $url)
    {
        return $this->setMethod('get')->setUrl($url);
    }

    public function toArray()
    {
        return [
            'data' => $this->getData(),
            'url' => $this->getUrl(),
            'headers' => $this->getHeaders(),
        ];
    }

    public function send()
    {
        $postData = $this->getData();

        $url = $this->getUrl();
        $headers = $this->getHeaders();

        $headers = array_merge($headers, [
            'Authorization: Bearer '.$this->token,
        ]);


        $upload = false;
        foreach ($headers as $key => $value) {
            if ($value === 'Accept: application/json') {
                $upload = true;
            }
        }


        $method = $this->getMethod();

        if ($method === 'get') {
            $url = $url.'?'.http_build_query($postData);
            $postData = null;
        } else {
            if (!$upload) {
                $postData = json_encode($postData);
            }
        }


        // Инициализируем cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if ($method === 'post') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        }

        // Выполняем запрос
        $response = curl_exec($ch);

        // Проверяем на ошибки
        $error = null;
        if (curl_errno($ch)) {
            $error = 'Ошибка cURL: '.curl_error($ch);
        }

        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        // Закрываем соединение
        curl_close($ch);

        return new idimageResponse($status, $response, $error);
    }

    protected function setData(array $data)
    {
        $this->data = $data;

        return $this;
    }

    protected function setHeaders(array $headers)
    {
        $this->headers = $headers;

        return $this;
    }

    public function getHeaders()
    {
        return $this->headers;
    }


    public function getData()
    {
        return $this->data;
    }

    public function getUrl()
    {
        return $this->url;
    }

    protected function setUrl(string $url)
    {
        $this->url = $this->apiUrl.'/'.ltrim($url, '/');

        return $this;
    }

    private function setMethod(string $method)
    {
        $this->method = $method;

        return $this;
    }

    public function getMethod()
    {
        return $this->method;
    }


}
