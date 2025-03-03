<?php

use IdImage\Exceptions\ExceptionJsonModx;

class idImageIndexed extends xPDOSimpleObject
{
    /** @var idImageVersion $Version */
    protected $Version;

    public function save($cacheFlag = null)
    {
        if (!$this->isNew()) {
            $this->set('updatedon', time());
        } else {
            $this->set('createdon', time());
        }

        return parent::save($cacheFlag);
    }


    /**
     * @return idImageVersion
     */
    public function version()
    {
        return $this->loadData();
    }


    public function toArray($keyPrefix = '', $rawValues = false, $excludeLazy = false, $includeRelated = false)
    {
        $original = parent::toArray($keyPrefix, $rawValues, $excludeLazy, $includeRelated);
        $additional = array_merge(
            $this->loadData()->toArray($keyPrefix, $rawValues, $excludeLazy, $includeRelated),
        );
        $intersect = array_keys(array_intersect_key($original, $additional));
        foreach ($intersect as $key) {
            unset($additional[$key]);
        }

        return array_merge($original, $additional);
    }

    /**
     * @return idImageVersion
     */
    public function loadData()
    {
        if (!is_object($this->Version) || !($this->Version instanceof msProductData)) {
            $q = $this->xpdo->newQuery('idImageVersion');
            $q->where(array(
                'use_version' => true,
                'indexed_id' => $this->get('id'),
            ));
            $q->sortby('id', 'DESC');

            if (!$this->Version = $this->xpdo->getObject('idImageVersion', $q)) {
                $this->Version = $this->xpdo->newObject('idImageVersion');
                $this->Version->set('indexed_id', $this->get('id'));
                parent::addOne($this->Version);
            }


        }

        return $this->Version;
    }

    public function get($k, $format = null, $formatTemplate = null)
    {
        switch ($k) {
            case 'id':
                break;
            default:
                if (isset($this->loadData()->_fields[$k])) {
                    return $this->loadData()->get($k, $format, $formatTemplate);
                }
                break;
        }


        return parent::get($k, $format, $formatTemplate);
    }

    public function api()
    {
        /* @var idImage $idImage */
        $idImage = $this->xpdo->getService('idimage', 'idImage', MODX_CORE_PATH.'components/idimage/model/');

        return $idImage->api()->indexed();
    }

}
