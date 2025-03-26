<?php

namespace IdImage\Support;

use idImage;
use IdImage\Sender;
use idImageClose;
use idImageTask;
use msOption;
use msProduct;
use PDO;

/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 19.02.2025
 * Time: 23:48
 */
class Query
{
    private idImage $idImage;

    public function __construct(idImage $idImage)
    {
        $this->idImage = $idImage;
    }

    public function create($class)
    {
        return new xPDOQueryIdImage($this->idImage->modx, $class);
    }

    public function files()
    {
        $query = $this->create('msProduct');
        $query->select('File.id as file_id,File.product_id as id, File.url as image, File.path as path, File.hash as hash');
        $query->select('msProduct.published as published, msProduct.deleted as deleted');
        //$query->leftJoin('idImageClose', 'Close', 'Close.pid = msProductFile.product_id');
        $query->innerJoin('msProductFile', 'File', 'File.product_id = msProduct.id');

        return $query;
    }

    public function filesCriteria()
    {
        $thumbnailSize = $this->idImage->modx->getOption('ms2_product_thumbnail_size', null, 'small');

        return $this->files()->where([
            'msProduct.published' => true,
            'msProduct.deleted:!=' => true,
            'File.rank' => 0,
            'File.active' => true,
            'File.path:LIKE' => '%/'.$thumbnailSize.'/%',
        ]);
    }


    public function closes()
    {
        return $this->create('idImageClose');
    }

    public function closesEmbedding()
    {
        return $this->closes()->innerJoin('idImageEmbedding', 'Embedding', 'Embedding.hash = idImageClose.hash');
    }

    public function similar()
    {
        return $this->create('idImageSimilar');
    }

    public function tasks()
    {
        return $this->create('idImageTask');
    }

    public function tasksExecuteAt()
    {
        return $this->tasks()->where([
            'execute_at:>=' => time(),
            'AND:execute_at:!=' => null,
        ]);
    }

    public function tasksQueue(string $operation)
    {
        $map = Sender::$operationsMap;
        if (!in_array($operation, $map)) {
            throw new \Exception('Invalid operation: '.$operation);
        }

        $query = $this->tasks()
            ->where([
                'operation' => $operation,
                'status:IN' => [
                    idImageTask::STATUS_PENDING,
                    idImageTask::STATUS_QUEUE,
                    idImageTask::STATUS_RETRY, // Повторны статус по кол-ву ошибок
                ],
            ])
            ->andCondition(array(
                'execute_at:<=' => time(), // Только если время исполнения настало
                'OR:execute_at:=' => null, // Только если время исполнения настало
            ));

        return $query;
    }

    public function embeddings()
    {
        return $this->create('idImageEmbedding')->select($this->idImage->modx->getSelectColumns('idImageEmbedding', 'idImageEmbedding', ''));
    }


    /**
     * Вернет кол-во товаров доступных для поиска
     * @return mixed
     */
    public function productIndexedCount()
    {
        $query = $this->create('idImageClose');
        $query->innerJoin('msProduct', 'Product', 'Product.id = idImageClose.pid');
        $query->innerJoin('idImageEmbedding', 'Embedding', 'Embedding.hash = idImageClose.hash');

        return $query->count();
    }


    public function indexeds()
    {
        return $this->create('idImageIndexed');
    }

    public function closesCriteria()
    {
        return $this->closes()->where([
            'active' => 1,
        ]);
    }


    /**
     * Вернет категории по ключу idimage
     * @return null|array
     */
    public function optionCategories(): ?array
    {
        $categories = null;
        /* @var msOption $option */
        if ($option = $this->idImage->modx->getObject('msOption', ['key' => 'idimage'])) {
            $q = $this->idImage->modx->newQuery('msCategoryOption');
            $q->select('category_id');
            $q->where(array(
                'option_id' => $option->id,
            ));
            if ($q->prepare() && $q->stmt->execute()) {
                while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                    $categories[] = (int)$row['category_id'];
                }
            }
        }

        return $categories;
    }


    /**
     * @param  int  $id
     * @param $product_id
     * @return idImageClose|object|string|null
     */
    public function getCloseOrCreate(int $id, $product_id = null)
    {
        /* @var idImageClose $Close */
        if (!$Close = $this->idImage->modx->getObject('idImageClose', $id)) {
            try {
                if (!$product_id) {
                    return $this->idImage->modx->lexicon('idimage_error_close_not_found');
                }

                $response = $this->idImage->runProcessor('mgr/actions/product/creation', [
                    'ids' => [$product_id],
                ]);
                if ($response->isError()) {
                    return $response->getMessage();
                }
                if (!$Close = $this->idImage->modx->getObject('idImageClose', ['pid' => $product_id])) {
                    return $this->idImage->modx->lexicon('idimage_error_close_not_found');
                }
            } catch (\Exception $e) {
                return $e->getMessage();
            }
        }


        /* @var msProduct $product */
        if ($product = $this->idImage->modx->getObject('msProduct', $product_id)) {
            $active = ($product->get('published') && !$product->get('deleted'));
            if ($Close->get('active') !== $active) {
                $Close->set('active', $active);
                $Close->save();
            }
        }

        return $Close;
    }
}
