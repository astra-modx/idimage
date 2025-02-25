<?php

namespace IdImage\Helpers;

use Exception;
use IdImage\Entities\EntityCatalog;

/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 19.02.2025
 * Time: 23:48
 */
class Response
{
    private $status;
    private $content;

    /* @var string|null $msg */
    private $msg;

    public function __construct($status, $content, $msg = null)
    {
        $this->status = $status;
        $this->content = $content;
        $this->msg = $msg;
    }


    public function isOk()
    {
        return ($this->status === 200 || $this->status === 204);
    }

    public function isFail()
    {
        return !$this->isOk();
    }

    public function getStatus(): int
    {
        return !empty($this->status) ? $this->status : 0;
    }

    public function getContent()
    {
        return !empty($this->content) ? $this->content : null;
    }

    public function getMsg()
    {
        $msg = !empty($this->msg) ? $this->msg : '';
        if ($this->isFail()) {
            if ($data = $this->json()) {
                if (!empty($data['message'])) {
                    $msg = $data['message'];
                }
            }
        }

        return $msg;
    }

    protected $decoded;

    public function json($key = null, $default = null)
    {
        if (!$this->decoded) {
            if ($content = $this->getContent()) {
                if (substr($content, 0, 1) === '{') {
                    $this->decoded = json_decode($this->getContent(), 1) ?? null;
                }
            }
        }

        if (is_null($this->decoded)) {
            return $default;
        }
        if (is_null($key)) {
            return $this->decoded;
        }

        if (!is_array($this->decoded)) {
            return $default;
        }

        return $this->decoded[$key];
    }

    public function toArray()
    {
        return [
            'status' => $this->getStatus(),
            'msg' => $this->getMsg(),
            'data' => $this->json() ?? $this->getContent(),
        ];
    }


    public function exception()
    {
        $msg = "[STATUS: ".$this->getStatus()."] msg: ".$this->getMsg();

        return new Exception($msg);
    }

    public function items($callback = null)
    {
        $items = $this->json('items');
        if (empty($items) || !is_array($items)) {
            return null;
        }
        if (is_callable($callback)) {
            if (!empty($items) && is_array($items)) {
                foreach ($items as $item) {
                    $callback($item);
                }
            }
        }

        return $items;
    }

    public function entityCatalog()
    {
        if ($this->isFail()) {
            return null;
        }
        $Entity = new EntityCatalog();

        return $Entity->fromArray($this->json());
    }
}
