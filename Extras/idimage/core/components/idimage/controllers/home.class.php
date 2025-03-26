<?php

/**
 * The home manager controller for idimage.
 *
 */
class idimageHomeManagerController extends modExtraManagerController
{
    /** @var idimage $idimage */
    public $idimage;


    /**
     *
     */
    public function initialize()
    {
        $this->idimage = $this->modx->getService('idimage', 'idimage', MODX_CORE_PATH.'components/idimage/model/');
        parent::initialize();
    }


    /**
     * @return array
     */
    public function getLanguageTopics()
    {
        return ['idimage:manager', 'idimage:default', 'idimage:help', 'idimage:navbar', 'idimage:tabs', 'idimage:actions', 'idimage:filters'];
    }


    /**
     * @return bool
     */
    public function checkPermissions()
    {
        return true;
    }


    /**
     * @return null|string
     */
    public function getPageTitle()
    {
        return $this->modx->lexicon('idimage');
    }


    /**
     * @return void
     */
    public function loadCustomCssJs()
    {
        $this->idimage->loadVersion();
        $this->idimage->addCss('mgr/main.css');
        $this->idimage->addCss('mgr/help.css');
        $this->idimage->addJavascript('mgr/idimage.js');
        $this->idimage->addJavascript('mgr/misc/utils.js');
        $this->idimage->addJavascript('mgr/misc/combo.js');
        $this->idimage->addJavascript('mgr/misc/category.tree.js');
        $this->idimage->addJavascript('mgr/misc/default.cyclic.js');
        $this->idimage->addJavascript('mgr/misc/default.grid.js');
        $this->idimage->addJavascript('mgr/misc/default.window.js');
        $this->idimage->addJavascript('mgr/widgets/closes/grid.js');
        $this->idimage->addJavascript('mgr/widgets/closes/windows.js');
        $this->idimage->addJavascript('mgr/widgets/tasks/stat.js');
        $this->idimage->addJavascript('mgr/widgets/tasks/grid.js');
        $this->idimage->addJavascript('mgr/widgets/tasks/windows.js');
        $this->idimage->addJavascript('mgr/widgets/settings/form.js');
        $this->idimage->addJavascript('mgr/widgets/navbar.js');
        $this->idimage->addJavascript('mgr/widgets/home.panel.js');
        $this->idimage->addJavascript('mgr/sections/home.js');

        $this->idimage->addJavascript(MODX_MANAGER_URL.'assets/modext/util/datetime.js');

        $this->idimage->config['date_format'] = $this->modx->getOption('idimage_date_format', null, '%d.%m.%y <span class="gray">%H:%M</span>');
        $this->idimage->config['status_map'] = $this->idimage->statusMap();
        $this->idimage->config['status_map_task'] = $this->idimage->statusMapTask();

        // Stat
        $Stat = new \IdImage\Stat($this->idimage);
        $this->idimage->config['stat'] = $Stat->process()->tpl();
        $this->idimage->config['context'] = 'web';
        $this->idimage->config['actions'] = $this->actionsController();
        $this->idimage->config['operationsMap'] = \IdImage\Sender::$operationsMap;
        $this->idimage->config['task_status_map'] = idImageTask::$statusMap;
        $this->idimage->config['settings'] = $this->settings();


        $this->addHtml(
            '<script type="text/javascript">
        idimage.config = '.json_encode($this->idimage->config).';
        idimage.config.connector_url = "'.$this->idimage->config['connectorUrl'].'";
        Ext.onReady(function() {MODx.load({ xtype: "idimage-page-home"});});
        </script>'
        );
    }

    public function settings()
    {
        $values = $this->idimage->settingKeys();

        $prefix = 'idimage_';
        /* @var modSystemSetting $object */
        $q = $this->modx->newQuery('modSystemSetting');
        $q->where(array(
            'key:IN' => array_map(function ($key) use ($prefix) {
                return $prefix.$key;
            }, array_keys($values)),
        ));
        if ($objectList = $this->modx->getCollection('modSystemSetting', $q)) {
            foreach ($objectList as $object) {
                $key = str_ireplace($prefix, '', $object->get('key'));
                $values[$key] = $object->get('value');
            }
        }

        return $values;
    }


    public function actionsController()
    {
        return [
            'product_creation' => 'mgr/actions/product/creation',
        ];
    }


    /**
     * @return string
     */
    public function getTemplateFile()
    {
        $this->content .= '<div id="idimage-panel-home-div"></div>';

        return '';
    }


}
