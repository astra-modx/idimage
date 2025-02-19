<?php

class idImageCloseGetListProcessor extends modObjectGetListProcessor
{
    public $objectType = 'idImageClose';
    public $classKey = 'idImageClose';
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'DESC';
    public $languageTopics = ['idimage:manager'];
    //public $permission = 'list';


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
        $query = trim($this->getProperty('query'));
        if ($query) {
            $c->where([
                'picture:LIKE' => "%{$query}%",
                'OR:hash:LIKE' => "%{$query}%",
            ]);
        }

        $received = $this->getProperty('received');
        if ($received != '') {
            $c->where("{$this->objectType}.received={$received}");
        }

        $pid = trim($this->getProperty('pid'));
        if (!empty($pid)) {
            $c->where("{$this->objectType}.pid={$pid}");
        }

        return $c;
    }


    /**
     * @param  xPDOObject  $object
     *
     * @return array
     */
    public function prepareRow(xPDOObject $object)
    {
        $array = $object->toArray();
        $array['actions'] = [];

        $lexicon_key = 'item';
        $lexicon_key_action = 'Item';


        // Edit
        $array['actions'][] = [
            'cls' => '',
            'icon' => 'icon icon-edit',
            'title' => $this->modx->lexicon('idimage_'.$lexicon_key.'_update'),
            'action' => 'update'.$lexicon_key_action,
            'button' => true,
            'menu' => true,
        ];

        if (!$array['active']) {
            $array['actions'][] = [
                'cls' => '',
                'icon' => 'icon icon-power-off action-green',
                'title' => $this->modx->lexicon('idimage_'.$lexicon_key.'_enable'),
                'multiple' => $this->modx->lexicon('idimage_'.$lexicon_key.'s_enable'),
                'action' => 'enableItem',
                'button' => true,
                'menu' => true,
            ];
        } else {
            $array['actions'][] = [
                'cls' => '',
                'icon' => 'icon icon-power-off action-gray',
                'title' => $this->modx->lexicon('idimage_'.$lexicon_key.'_disable'),
                'multiple' => $this->modx->lexicon('idimage_'.$lexicon_key.'s_disable'),
                'action' => 'disable'.$lexicon_key_action,
                'button' => true,
                'menu' => true,
            ];
        }

        // Remove
        $array['actions'][] = [
            'cls' => '',
            'icon' => 'icon icon-trash-o action-red',
            'title' => $this->modx->lexicon('idimage_'.$lexicon_key.'_remove'),
            'multiple' => $this->modx->lexicon('idimage_'.$lexicon_key.'s_remove'),
            'action' => 'remove'.$lexicon_key_action,
            'button' => true,
            'menu' => true,
        ];

        return $array;
    }
}

return 'idImageCloseGetListProcessor';
