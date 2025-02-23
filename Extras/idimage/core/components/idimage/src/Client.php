<?php

namespace IdImage;

use CURLFile;
use Exception;
use IdImage\Helpers\Response;
use modX;

/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 19.02.2025
 * Time: 23:48
 */
class Client
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
        $this->apiUrl = $modx->getOption('idimage_api_url', null, null);
        if (empty($this->token)) {
            throw new Exception('Token not set, setting idimage_token');
        }
        if (empty($this->apiUrl)) {
            throw new Exception('apiUrl not set, setting idimage_api_url');
        }
    }


    public function get(string $url, $params = null)
    {
        return $this->setMethod('get')->setData($params)->setUrl($url);
    }

    public function post(string $url, $params = null)
    {
        return $this->setMethod('post')
            ->setData($params)
            ->setUrl($url)
            ->setHeaders([
                'Content-Type: application/json',
            ]);
    }

    public function delete(string $url, $params = null)
    {
        return $this->setMethod('delete')
            ->setHeaders([
                'Content-Type: application/json',
            ])
            ->setData($params)
            ->setUrl($url);
    }

    public function upload(string $offerId, $imagePath)
    {
        return $this
            ->setUrl('images/service/upload')
            ->setHeaders([
                'Accept: application/json',
            ])
            ->setData([
                'offer_id' => $offerId,
                'image' => new CURLFile($imagePath, 'image/jpeg', basename($imagePath)),
            ]);
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
            if (!empty($postData)) {
                $url = $url.'?'.http_build_query($postData);
            }
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


        switch ($method) {
            case 'post':
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
                break;
            case 'delete':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
                if (!empty($postData)) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
                }
                break;
            default:
                break;
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

        return new Response($status, $response, $error);
    }

    protected function setData($data = null)
    {
        if (is_array($data)) {
            $this->data = $data;
        }

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
        $this->url = rtrim($this->apiUrl, '/').'/'.ltrim($url, '/');

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
