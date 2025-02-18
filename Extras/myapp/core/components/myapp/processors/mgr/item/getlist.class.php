<?php

class myappItemGetListProcessor extends modObjectGetListProcessor
{
    public $objectType = 'myappItem';
    public $classKey = 'myappItem';
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'DESC';
    public $languageTopics = ['myapp:manager'];
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
     * @param xPDOQuery $c
     *
     * @return xPDOQuery
     */
    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        $query = trim($this->getProperty('query'));
        if ($query) {
            $c->where([
                'name:LIKE' => "%{$query}%",
                'OR:description:LIKE' => "%{$query}%",
            ]);
        }
        $active = $this->getProperty('active');
        if ($active != '') {
            $c->where("{$this->objectType}.active={$active}");
        }
        $resource = trim($this->getProperty('resource'));
        if (!empty($resource)) {
            $c->where("{$this->objectType}.resource_id={$resource}");
        }
        return $c;
    }


    /**
     * @param xPDOObject $object
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
            'title' => $this->modx->lexicon('myapp_'.$lexicon_key.'_update'),
            'action' => 'update'.$lexicon_key_action,
            'button' => true,
            'menu' => true,
        ];

        if (!$array['active']) {
            $array['actions'][] = [
                'cls' => '',
                'icon' => 'icon icon-power-off action-green',
                'title' => $this->modx->lexicon('myapp_'.$lexicon_key.'_enable'),
                'multiple' => $this->modx->lexicon('myapp_'.$lexicon_key.'s_enable'),
                'action' => 'enableItem',
                'button' => true,
                'menu' => true,
            ];
        } else {
            $array['actions'][] = [
                'cls' => '',
                'icon' => 'icon icon-power-off action-gray',
                'title' => $this->modx->lexicon('myapp_'.$lexicon_key.'_disable'),
                'multiple' => $this->modx->lexicon('myapp_'.$lexicon_key.'s_disable'),
                'action' => 'disable'.$lexicon_key_action,
                'button' => true,
                'menu' => true,
            ];
        }

        // Remove
        $array['actions'][] = [
            'cls' => '',
            'icon' => 'icon icon-trash-o action-red',
            'title' => $this->modx->lexicon('myapp_'.$lexicon_key.'_remove'),
            'multiple' => $this->modx->lexicon('myapp_'.$lexicon_key.'s_remove'),
            'action' => 'remove'.$lexicon_key_action,
            'button' => true,
            'menu' => true,
        ];
        return $array;
    }
}

return 'myappItemGetListProcessor';