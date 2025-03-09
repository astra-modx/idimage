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
    protected $embedding = null;
    protected $modify = null;
    private CosineSimilarity $cosineSimilarity;
    private Similar $similar;

    public function __construct(idImage $idImage, int $minimumProbabilityScore = 70, int $maximumProductsFound = 50)
    {
        $this->idImage = $idImage;
        $this->cosineSimilarity = new CosineSimilarity($minimumProbabilityScore, $maximumProductsFound);
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

    public function getSimilar(int $pid, array $embedding, array $items): Similar
    {
        if (count($embedding) !== 512) {
            throw new Exception("Вектор должен быть массивом");
        }
        $similar = $this->cosineSimilarity->collection($pid, $embedding, $items);
        $this->similar->fromArray($similar);

        return $this->similar;
    }


    public function getEmbedding()
    {
        return $this->embedding;
    }

    public function loadEmbedding()
    {
        if (is_null($this->embedding)) {
            $query = $this->query();

            if ($this->modify) {
                call_user_func($this->modify, $query);
            }

            $items = $query->toArray();

            $items = array_map(function ($item) {
                return [
                    'pid' => $item['pid'],
                    'embedding' => json_decode($item['embedding'], true),
                ];
            }, $items);

            $items = array_filter($items, function ($item) {
                return (is_array($item['embedding']) && count($item['embedding']) === 512);
            });

            $this->embedding = $items;
        }
    }

}
