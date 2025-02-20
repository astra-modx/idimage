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


    public function isOk(): bool
    {
        return ($this->status === 200 || $this->status === 204);
    }

    public function isFail(): bool
    {
        return !$this->isOk();
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getMsg(): ?string
    {
        return $this->msg;
    }

    public function json()
    {
        $content = $this->getContent();
        if (substr($content, 0, 1) === '{') {
            return json_decode($this->getContent(), 1) ?? null;
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
