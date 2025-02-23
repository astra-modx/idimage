<?php

use IdImage\Entities\EntityClose;

if (!class_exists('idImageActionsProcessor')) {
    include_once __DIR__.'/actions.class.php';
}

class idImageUploadProcessor extends idImageActionsProcessor implements \IdImage\Interfaces\ActionProgressBar
{
    public function stepChunk()
    {
        return 5;
    }

    public function withProgressIds()
    {
        return $this->query()->closes()->where([
            'status' => idImageClose::STATUS_UPLOAD,
        ])->ids();
    }

    /**
     * @return array|string
     */
    public function process()
    {
        return $this->withProgressBar(function (array $ids) {
            $PhpThumb = new \IdImage\Helpers\PhpThumb($this->modx);
            $this->query()
                ->closes()
                ->where(['id:IN' => $ids])
                ->each(function (idImageClose $close) use ($PhpThumb) {
                    $ImagePath = $close->picturePath();

                    // Загружаем картинку
                    $PhpThumb->makeThumbnail($ImagePath, function ($pathTmp) use ($close) {
                        $Response = $this->idImage->operation()->upload($close->offerId(), $pathTmp);

                        $status = idImageClose::STATUS_QUEUE;
                        $errors = null;
                        if ($Response->isOk()) {
                            $data = $Response->json();
                            if (!empty($data['url'])) {
                                $close->set('picture_cloud', $data['url']);
                            }
                        } else {
                            $status = idImageClose::STATUS_FAILED;
                            $errors = $Response->json();
                        }

                        $close->set('errors', $errors);
                        $close->set('status', $status);
                        $close->set('status_code', $Response->getStatus());

                        $close->save();
                    });

                    $this->pt();
                });

            sleep(1);

            return $this->total();
        });
    }

}

return 'idImageUploadProcessor';
