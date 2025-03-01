<?php

use IdImage\Api\Entities\EntityClose;

if (!class_exists('idImageActionsProcessor')) {
    include_once __DIR__.'/../../../actions.class.php';
}

class idImageActionsImageQueueAddProcessor extends idImageActionsProcessor implements \IdImage\Interfaces\ActionProgressBar
{

    public function stepChunk()
    {
        return 1000;
    }

    public function withProgressIds()
    {
        return $this->query()->closes()->where([
            'status:=' => idImageClose::STATUS_QUEUE,
        ])->ids();
    }

    /**
     * @return array|string
     */
    public function process()
    {
        // Check cloud
        if (!$this->idImage->isCloudUpload()) {
            if ($this->idImage->validateSiteUrl() !== true) {
                return $this->failure($this->modx->lexicon('idimage_site_url_invalid', ['url' => $this->idImage->siteUrl()]));
            }
        }

        return $this->withProgressBar(function (array $ids) {
            $total = 0;

            $Operation = $this->idImage->api()->queue();


            $Offers = new \IdImage\Support\Offers();

            $closes = null;
            $this->query()
                ->closes()
                ->where(['id:IN' => $ids])
                ->each(function (idImageClose $close) use (&$total, &$closes, $Offers) {
                    // add close
                    $closes[$close->get('pid')] = $close;

                    // create entity
                    $EntityClose = new EntityClose();


                    # Cloud upload
                    if ($this->idImage->isCloudUpload()) {
                        $url = $close->uploadLink();
                    } else {
                        $url = $close->link($this->idImage->siteUrl());
                    }

                    if (empty($url)) {
                        $EntityClose->setError([
                            'url' => 'url not found',
                        ]);
                    } else {
                        $EntityClose->setPicture($url);
                    }

                    $EntityClose->setOfferId($close->offerId());

                    // tags
                    $tags = (!empty($close->get('tags')) && is_array($close->get('tags'))) ? $close->get('tags') : [];
                    if (!empty($tags)) {
                        $EntityClose->setTags($tags);
                    }

                    $Offers->addOffer($EntityClose, idImageClose::STATUS_INVALID);
                });


            // Проверка наличия офферов

            // send queue
            $Operation->add($Offers, function (EntityClose $entity) use (&$closes) {
                /* @var idImageClose $Close */
                $Close = $closes[$entity->getOfferId()];


                $received = $entity->getReceived();

                // Ставим метку о доставке
                $Close->set('received', $received);
                $Close->set('received_at', time());

                // Пишем дату доставки
                $status = idImageClose::STATUS_PROCESSING;
                $errors = null;

                if ($entity->isError()) {
                    $status = idImageClose::STATUS_FAILED;
                    $errors = $entity->getError();
                }

                $Close->set('status', $status);
                $Close->set('errors', $errors);

                $Close->save();
            });


            sleep(1); // otherwise it bloDcks due to a large number of requests

            return $total;
        });
    }

}

return 'idImageActionsImageQueueAddProcessor';
