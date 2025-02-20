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
        $this->idimage = $this->modx->getService('idimage', 'idimage', MODX_CORE_PATH . 'components/idimage/model/');
        parent::initialize();
    }


    /**
     * @return array
     */
    public function getLanguageTopics()
    {
        return ['idimage:manager', 'idimage:default'];
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
        $this->addCss($this->idimage->config['cssUrl'] . 'mgr/main.css');
        $this->addJavascript($this->idimage->config['jsUrl'] . 'mgr/idimage.js');
        $this->addJavascript($this->idimage->config['jsUrl'] . 'mgr/misc/utils.js');
        $this->addJavascript($this->idimage->config['jsUrl'] . 'mgr/misc/combo.js');
        $this->addJavascript($this->idimage->config['jsUrl'] . 'mgr/misc/default.cyclic.js');
        $this->addJavascript($this->idimage->config['jsUrl'] . 'mgr/misc/default.grid.js');
        $this->addJavascript($this->idimage->config['jsUrl'] . 'mgr/misc/default.window.js');
        $this->addJavascript($this->idimage->config['jsUrl'] . 'mgr/widgets/closes/grid.js');
        $this->addJavascript($this->idimage->config['jsUrl'] . 'mgr/widgets/closes/windows.js');
        $this->addJavascript($this->idimage->config['jsUrl'] . 'mgr/widgets/home.panel.js');
        $this->addJavascript($this->idimage->config['jsUrl'] . 'mgr/sections/home.js');

        $this->addJavascript(MODX_MANAGER_URL . 'assets/modext/util/datetime.js');

        $this->idimage->config['date_format'] = $this->modx->getOption('idimage_date_format', null, '%d.%m.%y <span class="gray">%H:%M</span>');
        $this->idimage->config['help_buttons'] = ($buttons = $this->getButtons()) ? $buttons : '';
        $this->idimage->config['status_map'] = $this->idimage->statusMap();

        $this->addHtml('<script type="text/javascript">
        idimage.config = ' . json_encode($this->idimage->config) . ';
        idimage.config.connector_url = "' . $this->idimage->config['connectorUrl'] . '";
        Ext.onReady(function() {MODx.load({ xtype: "idimage-page-home"});});
        </script>');
    }


    /**
     * @return string
     */
    public function getTemplateFile()
    {
        $this->content .=  '<div id="idimage-panel-home-div"></div>';
        return '';
    }

    /**
     * @return string
     */
    public function getButtons()
    {
        $buttons = null;
        $name = 'idimage';
        $path = "Extras/{$name}/_build/build.php";
        if (file_exists(MODX_BASE_PATH . $path)) {
            $site_url = $this->modx->getOption('site_url').$path;
            $buttons[] = [
                'url' => $site_url,
                'text' => $this->modx->lexicon('idimage_button_install'),
            ];
            $buttons[] = [
                'url' => $site_url.'?download=1&encryption_disabled=1',
                'text' => $this->modx->lexicon('idimage_button_download'),
            ];
            $buttons[] = [
                'url' => $site_url.'?download=1',
                'text' => $this->modx->lexicon('idimage_button_download_encryption'),
            ];
        }
        return $buttons;
    }
}
