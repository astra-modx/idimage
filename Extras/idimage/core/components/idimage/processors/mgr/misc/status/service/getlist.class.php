<?php


class idimageActiveGetListProcessor extends modObjectProcessor
{
    public $languageTopics = array('idimage:manager');

    /** {@inheritDoc} */
    public function process()
    {
        $statuses = idImageClose::$statusServiceMap;

        $array = [];
        foreach ($statuses as $key => $alias) {
            $array[] = [
                'name' => $alias,
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
                    'name' => $this->modx->lexicon('idimage_all_statuses_services'),
                    'value' => '',
                ),
            ), $array);
        }

        return parent::outputArray($array, $count);
    }

}

return 'idimageActiveGetListProcessor';
