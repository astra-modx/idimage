<?php
/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 22.03.2025
 * Time: 15:53
 */

namespace IdImage;

use Exception;
use idImage;
use IdImage\Ai\Similar;
use IdImage\Interfaces\IndexedTypeInterfaces;
use IdImage\Support\Response;
use idImageClose;
use idImageTask;

class Indexer
{
    public idImage $idImage;
    private ?IndexedTypeInterfaces $indexer = null;

    const TYPE_INDEX_ALL = 'index_all';
    const TYPE_INDEX_BY_CATEGORY = 'index_by_category';
    const TYPE_INDEX_FIRST_LEVEL_CATEGORY = 'index_first_level_category';
    private Similar $similar;

    public static $typesMap = [
        self::TYPE_INDEX_ALL,
        self::TYPE_INDEX_BY_CATEGORY,
        self::TYPE_INDEX_FIRST_LEVEL_CATEGORY,
    ];

    public string $type = self::TYPE_INDEX_ALL;

    public function __construct(idImage $idImage)
    {
        $this->similar = new Similar(
            $idImage->maximumProductsFound(),
            $idImage->minimumProbabilityScore()
        );

        $this->type = $idImage->option('indexed_type');
    }

    public function indexerTypeDefault(): IndexedTypeInterfaces
    {
        return $this->indexers($this->type);
    }

    /**
     * Сравнение векторов
     * @param  idImage  $idImage
     * @param  IndexedTypeInterfaces  $IndexedType
     * @param  Similar  $similar
     * @param  array  $offerIds
     * @return array
     */
    public static function comparison(idImage $idImage, IndexedTypeInterfaces $IndexedType, Similar $similar, array $offerIds): ?array
    {
        // Создание коллекции для индексации по векторам
        $productIndexer = $IndexedType->process($idImage);

        if ($productIndexer->isEmpty()) {
            return null;
        }


        $statuses = \idImageTask::$statusMap;
        $results = null;
        foreach ($offerIds as $offerId) {
            $similarData = null;
            $errors = null;
            $status = idImageTask::STATUS_COMPLETED;
            if ($data = $productIndexer->embedding($offerId)) {
                $embedding = $data['embedding'];
                $parent = $data['parent'];

                // Записываем данные для сравнения
                $similar->create($offerId, $parent, $embedding);

                ###############
                #### сравниваем коллекцию
                try {
                    $similarData = $IndexedType->comparison($similar, $productIndexer)->toArray();
                } catch (Exception $e) {
                    $errors = $e->getMessage();
                }
            } else {
                $status = idImageTask::STATUS_FAILED;
                $errors = 'Product not found';
            }

            if (!empty($errors)) {
                $errors = is_array($errors) ? $errors : [$errors];
                $status = idImageClose::STATUS_FAILED;
            }


            $results[] = [
                'status' => $statuses[$status] ?? idImageTask::STATUS_FAILED,
                'offer_id' => $similar->getOfferId(),
                'errors' => $errors,
                'similar' => $similarData,
            ];
        }

        return $results;
    }

    public function response(array $results): Response
    {
        $response = new Response(200, '');

        $response->setDecoded([
            'items' => $results,
        ]);

        return $response;
    }

    public function indexers(string $name): IndexedTypeInterfaces
    {
        if (!is_null($this->indexer)) {
            return $this->indexer;
        }
        switch ($name) {
            case self::TYPE_INDEX_ALL:
                $this->indexer = new \IdImage\Ai\Types\IndexAll();
                break;
            case self::TYPE_INDEX_BY_CATEGORY:
                $this->indexer = new \IdImage\Ai\Types\IndexByParentCategory();
                break;
            case  self::TYPE_INDEX_FIRST_LEVEL_CATEGORY:
                $this->indexer = new \IdImage\Ai\Types\IndexFirstLevelCategory();
                break;
            default:
                throw new \InvalidArgumentException('Unknown indexer name: '.$name);
        }

        return $this->indexer;
    }

    // Индексация всего каталога (все товары)
    public function indexAll()
    {
        return $this->indexers(self::TYPE_INDEX_ALL);
    }

    // Индексация по родительской категории

    public function indexByParentCategory()
    {
        return $this->indexers(self::TYPE_INDEX_BY_CATEGORY);
    }

    // Индексация товаров первого уровня
    public function indexFirstLevelCategory()
    {
        return $this->indexers(self::TYPE_INDEX_FIRST_LEVEL_CATEGORY);
    }

    public function similar()
    {
        return $this->similar;
    }
}
