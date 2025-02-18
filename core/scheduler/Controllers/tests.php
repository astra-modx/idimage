<?php

/**
 * Демонстрация контроллера
 */
class CrontabControllerTests extends modCrontabController
{
    public function process()
    {
        $idImage = $this->modx->getService('idimage', 'idimage', MODX_CORE_PATH.'components/idimage/model/');


        $siteUrl = $this->modx->getOption('site_url');

        /* @var msProduct $object */
        $q = $this->modx->newQuery('msProduct');
        $q->where(array(
            'class_key' => 'msProduct',
        ));
        if ($objectList = $this->modx->getCollection('msProduct', $q)) {
            foreach ($objectList as $object) {
                $Parent = $object->getOne('Parent');
                $Data = $object->getOne('Data');

                $pagetitle = $Parent->get('pagetitle');
                $tags = [
                    $pagetitle,
                ];
                $thumb = $siteUrl.''.ltrim($Data->thumb, '/');

                /* @var idimageItem $Item */
                $Item = $this->modx->newObject('idimageItem');

                $Item->set('picture', $thumb);
                $Item->set('resource_id', $object->id);
                $Item->set('tags', $tags);
                $Item->save();
            }
        }
    }
}
