<?php
/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 09.03.2025
 * Time: 12:42
 */

namespace IdImage\Ai;


use Closure;
use Exception;
use idImage;
use idImageClose;
use InvalidArgumentException;

class CollectionProduct
{
    private idImage $idImage;
    protected ?array $items = null;
    protected $modify = null;
    private CosineSimilarity $cosineSimilarity;
    private Similar $similar;

    public function __construct(idImage $idImage, int $minimumProbabilityScore = 70, int $maximumProductsFound = 50)
    {
        $this->idImage = $idImage;
        $this->cosineSimilarity = new CosineSimilarity($this, $minimumProbabilityScore, $maximumProductsFound);
        $this->similar = new Similar();
    }

    public function query()
    {
        return $this->idImage->query()->embeddings();
    }

    public function modifyQuery(Closure $closure)
    {
        $this->modify = $closure;

        return $this;
    }

    public function compare(int $pid, array $embedding): Similar
    {
        $similar = $this->similar;
        $similar->setPid($pid);
        $similar->setEmbedding($embedding);
        $this->cosineSimilarity->collection($similar);

        return $similar;
    }


    public function all()
    {
        return $this->items;
    }


    public function count()
    {
        return is_array($this->items) ? count($this->items) : 0;
    }

    // is not emoty
    public function isEmpty()
    {
        return $this->count() === 0;
    }

    public function isNotEmpty()
    {
        return !$this->isEmpty();
    }


    public function get(int $pid)
    {
        $this->loadEmbedding();

        if (!isset($this->items[$pid])) {
            return null;
        }

        return $this->items[$pid];
    }

    public function loadEmbedding()
    {
        if (is_null($this->items)) {
            $this->items = [];
            $query = $this->query();
            if ($this->modify) {
                call_user_func($this->modify, $query);
            }
            $query->collection(function ($item) use (&$items) {
                $embedding = json_decode($item['data'], true);
                if (is_array($embedding) && count($embedding) === 512) {
                    $this->items[$item['pid']] = $embedding;
                }
            });
        }
    }

}
