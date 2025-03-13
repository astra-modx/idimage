<?php


class idimageActiveGetListProcessor extends modObjectProcessor
{
    public $languageTopics = array('idimage:manager');

    /** {@inheritDoc} */
    public function process()
    {
        $statuses = idImageTask::$statusMap;

        $array = [];
        foreach ($statuses as $key) {
            $array[] = [
                'name' => $key,
                'value' => $key,
            ];
        }

        return $this->outputArray($array);
    }

    /** {@inheritDoc} */
    public function outputArray(array $array, $count = false)
    {
        if ($this->getProperty('addall')) {
            $array = array_merge_recursive(array(
                array(
                    'name' => $this->modx->lexicon('idimage_all_statuses'),
                    'value' => '',
                ),
            ), $array);
        }

        return parent::outputArray($array, $count);
    }

}

return 'idimageActiveGetListProcessor';
