<?php

if (!class_exists('idImageActionsProcessor')) {
    include_once __DIR__.'/actions.class.php';
}

class idImageReIndexProcessor extends idImageActionsProcessor
{
    /**
     * @return array|string
     */
    public function process()
    {
        $Response = $this->idImage->client()->reindex()->send();
        if (!$Response->isOk()) {
            return $this->failure($Response->getMsg());
        }

        $data = $Response->json();

        if (empty($data['success'])) {
            return $this->failure("Can't reindex");
        }

        return $this->success('');
    }

}

return 'idImageReIndexProcessor';
