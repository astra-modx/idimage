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
        $Indexed = $idImage->actions()->indexed()->item()->entity();
        dd($Indexed);

        dd($Indexed->active());

        dd($Catalog->toArray());

        /* @var idImageIndexed $Indexed */
        $Indexed = $this->modx->getObject('idImageIndexed', 7);
        $Entity = $Indexed->entity()->fromArray($Indexed->toArray());

        $path = MODX_CORE_PATH.'cache/idimage/versions/';


        try {
            $data = $Entity->downloader()->read($path);

            dd(count($data));
        } catch (Exception $e) {
            dd($e->getMessage());
        }


        dd($data);


        /*  $Entity = $Indexed->entity()
              ->setDownloadLink($Indexed->get('download_link'))
              ->setVersion($Indexed->get('version'))
              ->setRun($Indexed->get('run'))
              ->setLaunch($Indexed->get('launch'))
              ->setCompleted($Indexed->get('completed'))
              ->setUpload($Indexed->get('upload'))
              ->setSize($Indexed->get('size'))
              ->setImages($Indexed->get('images'))
              ->setCloses($Indexed->get('closes'))
              ->setSealed($Indexed->get('sealed'))
              ->setStartAt($Indexed->get('start_at'))
              ->setFinishedAt($Indexed->get('finished_at'))
              ->setUploadAt($Indexed->get('upload_at'));*/


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
