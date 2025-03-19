<?php
/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 09.03.2025
 * Time: 12:42
 */

namespace IdImage\Ai;


use Exception;
use InvalidArgumentException;

class CosineSimilarity
{
    private int $minimumProbabilityScore = 70;
    private int $maximumProductsFound;
    private CollectionProduct $products;

    public function __construct(CollectionProduct $products, int $minimumProbabilityScore = 70, int $maximumProductsFound = 50)
    {
        $this->products = $products;
        $this->minimumProbabilityScore = $minimumProbabilityScore;
        $this->maximumProductsFound = $maximumProductsFound;
    }

    public function minimumProbabilityScore()
    {
        return $this->minimumProbabilityScore;
    }

    public function maximumProductsFound()
    {
        return $this->maximumProductsFound;
    }

    protected $total = 0;

    public function total()
    {
        return $this->total;
    }

    public function collection(Similar $similar): Similar
    {
        $searchPid = $similar->getPid();
        $vectorA = $similar->getEmbedding();

        $percentage = [];
        $compared = 0;

        foreach ($this->products->all() as $pid => $vectorB) {
            // offset for current item
            if ($pid == $searchPid) {
                continue;
            }
            if (!is_array($vectorB)) {
                throw new Exception("Вектор должен быть массивом pid: {$pid}");
            }

            if (count($vectorB) != 512) {
                throw new Exception("Вектор должен быть длиной 512 pid: {$pid}");
            }

            $score = $this->compare($vectorA, $vectorB);
            $probability = $this->parserPercentage($score);
            if ($probability >= $this->minimumProbabilityScore()) {
                $percentage[] = [
                    'offer_id' => $pid,
                    'probability' => $probability,
                ];
            }
            $compared++;
        }

        // sort by probability
        usort($percentage, function ($a, $b) {
            return $b['probability'] <=> $a['probability'];
        });


        // Получаем только первые 50 элемент
        if (count($percentage) > $this->maximumProductsFound()) {
            $percentage = array_slice($percentage, 0, $this->maximumProductsFound());
        }

        $similar->setCompared($compared);
        $similar->setData($percentage);

        return $similar;
    }

    function parserPercentage(float $value)
    {
        return round($value * 100, 2);
    }

    public function compare(array $vectorA, array $vectorB): float
    {
        $dotProduct = 0;
        $normA = 0;
        $normB = 0;

        $length = count($vectorA);
        if ($length !== count($vectorB)) {
            throw new InvalidArgumentException("Векторы должны быть одной длины");
        }

        for ($i = 0; $i < $length; $i++) {
            $dotProduct += $vectorA[$i] * $vectorB[$i];
            $normA += $vectorA[$i] ** 2;
            $normB += $vectorB[$i] ** 2;
        }

        $normA = sqrt($normA);
        $normB = sqrt($normB);

        if ($normA == 0 || $normB == 0) {
            throw new InvalidArgumentException("Один из векторов нулевой");
        }

        return $dotProduct / ($normA * $normB);
    }
}
