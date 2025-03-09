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

    private int $total;
    private array $similar;
    private int $status;
    /**
     * @var int|mixed
     */
    private $min_value;
    /**
     * @var int|mixed
     */
    private $max_value;

    public function fromArray(array $similar)
    {
        $this->total = count($similar);
        $this->similar = $similar;
        $this->status = $similar ? IdImageClose::STATUS_COMPLETED : IdImageClose::STATUS_NOT_FOUND_SIMILAR;

        $probability = array_column($similar, 'probability');
        $this->min_value = $similar ? min($probability) : 0;
        $this->max_value = $similar ? max($probability) : 0;

        return $this;
    }


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

    public function status()
    {
        return $this->status;
    }

    public function getSimilar(): array
    {
        return $this->similar;
    }

    public function toArray()
    {
        return [
            'min_value' => $this->min_value,
            'max_value' => $this->max_value,
            'total' => $this->total,
            'status' => $this->status,
            'similar' => $this->similar,
        ];
    }
}
