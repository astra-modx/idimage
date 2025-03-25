<?php


if (!class_exists('idImageActionsProcessor')) {
    include_once __DIR__.'/../../../actions.class.php';
}

class idImageApiTaskStatProcessor extends idImageActionsProcessor
{
    public function process()
    {
        $this->canToken();
        $Response = $this->idimage()->api()->task()->stat()->send();
        if (!$Response->isOk()) {
            $Response->exception();
        }
        $data = $Response->json();

        return $this->success($this->modx->lexicon('success'), $data);
    }

}

return 'idImageApiTaskStatProcessor';
