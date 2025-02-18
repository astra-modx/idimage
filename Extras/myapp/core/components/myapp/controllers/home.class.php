<?php

/**
 * The home manager controller for myapp.
 *
 */
class myappHomeManagerController extends modExtraManagerController
{
    /** @var myapp $myapp */
    public $myapp;


    /**
     *
     */
    public function initialize()
    {
        $this->myapp = $this->modx->getService('myapp', 'myapp', MODX_CORE_PATH . 'components/myapp/model/');
        parent::initialize();
    }


    /**
     * @return array
     */
    public function getLanguageTopics()
    {
        return ['myapp:manager', 'myapp:default'];
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
        return $this->modx->lexicon('myapp');
    }


    /**
     * @return void
     */
    public function loadCustomCssJs()
    {
        $this->addCss($this->myapp->config['cssUrl'] . 'mgr/main.css');
        $this->addJavascript($this->myapp->config['jsUrl'] . 'mgr/myapp.js');
        $this->addJavascript($this->myapp->config['jsUrl'] . 'mgr/misc/utils.js');
        $this->addJavascript($this->myapp->config['jsUrl'] . 'mgr/misc/combo.js');
        $this->addJavascript($this->myapp->config['jsUrl'] . 'mgr/misc/default.grid.js');
        $this->addJavascript($this->myapp->config['jsUrl'] . 'mgr/misc/default.window.js');
        $this->addJavascript($this->myapp->config['jsUrl'] . 'mgr/widgets/items/grid.js');
        $this->addJavascript($this->myapp->config['jsUrl'] . 'mgr/widgets/items/windows.js');
        $this->addJavascript($this->myapp->config['jsUrl'] . 'mgr/widgets/home.panel.js');
        $this->addJavascript($this->myapp->config['jsUrl'] . 'mgr/sections/home.js');

        $this->addJavascript(MODX_MANAGER_URL . 'assets/modext/util/datetime.js');

        $this->myapp->config['date_format'] = $this->modx->getOption('myapp_date_format', null, '%d.%m.%y <span class="gray">%H:%M</span>');
        $this->myapp->config['help_buttons'] = ($buttons = $this->getButtons()) ? $buttons : '';

        $this->addHtml('<script type="text/javascript">
        myapp.config = ' . json_encode($this->myapp->config) . ';
        myapp.config.connector_url = "' . $this->myapp->config['connectorUrl'] . '";
        Ext.onReady(function() {MODx.load({ xtype: "myapp-page-home"});});
        </script>');
    }


    /**
     * @return string
     */
    public function getTemplateFile()
    {
        $this->content .=  '<div id="myapp-panel-home-div"></div>';
        return '';
    }

    /**
     * @return string
     */
    public function getButtons()
    {
        $buttons = null;
        $name = 'myapp';
        $path = "Extras/{$name}/_build/build.php";
        if (file_exists(MODX_BASE_PATH . $path)) {
            $site_url = $this->modx->getOption('site_url').$path;
            $buttons[] = [
                'url' => $site_url,
                'text' => $this->modx->lexicon('myapp_button_install'),
            ];
            $buttons[] = [
                'url' => $site_url.'?download=1&encryption_disabled=1',
                'text' => $this->modx->lexicon('myapp_button_download'),
            ];
            $buttons[] = [
                'url' => $site_url.'?download=1',
                'text' => $this->modx->lexicon('myapp_button_download_encryption'),
            ];
        }
        return $buttons;
    }
}