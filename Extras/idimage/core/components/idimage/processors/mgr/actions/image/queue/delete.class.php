<?php

use IdImage\Api\Entities\EntityClose;

if (!class_exists('idImageActionsProcessor')) {
    include_once __DIR__.'/../../../actions.class.php';
}

class idImageActionsImageDeleteProcessor extends idImageActionsProcessor implements \IdImage\Interfaces\ActionProgressBar
{

    public function stepChunk()
    {
        return 1000;
    }

    public function withProgressIds()
    {
        return $this->query()->closes()
            /*  ->where([
                  'received' => true, // любые предложения что доставлялись
              ])*/
            ->ids();
    }

    /**
     * @return array|string
     */
    public function process()
    {
        return $this->withProgressBar(function (array $ids) {
            $Operation = $this->idImage->api()->queue();
            $closes = null;

            $Offers = new \IdImage\Support\Offers();

            $this->query()
                ->closes()
                ->where(['id:IN' => $ids])
                ->each(function (idImageClose $close) use (&$closes, $Offers) {
                    // Create Entity
                    $EntityClose = new EntityClose();
                    $EntityClose->delete();
                    $EntityClose->setOfferId($close->offerId())->delete();

                    // Add offer
                    $closes[$close->offerId()] = $close;

                    $Offers->addOffer($EntityClose, idImageClose::STATUS_INVALID);

                    return true;
                });


            // send service
            $Operation->delete($Offers, function (EntityClose $entity) use (&$closes) {
                // Get close
                $Close = $closes[$entity->getOfferId()];

                $Close->set('status', idImageClose::STATUS_DELETED);
                $Close->set('received', $entity->getReceived()); // Ставим метку о доставке
                $Close->set('received_at', time());

                // Пишем дату доставки
                $errors = null;
                if (!$entity->isError()) {
                    $errors = $entity->getError();
                }
                $Close->set('errors', $errors);

                $Close->save();
            });


            sleep(1); // otherwise it bloDcks due to a large number of requests

            return count($closes);
        });
    }

}

return 'idImageActionsImageDeleteProcessor';
