<?php

if (!class_exists('idImageActionsProcessor')) {
    include_once __DIR__.'/../../../actions.class.php';
}

class idImageProductCreationProcessor extends idImageActionsProcessor implements \IdImage\Interfaces\ActionProgressBar
{
    public function stepChunk()
    {
        return $this->idImage->limitCreation();
    }

    public function fromCategories($query)
    {
        $categories = $this->getProperty('categories');
        if (!empty($categories)) {
            $ctx = 'web';
            #$contexts = array_map('trim', explode(',',  'web'));
            $categories = $this->modx->fromJSON($categories);
            /* @var $pdoFetch pdoFetch */
            $pdoFetch = $this->modx->getService('pdoFetch');

            $products = [];
            foreach ($categories as $category) {
                $ids = $pdoFetch->getChildIds('msCategory', $category, 10, array('context' => $ctx));
                $products = array_merge($products, $ids);
            }
            $products = array_map('intval', $products);
            $products = array_filter(array_unique($products));
            $query->where([
                'msProduct.id:IN' => $products,
            ]);
        }
    }

    public function withProgressIds()
    {
        $query = $this->query()->filesCriteria();
        $this->fromCategories($query);
        $ids = $query->ids('msProduct.id as id');

        return $ids;
    }

    /**
     * @return array|string
     */
    public function process()
    {
        return $this->withProgressBar(function (array $ids) {
            $files = $this->query()->files()->where(['id:IN' => $ids]);
            $files->collection(function (array $row) {
                // путь до изображения
                $imagePath = MODX_BASE_PATH.ltrim($row['image'], '/');
                $pid = (int)$row['id'];
                $picture = str_ireplace(MODX_BASE_PATH, '', $imagePath);

                /* @var idImageClose $Close */
                if (!$Close = $this->idImage->modx->getObject('idImageClose', ['pid' => $pid])) {
                    $Close = $this->idImage->modx->newObject('idImageClose');
                    $Close->set('pid', $pid);
                }
                $status = idImageClose::STATUS_QUEUE;
                $errors = null;
                if (!file_exists($imagePath)) {
                    $status = idImageClose::STATUS_FAILED;
                    $errors = [
                        'file not found' => $picture,
                    ];
                }

                $Close->set('errors', $errors);
                $Close->set('hash', $Close->createHash($imagePath));
                $Close->set('picture', $picture);
                $Close->set('status', $status);

                if (!$Close->save()) {
                    throw new \IdImage\Exceptions\ExceptionJsonModx('Failed to save Close object: '.$pid);
                }
                $this->pt();
            });


            return $this->total();
        });
    }

}

return 'idImageProductCreationProcessor';
