<?php
/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 19.02.2025
 * Time: 23:48
 */

class idImageHander
{

    private idImage $idImage;

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
            'status' => idImageClose::STATUS_PROCESS,
        ]);
    }

    public function queryStatusBuild()
    {
        return $this->query()->where([
            'status' => idImageClose::STATUS_BUILD,
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

    public function statusPoll(int $limit = 1000)
    {
        $Response = $this->idImage->client()->statusPoll($limit)->send();
        if (!$Response->isOk()) {
            throw new Exception($Response->getMsg());
        }

        return $Response;
    }

    public function extractorItems(idImageResponse $response)
    {
        $data = $response->json();
        $items = [];


        if (!empty($data['items'])) {
            $statusMapComparison = $this->idImage->statusMapComparison();


            foreach ($data['items'] as $item) {
                $status = idImageClose::STATUS_UNKNOWN;
                if (isset($statusMapComparison[$item['status']])) {
                    $status = $statusMapComparison[$item['status']];
                }

                $id = (int)$item['offer_id'];
                $closes = null;
                if (!empty($item['closes'])) {
                    foreach ($item['closes'] as $offer) {
                        $closes[(int)$offer['offer_id']] = $offer['probability'];
                    }
                }
                $items[$id] = [
                    'status' => $status,
                    'version' => $item['version'] ?? 0,
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
