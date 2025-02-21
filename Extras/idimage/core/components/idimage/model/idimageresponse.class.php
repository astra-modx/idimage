<?php
/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 19.02.2025
 * Time: 23:48
 */

class idImageResponse
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
        return !empty($this->msg) ? $this->msg : '';
    }

    public function json()
    {
        if ($content = $this->getContent()) {
            if (substr($content, 0, 1) === '{') {
                return json_decode($this->getContent(), 1) ?? null;
            }
        }

        return null;
    }

    public function toArray()
    {
        return [
            'status' => $this->getStatus(),
            'msg' => $this->getMsg(),
            'data' => $this->json() ?? $this->getContent(),
        ];
    }
}
