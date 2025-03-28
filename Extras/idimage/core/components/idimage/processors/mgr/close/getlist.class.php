<?php

class idImageCloseGetListProcessor extends modObjectGetListProcessor
{
    public $objectType = 'idImageClose';
    public $classKey = 'idImageClose';
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'DESC';
    public $languageTopics = ['idimage:manager', 'idimage:actions'];
    //public $permission = 'list';
    private idImage $idImage;

    public function initialize()
    {
        $this->idImage = $this->modx->getService('idimage', 'idImage', MODX_CORE_PATH.'components/idimage/model/');

        return parent::initialize(); // TODO: Change the autogenerated stub
    }

    /**
     * We do a special check of permissions
     * because our objects is not an instances of modAccessibleObject
     *
     * @return boolean|string
     */
    public function beforeQuery()
    {
        if (!$this->checkPermissions()) {
            return $this->modx->lexicon('access_denied');
        }

        return true;
    }


    /**
     * @param  xPDOQuery  $c
     *
     * @return xPDOQuery
     */
    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        $c->select($this->modx->getSelectColumns('idImageClose', 'idImageClose'));

        $query = trim($this->getProperty('query'));
        if ($query) {
            $c->where([
                'idImageClose.picture:LIKE' => "%{$query}%",
                'OR:idImageClose.hash:LIKE' => "%{$query}%",
                'OR:msProduct.pagetitle:LIKE' => "%{$query}%",
            ]);
            $id = (int)$query;
            if ($id > 0) {
                $c->where([
                    'OR:idImageClose.id:=' => $id,
                    'OR:idImageClose.pid:=' => $id,
                ]);
            }
        }

        $received = $this->getProperty('received');
        if ($received != '') {
            $c->where("{$this->objectType}.received={$received}");
        }

        $similar = $this->getProperty('similar');
        if ($similar != '') {
            if ($similar == '1') {
                $c->where("Similar.total!=0");
            } else {
                $c->where("Similar.total=0");
            }
        }

        $active = $this->getProperty('active');
        if ($active != '') {
            if ($active == '1') {
                $c->where("{$this->objectType}.active=1");
            } else {
                $c->where("{$this->objectType}.active=0");
            }
        }

        $pid = trim($this->getProperty('pid'));
        if (!empty($pid)) {
            $c->where("{$this->objectType}.pid={$pid}");
        }

        $status = trim($this->getProperty('status'));
        if (!empty($status)) {
            $c->where("{$this->objectType}.status={$status}");
        }

        $c->leftJoin('msProduct', 'msProduct', 'msProduct.id=idImageClose.pid');
        $c->select('msProduct.pagetitle AS pagetitle');

        $c->leftJoin('idImageSimilar', 'Similar', 'Similar.pid=idImageClose.pid');
        $c->select('COUNT(Similar.id) AS similar_exists, Similar.total AS total');

        $c->leftJoin('idImageEmbedding', 'Embedding', 'Embedding.hash=idImageClose.hash');
        $c->select('COUNT(Embedding.id) AS embedding_exists');

        $c->groupBy('idImageClose.id');

        return $c;
    }


    /**
     * @param  xPDOObject  $object
     *
     * @return array
     */
    public function prepareRow(xPDOObject $object)
    {
        /* @var idImageClose $object */
        $array = $object->toArray();
        unset($array['similar']);

        $array['picture_thumb'] = $object->existsThumbnail() ? $object->getTargetUrl() : null;
        $array['similar_exists'] = (bool)$array['similar_exists'];
        $array['embedding_exists'] = (bool)$array['embedding_exists'];

        $extractor = $this->idImage->extractor();
        if ($similar = $object->similar()) {
            $extractor->load($similar)->best();
        }
        $array['images'] = $extractor->slice();

        $array['exists_thumbnail'] = $object->existsThumbnail();

        $IndexedAction = new \IdImage\Support\IndexedAction($object, basename(__DIR__));
        $actions = $IndexedAction->getList(function (\IdImage\Support\IndexedAction $action) use ($object) {
            $upload = $object->get('upload');
            $embedding_exists = $object->get('embedding_exists');
            $similar_exists = $object->get('similar_exists');

            if ($object->existsThumbnail()) {
                $button = (!$upload && !$embedding_exists);
                $action->add('upload', 'icon icon-upload', $button, true, null, true);
            }

            if ($object->get('embedding_exists')) {
                $button = empty($object->get('similar_exists'));
                $action->add('indexed', 'icon icon-refresh', $button, true, null, true);
            }

            $action->add('check', 'icon icon-refresh', false, true, null, true);

            if (!$object->existsThumbnail()) {
                $action->add('thumbnailCreate', 'icon-camera action-green');
            } else {
                $action->add('thumbnailRemove', 'icon icon-trash-o', false);
            }

            if ($similar_exists) {
                $action->add('similarRemove', 'icon-trash-o', false);
            }

            if ($embedding_exists) {
                $action->add('embeddingRemove', 'icon-trash-o', false);
            }

            $action->add('remove', 'icon icon-trash-o action-red', false);
        });
        $array['actions'] = $actions;

        return $array;
    }
}

return 'idImageCloseGetListProcessor';
