<?php

if (!class_exists('idImageActionsProcessor')) {
    include_once __DIR__.'/../../../actions.class.php';
}

class idImageActionsImageUploadCloudProcessor extends idImageActionsProcessor implements \IdImage\Interfaces\ActionProgressBar
{
    public function stepChunk()
    {
        return 5;
    }

    public function withProgressIds()
    {
        return $this->query()->closes()->where([
            'upload' => false,
            'OR:upload_link:=' => null,
        ])->ids();
    }

    /**
     * @return array|string
     */
    public function process()
    {
        if ($this->getProperty('steps')) {
            // Проверка только на первом этапе
            if (!$this->idImage->isCloudUpload()) {
                return $this->failure($this->modx->lexicon('idimage_cloud_upload_disabled'));
            }

            // Получаем информацию о каталоге
            $Entity = $this->idImage->api()->indexed()->entity();

            // Проверяем наличие каталога
            if (!$Entity->active()) {
                return $this->failure($this->modx->lexicon('idimage_catalog_disabled'));
            }
            if (!$Entity->uploadApi()) {
                return $this->failure($this->modx->lexicon('idimage_cloud_upload_disabled'));
            }
        }

        return $this->withProgressBar(function (array $ids) {
            $PhpThumb = new \IdImage\Support\PhpThumb($this->modx);
            $this->query()
                ->closes()
                ->where(['id:IN' => $ids])
                ->each(function (idImageClose $close) use ($PhpThumb) {
                    $ImagePath = $close->picturePath();

                    // Загружаем картинку
                    $PhpThumb->makeThumbnail($ImagePath, function ($pathTmp) use ($close) {
                        $Response = $this->idImage->api()->queue()->upload($close->offerId(), $pathTmp)->send();

                        $status = idImageClose::STATUS_QUEUE;
                        $errors = null;
                        if ($Response->isOk()) {
                            $data = $Response->json();
                            if (!empty($data['url'])) {
                                $close->set('upload', true);
                                $close->set('upload_link', $data['url']);
                            }
                        } else {
                            $status = idImageClose::STATUS_FAILED;
                            $errors = $Response->json();
                        }

                        $close->set('errors', $errors);
                        $close->set('status', $status);

                        $close->save();
                    });

                    $this->pt();
                });

            sleep(1);

            return $this->total();
        });
    }

}

return 'idImageActionsImageUploadCloudProcessor';
