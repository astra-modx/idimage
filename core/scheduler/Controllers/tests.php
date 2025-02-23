<?php

/**
 * Демонстрация контроллера
 */
class CrontabControllerTests extends modCrontabController
{


    public function process()
    {
        /* @var idImage $idImage */
        $idImage = $this->modx->getService('idimage', 'idImage', MODX_CORE_PATH.'components/idimage/model/');


        $version = $idImage->operation()->lastVersion();

        dd($version);

        /*  $response = $idImage->runProcessor('mgr/actions/creation', [
              'steps' => true,
          ]);
  */

        $response = $idImage->runProcessor('mgr/actions/upload', [
            'ids' => [2257, 2258],
        ]);

        dd($response->response);


        $files = $idImage->query()->files();
        dd($files->toArray());


        dd($ids);

        $total = $idImage->handler()->creation($files, function (idImageClose $close) {
            dd($close->toArray());
        });


        dd($files->count());


        $Query = $idImage->handler()->creation();
        dd(22);

        dd($Query->closes()->count());
    }

}
