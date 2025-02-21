<?php
/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 19.02.2025
 * Time: 23:48
 */

class idImageHander
{
    /* @var idImage $idImage */
    private $idImage;

    public function __construct(idImage $idImage)
    {
        $this->idImage = $idImage;
        $this->idImage->modx->loadClass('idImageClose');
    }

    public function query($active = true)
    {
        $query = $this->idImage->query();
        if (!is_bool($active)) {
            $query->where([
                'active' => 1,
            ]);
        }

        return $query;
    }

    public function queryStatusProgress()
    {
        return $this->query()->where([
            'status' => idImageClose::STATUS_PROCESSING,
        ]);
    }

    public function queryStatusBuild()
    {
        return $this->query()->where([
            'status' => idImageClose::STATUS_PROCESSING,
        ]);
    }

    public function uploads($callback = null, int $limit = 60)
    {
        $total = 0;
        $this->queryStatusProgress()->limit($limit)->each(function (idImageClose $close) use (&$total, $callback) {
            $total++;
            $success = $this->idImage->operation()->upload($close);
            if ($callback) {
                $callback($total, $success);
            }
        });

        return $total;
    }

    public function statusPoll(array $OfferIds)
    {
        $Response = $this->idImage->client()->statusPoll($OfferIds)->send();
        if (!$Response->isOk()) {
            throw new Exception($Response->getMsg());
        }

        return $Response;
    }

    public function lastVersion()
    {
        $Response = $this->idImage->client()->lastVersion()->send();
        if (!$Response->isOk()) {
            throw $this->exception($Response);
        }
        $data = $Response->json();
        if (empty($data['closes_url'])) {
            return null;
        }
        $content = file_get_contents($data['closes_url']);
        $data = json_decode($content, true);

        if (!is_array($data)) {
            return null;
        }
        if (empty($data['closes']) || !is_array($data['closes'])) {
            return null;
        }

        return $data['closes'];
    }

    public function exception($Response)
    {
        $msg = "[STATUS: ".$Response->getStatus()."] msg: ".$Response->getMsg();

        return new Exception($msg);
    }

    public function extractorItems(array $closes)
    {
        $items = [];

        if (count($closes) > 0) {
            $statusMap = $this->idImage->statusMap();
            foreach ($closes as $item) {
                $status = $item['status'];
                if (!isset($statusMap[$status])) {
                    $status = idImageClose::STATUS_UNKNOWN;
                }

                $id = (int)$item['offer_id'];
                $closes = null;
                if (!empty($item['closes'])) {
                    foreach ($item['closes'] as $offer) {
                        $closes[$offer['offer_id']] = $offer['probability'];
                    }
                }
                $items[$id] = [
                    'status' => $status,
                    'total_close' => $item['total_close'] ?? 0,
                    'min_scope' => $item['min_scope'] ?? 0,
                    'closes' => $closes,
                ];
            }
        }

        return $items;
    }

    public function bulk($callback = null)
    {
        // default small change setting
        $cropSize = $this->idImage->modx->getOption('idimage_crop_size', null, 'small');

        $total = 0;
        $q = $this->idImage->modx->newQuery('msProductFile');
        $q->select('msProductFile.product_id as id, msProductFile.url as image, msProductFile.path as path');
        $q->leftJoin('idImageClose', 'Close', 'Close.pid = msProductFile.product_id');
        $q->innerJoin('msProduct', 'msProduct', 'msProduct.id = msProductFile.product_id');
        $q->where([
            'msProduct.published' => true,
            'msProduct.deleted:!=' => true,
            'Close.pid:IS' => null,
        ]);
        if ($q->prepare() && $q->stmt->execute()) {
            while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                // Get all image is path not indexed field mysql
                if (strripos($row['path'], '/'.$cropSize.'/') !== false) {
                    $total++;
                    $success = $this->idImage->operation()->create((int)$row['id'], MODX_BASE_PATH.ltrim($row['image'], '/'));
                    if (is_callable($callback)) {
                        $callback($total, $success);
                    }
                }
            }
        }

        return $total;
    }

    public function bulkProduct($callback = null)
    {
        $total = 0;
        $q = $this->idImage->modx->newQuery('msProduct');
        $q->select('msProduct.id as id, Data.image as image');
        $q->innerJoin('msProductData', 'Data', 'Data.id = msProduct.id');
        $q->leftJoin('idImageClose', 'Close', 'Close.pid = msProduct.id');
        $q->where([
            'Data.image:!=' => '',
            'Close.pid:IS' => null,
        ]);
        if ($q->prepare() && $q->stmt->execute()) {
            while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                $total++;
                $success = $this->idImage->operation()->create((int)$row['id'], MODX_BASE_PATH.ltrim($row['image'], '/'));
                if (is_callable($callback)) {
                    $callback($total, $success);
                }
            }
        }

        return $total;
    }
}
