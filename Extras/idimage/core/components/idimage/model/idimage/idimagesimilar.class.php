<?php

class idImageSimilar extends xPDOSimpleObject
{

    public function getProducts()
    {
        $products = [];
        $similar = $this->get('data');
        if (!empty($similar) && is_array($similar)) {
            $ids = array_column($similar, 'offer_id');
            $rows = null;

            $thumbnailSize = $this->xpdo->getOption('ms2_product_thumbnail_size', null, 'small');

            $q = $this->xpdo->newQuery('msProduct');
            $q->select('msProduct.id, File.url as url');
            $q->where(array(
                'msProduct.published' => true,
                'msProduct.id:IN' => $ids,
                'File.active' => true,
                'File.path:LIKE' => '%/'.$thumbnailSize.'/%',
            ));
            $q->innerJoin('msProductFile', 'File', 'File.product_id = msProduct.id');
            if ($q->prepare() && $q->stmt->execute()) {
                while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                    $rows[(int)$row['id']] = $row['url'];
                }
            }

            foreach ($similar as $id => $item) {
                $offer_id = (int)$item['offer_id'];
                if (!isset($rows[$offer_id])) {
                    continue;
                }
                $image = $rows[$offer_id];

                $products[] = [
                    'pid' => $offer_id,
                    'image' => $image,
                    'probability' => $item['probability'],
                ];
            }

            // Сортируем массив по вероятности
            usort($products, function ($a, $b) {
                return ($b['probability'] > $a['probability']) ? 1 : (($b['probability'] < $a['probability']) ? -1 : 0);
            });
        }

        return $products;
    }
}
