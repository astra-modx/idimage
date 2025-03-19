<?php
/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 09.03.2025
 * Time: 12:42
 */

namespace IdImage\Ai;


use Closure;
use idImage;
use idImageClose;
use InvalidArgumentException;

class Similar
{

    private int $total = 0;
    private ?array $data = null;
    private int $status;
    /**
     * @var int|mixed
     */
    private $min_value;
    /**
     * @var int|mixed
     */
    private $max_value;
    private int $compared = 0;
    private ?int $pid = null;
    private ?array $embedding = null;


    public function minValue()
    {
        return $this->min_value;
    }

    public function maxValue()
    {
        return $this->max_value;
    }


    public function total()
    {
        return $this->total;
    }


    public function toArray()
    {
        return [
            'min_value' => $this->min_value,
            'max_value' => $this->max_value,
            'total' => $this->total,
            'data' => $this->similar,
            'compared' => $this->compared,
        ];
    }

    public function setCompared(int $total)
    {
        $this->compared = $total;

        return $this;
    }

    public function compared()
    {
        return $this->compared;
    }

    public function status()
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function setPid(int $pid)
    {
        $this->pid = $pid;

        return $this;
    }

    public function getPid()
    {
        return $this->pid;
    }

    public function setEmbedding(array $embedding)
    {
        $this->embedding = $embedding;

        return $this;
    }

    public function getEmbedding(): array
    {
        return $this->embedding;
    }

    public function setData(array $percentage)
    {
        $this->total = count($percentage);
        $this->data = $percentage;
        $probability = array_column($percentage, 'probability');
        $this->min_value = $percentage ? min($probability) : 0;
        $this->max_value = $percentage ? max($probability) : 0;

        return $this;
    }

    public function data(): ?array
    {
        return $this->data;
    }

    public function setMinValue(int $value)
    {
        $this->min_value = $value;

        return $this;
    }

    public function setMaxValue(int $value)
    {
        $this->max_value = $value;

        return $this;
    }

}
