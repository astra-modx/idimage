<?php

if (!class_exists('idImageActionsProcessor')) {
    include_once __DIR__.'/../../actions.class.php';
}

class idImageCreationProcessor extends idImageActionsProcessor implements \IdImage\Interfaces\ActionProgressBar
{
    public function stepChunk()
    {
        return 100;
    }

    public function withProgressIds()
    {
        return $this->query()->filesCriteria()->ids('file_id', false);
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
                $Close->save();
                $this->pt();
            });

            return $this->total();
        });
    }

}

return 'idImageCreationProcessor';
