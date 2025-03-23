<?php
/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 09.03.2025
 * Time: 12:42
 */

namespace IdImage\Ai;

use InvalidArgumentException;

class ComparisonCosineSimilarity
{
    public function compareSimilar(Similar $similar, array $products)
    {
        $results = [];
        $compared = 0;
        $searchOfferId = $similar->getOfferId();
        $vectorA = $similar->getEmbedding();
        $maximumFound = $similar->maximumFound();
        $minimumScore = $similar->minimumScore();

        foreach ($products as $offerId => $data) {
            if ($offerId == $searchOfferId) {
                continue; // Сам себя не сравнив
            }
            $vectorB = $data['embedding'];

            // Получаем векторное сравнение
            $score = $this->compare($vectorA, $vectorB);

            // Получаем процентное сравнение
            $probability = $this->parserPercentage($score);

            // Если процентное сравнение больше минимального то добавляем в список
            if ($probability >= $minimumScore) {
                $results[] = [
                    'offer_id' => $offerId,
                    'probability' => $probability,
                ];
            }
            $compared++;
        }

        // Сортируем по проценту, вектора с большим процентом вверху
        usort($results, function ($a, $b) {
            return $b['probability'] <=> $a['probability'];
        });


        // Получаем только первые 50 элемент
        if (count($results) > $maximumFound) {
            $results = array_slice($results, 0, $maximumFound);
        }


        $probability = array_column($results, 'probability');
        $total = count($results);
        $min_value = $results ? min($probability) : 0;
        $max_value = $results ? max($probability) : 0;


        $similar->setCompared($compared);
        $similar->setData($results);
        $similar->setMinValue($min_value);
        $similar->setMaxValue($max_value);
        $similar->setTotal($total);

        return $this;
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
            throw new InvalidArgumentException("Векторы должны быть одной длины 512");
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
