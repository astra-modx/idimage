<?php

/**
 * Демонстрация контроллера
 */
class CrontabControllerDemo extends modCrontabController
{
    public function process()
    {
        /* @var msProduct $object */
        $q = $this->modx->newQuery('msProduct');
        $q->where(array(
            'class_key' => 'msProduct',
        ));
        if ($objectList = $this->modx->getCollection('msProduct', $q)) {
            foreach ($objectList as $object) {
               dd($object->toArray());

            }
        }
    }
}
