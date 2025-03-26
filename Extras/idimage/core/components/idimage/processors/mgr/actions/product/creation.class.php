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

    /**
     * Ограничение на выборку товаров через Options
     * @param $query
     * @return void
     */
    public function fromCategories($query)
    {
        if ($categories = $this->query()->optionCategories()) {
            $ctx = 'web';
            #$contexts = array_map('trim', explode(',',  'web'));
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
        $ids = $query->ids();

        // Отключение не действующих товаров

        if ($ids) {
            $this->disableNotActive($ids);
        }

        return $ids;
    }

    public function disableNotActive(array $ids)
    {
        $disableIds = implode(',', $ids);
        $table = $this->modx->getTableName('idImageClose');
        $sql = "UPDATE {$table} SET active = '0' WHERE pid  NOT IN ({$disableIds})";
        $this->modx->exec($sql);
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

            // todo надо как то сравнивать текущие состояние товаров с close, и удалять отключенные товары

            // Выбираем все товары с изображениями
            $files = $this->query()->filesCriteria()->where(['msProduct.id:IN' => $ids]);
            $files->collection(function (array $row) use (&$created, &$updated, &$created_thumbnail, &$task_upload) {
                /* @var idImageClose $Close */

                $published = (bool)$row['published'];
                $deleted = (bool)$row['deleted'];
                $active = ($published && !$deleted);


                $hash = $row['hash'];

                $pid = (int)$row['id'];
                // путь до изображения
                $imagePath = MODX_BASE_PATH.ltrim($row['image'], '/');

                if (!file_exists($imagePath)) {
                    $criteria = [
                        'pid' => $pid,
                        'active' => true,
                    ];
                    if ($Close = $this->idImage->modx->getObject('idImageClose', $criteria)) {
                        $Close->set('active', false);
                        $Close->save();
                    }

                    return false;
                }

                $picture = str_ireplace(MODX_BASE_PATH, '', $imagePath);
                $status = idImageClose::STATUS_COMPLETED;
                if (!$Close = $this->idImage->modx->getObject('idImageClose', ['pid' => $pid])) {
                    $Close = $this->idImage->modx->newObject('idImageClose');
                    $Close->set('pid', $pid);
                }

                $hash = $hash ?? $Close->createHash($imagePath);
                if (strlen($hash) !== 40) {
                    $status = idImageClose::STATUS_FAILED;
                    $Close->setErrors('hash is invalid, '.$pid.' '.$imagePath.' is not 40 chars');
                } else {
                    $Close->set('hash', $hash);
                }

                // Создаем новый превью при условии
                $Close->set('picture', $picture);
                $Close->set('active', $active);
                $Close->set('status', $status);

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
