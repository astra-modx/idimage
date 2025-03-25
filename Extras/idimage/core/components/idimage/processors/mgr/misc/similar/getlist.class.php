<?php


class idimageActiveGetListProcessor extends modObjectProcessor
{
    public $languageTopics = array('idimage:manager');

    /** {@inheritDoc} */
    public function process()
    {
        $array = array(
            0 => array(
                'name' => $this->modx->lexicon('idimage_similar'),
                'value' => '1',
            ),
            1 => array(
                'name' => $this->modx->lexicon('idimage_insimilar'),
                'value' => '0',
            ),
        );

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
        if ($this->getProperty('addall')) {
            $array = array_merge_recursive(array(
                array(
                    'name' => $this->modx->lexicon('idimage_similar_all'),
                    'value' => '',
                ),
            ), $array);
        }

        return parent::outputArray($array, $count);
    }

}

return 'idimageActiveGetListProcessor';
