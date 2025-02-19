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


    public function uploads($callback = null, int $limit = 60)
    {
        $total = 0;
        $q = $this->idImage->modx->newQuery('idImageClose');
        $q->where(array(
            'status' => idImageClose::STATUS_PROCESS,
            'active' => 1,
        ));
        $q->limit($limit);
        if ($objectList = $this->idImage->modx->getCollection('idImageClose', $q)) {
            foreach ($objectList as $object) {
                $total++;
                $success = $this->upload($object);
                if ($callback) {
                    $callback($total, $success);
                }
            }
        }

        return $total;
    }

    public function statusPoll($callback = null, int $limit = 1000)
    {
        $Response = $this->idImage->client()->statusPoll($limit)->send();

        if (!$Response->isOk()) {
            throw new Exception($Response->getMsg());
        }

        $data = $Response->json();

        $items = [];

        $statusMap = [
            'completed' => idImageClose::STATUS_DONE,
        ];

        if (!empty($data['items'])) {
            foreach ($data['items'] as $item) {
                $status = idImageClose::STATUS_UNKNOWN;
                if (isset($statusMap[$item['status']])) {
                    $status = $statusMap[$item['status']];
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


        $total = 0;

        if (!empty($items)) {
            /* @var idImageClose $Close */
            $q = $this->idImage->modx->newQuery('idImageClose');
            $q->where(array(
                'pid:IN' => array_keys($items),
            ));
            if ($objectList = $this->idImage->modx->getCollection('idImageClose', $q)) {
                foreach ($objectList as $object) {
                    $item = $items[$object->get('pid')] ?? null;
                    $object->set('status', $item['status']);
                    $object->set('version', $item['version']);
                    $object->set('total_close', $item['total_close']);
                    $object->set('min_scope', $item['min_scope']);
                    $object->set('closes', $item['closes']);
                    $object->save();
                    $total++;
                }
            }
        }

        return $total;
    }

    public function query()
    {
        return $this->idImage->modx->newQuery('idImageClose')->where([
            'active' => 1,
        ]);
    }

    public function collection($query, $callback = null)
    {
        $objectList = $this->idImage->modx->getCollection('idImageClose', $query);
        foreach ($objectList as $item) {
            $callback($item);
        }
    }

    public function collection2()
    {
        $total = 0;
        $q = $this->idImage->modx->newQuery('idImageClose');
        if ($objectList = $this->idImage->modx->getCollection('idImageClose', $q)) {
            foreach ($objectList as $object) {
                $total++;
                $success = $this->upload($object);
                if ($callback) {
                    $callback($total, $success);
                }
            }
        }
    }

    public function upload(idImageClose $Close)
    {
        $id = $Close->get('pid');
        if ($id <= 0) {
            throw new Exception('Invalid pid, can not upload');
        }

        // Проверяем наличие файла на диске
        $imagePath = MODX_BASE_PATH.$Close->get('picture');
        if (!file_exists($imagePath)) {
            return false;
        }


        // Готовим изображение
        $this->idImage->makeThumbnail($imagePath, function ($path) use ($id, $Close) {
            // Отправляем изображения в сервис
            $response = $this->idImage->client()->upload($id, $path)->send();
            // Записываем ответ

            $status = $response->isOk() ? idImageClose::STATUS_BUILD : idImageClose::STATUS_ERROR;

            $Close->set('status', $status);
            $Close->set('status_code', $response->getStatus());

            // Ставим метку о доставке
            $Close->set('received', $response->isOk());

            // Пишем дату доставки
            $Close->set('received_at', time());
        });

        return $Close->save();
    }

    public function create(int $id, string $imagePath)
    {
        /* @var idImageClose $Close */
        if (!$Close = $this->idImage->modx->getObject('idImageClose', ['pid' => $id])) {
            $Close = $this->idImage->modx->newObject('idImageClose');
        }

        $hash = $Close->createHash($imagePath);
        $Close->set('pid', $id);
        $Close->set('hash', $hash);
        $Close->set('picture', str_ireplace(MODX_BASE_PATH, '', $imagePath));
        $Close->set('status', idImageClose::STATUS_PROCESS);

        return $Close->save();
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
                $success = $this->create((int)$row['id'], MODX_BASE_PATH.ltrim($row['image'], '/'));
                if (is_callable($callback)) {
                    $callback($total, $success);
                }
            }
        }

        return $total;
    }
}
