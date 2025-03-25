<?php


class idimageActiveGetListProcessor extends modObjectProcessor
{
    public $languageTopics = array('idimage:manager');

    /** {@inheritDoc} */
    public function process()
    {
        $actions = \IdImage\Indexer::$typesMap;
        $array = [];

        foreach ($actions as $k) {
            $array[] = array(
                'name' => $this->modx->lexicon('idimage_type_'.$k),
                'value' => $k,
            );
        }
        $query = $this->getProperty('query');
        if (!empty($query)) {
            foreach ($array as $k => $format) {
                if (stripos($format['name'], $query) === false) {
                    unset($array[$k]);
                }
            }
            sort($array);
        }

        return $this->outputArray($array);
    }

    /** {@inheritDoc} */
    public function outputArray(array $array, $count = false)
    {
        return parent::outputArray($array, $count);
    }

}

return 'idimageActiveGetListProcessor';
