<?php

if (!class_exists('idImageActionsProcessor')) {
    include_once __DIR__.'/../../actions.class.php';
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
            $created = 0;
            $updated = 0;
            $created_thumbnail = 0;
            $task_upload = 0;

            // Выбираем все товары с изображениями
            $files = $this->query()->filesCriteria()->where(['id:IN' => $ids]);
            $files->collection(function (array $row) use (&$created, &$updated, &$created_thumbnail, &$task_upload) {
                // путь до изображения
                $imagePath = MODX_BASE_PATH.ltrim($row['image'], '/');

                if (!file_exists($imagePath)) {
                    // Пропускаем если отсутствуете файлы
                    return false;
                }

                $pid = (int)$row['id'];
                $picture = str_ireplace(MODX_BASE_PATH, '', $imagePath);

                /* @var idImageClose $Close */
                if (!$Close = $this->idImage->modx->getObject('idImageClose', ['pid' => $pid])) {
                    $Close = $this->idImage->modx->newObject('idImageClose');
                    $Close->set('pid', $pid);
                    $Close->set('status', idImageClose::STATUS_QUEUE);
                }

                $hash = $Close->createHash($imagePath);

                // Создаем новый превью при условии
                $Close->set('picture', $picture);
                $Close->set('hash', $hash);

                // Проверка наличия векторов
                // Если вектора есть то ставим метку что изобаржение загружено
                if ($Close->isNew()) {
                    $created++;
                } else {
                    if ($Close->isDirty('hash')) {
                        $updated++;
                    }
                }



                if (!$Close->save()) {
                    throw new \IdImage\Exceptions\ExceptionJsonModx('Failed to save Close object: '.$pid);
                } else {
                    // generate image thumbnail
                    if (!$Close->existsThumbnail()) {
                        $Close->generateThumbnail();
                        $created_thumbnail++;
                    }
                }

                // После сохранения
                if ($Close->isCreateTaskUpload()) {
                    $task_upload++;
                }

                return true;
            });

            $this->setStat([
                'created' => $created,
                'updated' => $updated,
                'task_upload' => $task_upload,
                'created_thumbnail' => $created_thumbnail,
            ]);

            return $files->totalIteration();
        });
    }

}

return 'idImageProductCreationProcessor';
