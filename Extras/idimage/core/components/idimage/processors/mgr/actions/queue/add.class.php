<?php

use IdImage\Entities\EntityClose;

if (!class_exists('idImageActionsProcessor')) {
    include_once __DIR__.'/../actions.class.php';
}

class idImageQueueAddProcessor extends idImageActionsProcessor implements \IdImage\Interfaces\ActionProgressBar
{

    public function stepChunk()
    {
        return 50;
    }

    public function withProgressIds()
    {
        return $this->query()->closes()->where([
            'status:!=' => idImageClose::STATUS_PROCESSING,
            'OR:status:=' => idImageClose::STATUS_QUEUE,
        ])->ids();
    }

    /**
     * @return array|string
     */
    public function process()
    {
        return $this->withProgressBar(function (array $ids) {
            $total = 0;

            $Operation = $this->idImage->operation();


            $Offers = new \IdImage\Offers();


            $closes = null;
            $this->query()
                ->closes()
                ->where(['id:IN' => $ids])
                ->each(function (idImageClose $close) use (&$total, $Operation, &$closes, $Offers) {
                    // add close
                    $closes[$close->get('pid')] = $close;

                    // create entity
                    $EntityClose = new EntityClose();
                    if (!$url = $close->url()) {
                        $EntityClose->setError([
                            'url' => 'url not found',
                        ]);
                    }
                    //$url = 'https://platon.site/assets/images/products/27173/0cd44484e38711efb180e89c25dff007-0cd44485e38711efb180e89c25dff007.jpg';

                    $EntityClose
                        ->setOfferId($close->offerId())
                        ->setPicture($url);

                    // tags
                    $tags = (!empty($close->get('tags')) && is_array($close->get('tags'))) ? $close->get('tags') : [];
                    if (!empty($tags)) {
                        $EntityClose->setTags($tags);
                    }

                    $Offers->addOffer($EntityClose, idImageClose::STATUS_INVALID);
                });


            // Проверка наличия офферов

            // send queue
            $Operation->addQueue($Offers, function (EntityClose $entity) use (&$closes) {
                /* @var idImageClose $Close */
                $Close = $closes[$entity->getOfferId()];

                $received = $entity->getReceived();

                // Ставим метку о доставке
                $Close->set('status_code', $entity->getStatusCode());
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

return 'idImageQueueAddProcessor';
