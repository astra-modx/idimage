<?php

class idImageTaskGetListProcessor extends modObjectGetListProcessor
{
    public $objectType = 'idImageTask';
    public $classKey = 'idImageTask';
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
        $c->select($this->modx->getSelectColumns('idImageTask', 'idImageTask'));

        $query = trim($this->getProperty('query'));
        if ($query) {
            $c->where([
                'idImageTask.task_id:LIKE' => "%{$query}%",
                'OR:idImageTask.etag:LIKE' => "%{$query}%",
            ]);
        }

        $status = trim($this->getProperty('status'));
        if (!empty($status)) {
            $c->where("{$this->objectType}.status='{$status}'");
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
        /* @var idImageTask $object */
        $array = $object->toArray();

        $errors = $object->getErrors();


        $array['error'] = !empty($errors['msg']) ? $errors['msg'] : null;


        $IndexedAction = new \IdImage\Support\IndexedAction($object, basename(__DIR__));
        $actions = $IndexedAction->getList(function (\IdImage\Support\IndexedAction $action) use ($object) {
            //$action->add('update', 'icon-edit');

            if ($object->attemptsExceeded()) {
                $action->add('resetAttempts', 'icon-repeat');
            }

            if ($object->isAllowedSend()) {
                $action->add('received', 'icon icon-send action-green', true, true, null, true);
            }

            if ($object->isAllowedPoll()) {
                $action->add('poll', 'icon icon-download action-green', false, true, null, true);
            }

            if (!$object->get('image_available')) {
                $action->add('upload', 'icon icon-download action-green', false, true, null, true);
            }


            $action->add('remove', 'icon icon-trash-o action-red', false, true, null, true);
        });
        $array['actions'] = $actions;

        return $array;
    }
}

return 'idImageTaskGetListProcessor';
