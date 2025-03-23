<?php

class idImageSettingProcessor extends modProcessor
{
    public $languageTopics = ['idimage:manager'];

    /**
     * @return array|string
     */
    public function process()
    {
        /* @var idImage $idImage */
        $idImage = $this->modx->getService('idimage', 'idImage', MODX_CORE_PATH.'components/idimage/model/');


        $values = $idImage->settingKeys();
        /* @var modSystemSetting $setting */

        foreach ($values as $key => $default) {
            $value = $this->getProperty($key);
            $key = 'idimage_'.$key;
            if ($setting = $this->modx->getObject('modSystemSetting', $key)) {
                $setting->set('value', $value);
                $setting->save();
            }
        }


        $this->modx->cacheManager->refresh(array(
            'system_settings' => array(),
            'context_settings' => array(),
            'settings' => array(),
        ));

        return $this->success();
    }


}

return 'idImageSettingProcessor';
