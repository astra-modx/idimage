<?php

include_once dirname(__FILE__).'/update.class.php';

class idImageCloseStatusPollProcessor extends idImageCloseUpdateProcessor
{
    public function beforeSet()
    {
        $pid = $this->object->pid;


        $Response = $this->idImage->client()->statusPollOffer($pid)->send();

        if (!$Response->isOk()) {
            return $this->failure($Response->getMsg());
        }


        $items = $this->idImage->handler()->extractorItems($Response);

        if (!isset($items[$pid])) {
            return $this->failure("No items for pid $pid");
        }

        $offer = $items[$pid];

        $success = $this->idImage->operation()->statusPoll($this->object, $offer);
        if (!$success) {
            return $this->failure("No success for pid $pid");
        }

        return true;
    }
}

return 'idImageCloseStatusPollProcessor';
