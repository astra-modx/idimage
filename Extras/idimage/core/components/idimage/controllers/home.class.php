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
        return ['idimage:manager', 'idimage:default', 'idimage:help', 'idimage:navbar'];
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
        $this->addCss($this->idimage->config['cssUrl'].'mgr/main.css');
        $this->addCss($this->idimage->config['cssUrl'].'mgr/help.css');
        $this->addJavascript($this->idimage->config['jsUrl'].'mgr/idimage.js');
        $this->addJavascript($this->idimage->config['jsUrl'].'mgr/misc/utils.js');
        $this->addJavascript($this->idimage->config['jsUrl'].'mgr/misc/combo.js');
        $this->addJavascript($this->idimage->config['jsUrl'].'mgr/misc/category.tree.js');
        $this->addJavascript($this->idimage->config['jsUrl'].'mgr/misc/default.cyclic.js');
        $this->addJavascript($this->idimage->config['jsUrl'].'mgr/misc/default.grid.js');
        $this->addJavascript($this->idimage->config['jsUrl'].'mgr/misc/default.window.js');
        $this->addJavascript($this->idimage->config['jsUrl'].'mgr/widgets/closes/grid.js');
        $this->addJavascript($this->idimage->config['jsUrl'].'mgr/widgets/closes/windows.js');
        $this->addJavascript($this->idimage->config['jsUrl'].'mgr/widgets/tasks/stat.js');
        $this->addJavascript($this->idimage->config['jsUrl'].'mgr/widgets/tasks/grid.js');
        $this->addJavascript($this->idimage->config['jsUrl'].'mgr/widgets/tasks/windows.js');
        $this->addJavascript($this->idimage->config['jsUrl'].'mgr/widgets/settings/form.js');
        $this->addJavascript($this->idimage->config['jsUrl'].'mgr/widgets/help/panel.js');
        $this->addJavascript($this->idimage->config['jsUrl'].'mgr/widgets/sync/panel.js');
        $this->addJavascript($this->idimage->config['jsUrl'].'mgr/widgets/home.panel.js');
        $this->addJavascript($this->idimage->config['jsUrl'].'mgr/sections/home.js');

        $this->addJavascript(MODX_MANAGER_URL.'assets/modext/util/datetime.js');

        $this->idimage->config['date_format'] = $this->modx->getOption('idimage_date_format', null, '%d.%m.%y <span class="gray">%H:%M</span>');
        $this->idimage->config['status_map'] = $this->idimage->statusMap();
        $this->idimage->config['status_map_task'] = $this->idimage->statusMapTask();

        // Stat
        $Stat = new \IdImage\Stat($this->idimage);
        $this->idimage->config['stat'] = $Stat->process()->tpl();
        $this->idimage->config['snippet'] = $this->snippet();
        $this->idimage->config['context'] = 'web';


        $this->addHtml(
            '<script type="text/javascript">
        idimage.config = '.json_encode($this->idimage->config).';
        idimage.config.connector_url = "'.$this->idimage->config['connectorUrl'].'";
        Ext.onReady(function() {MODx.load({ xtype: "idimage-page-home"});});
        </script>'
        );
    }

    /**
     * @return string
     */
    public function getTemplateFile()
    {
        $this->content .= '<div id="idimage-panel-home-div"></div>';

        return '';
    }

    public function snippet()
    {
        $tplFile = $this->idimage->config['corePath'].'elements/pages/snippet.tpl';
        if (!file_exists($tplFile)) {
            return null;
        }
        $tpl = file_get_contents($tplFile);

        $uniqid = uniqid();
        $chunk = $this->idimage->modx->newObject('modChunk', array('name' => "{tmp}-{$uniqid}"));
        $chunk->setCacheable(false);
        $output = $chunk->process([], $tpl);

        return $output;
    }

}
