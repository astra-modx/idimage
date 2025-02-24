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
        $this->addCss($this->idimage->config['cssUrl'].'mgr/main.css');
        $this->addCss($this->idimage->config['cssUrl'].'mgr/help.css');
        $this->addJavascript($this->idimage->config['jsUrl'].'mgr/idimage.js');
        $this->addJavascript($this->idimage->config['jsUrl'].'mgr/misc/utils.js');
        $this->addJavascript($this->idimage->config['jsUrl'].'mgr/misc/combo.js');
        $this->addJavascript($this->idimage->config['jsUrl'].'mgr/misc/default.cyclic.js');
        $this->addJavascript($this->idimage->config['jsUrl'].'mgr/misc/default.grid.js');
        $this->addJavascript($this->idimage->config['jsUrl'].'mgr/misc/default.window.js');
        $this->addJavascript($this->idimage->config['jsUrl'].'mgr/widgets/closes/grid.js');
        $this->addJavascript($this->idimage->config['jsUrl'].'mgr/widgets/closes/windows.js');
        $this->addJavascript($this->idimage->config['jsUrl'].'mgr/widgets/clouds/grid.js');
        $this->addJavascript($this->idimage->config['jsUrl'].'mgr/widgets/indexeds/grid.js');
        $this->addJavascript($this->idimage->config['jsUrl'].'mgr/widgets/indexeds/windows.js');
        $this->addJavascript($this->idimage->config['jsUrl'].'mgr/widgets/settings/form.js');

        $this->addJavascript($this->idimage->config['jsUrl'].'mgr/widgets/home.panel.js');
        $this->addJavascript($this->idimage->config['jsUrl'].'mgr/sections/home.js');

        $this->addJavascript(MODX_MANAGER_URL.'assets/modext/util/datetime.js');

        $this->idimage->config['date_format'] = $this->modx->getOption('idimage_date_format', null, '%d.%m.%y <span class="gray">%H:%M</span>');
        $this->idimage->config['help_buttons'] = ($buttons = $this->getButtons()) ? $buttons : '';
        $this->idimage->config['status_map'] = $this->idimage->statusMap();
        $this->idimage->config['status_service_map'] = $this->idimage->statusMapService();
        $this->idimage->config['cloud'] = $this->idimage->isCloudUpload();
        $this->idimage->config['availability'] = $this->availability();
        $this->addHtml(
            '<script type="text/javascript">
        idimage.config = '.json_encode($this->idimage->config).';
        idimage.config.connector_url = "'.$this->idimage->config['connectorUrl'].'";
        Ext.onReady(function() {MODx.load({ xtype: "idimage-page-home"});});
        </script>'
        );
    }


    public function availability()
    {
        $data = [
            'enable' => !empty($this->modx->getOption('idimage_enable')),
            'token' => !empty($this->modx->getOption('idimage_token')),
            'cloud' => $this->idimage->isCloudUpload(),
            'validate_site_url' => $this->idimage->validateSiteUrl(),
        ];

        return $data;
    }

    /**
     * @return string
     */
    public function getTemplateFile()
    {
        $this->content .= '<div id="idimage-panel-home-div"></div>';
        if ($help = $this->helpPage()) {
            $this->content .= $help;
        }

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
        if (file_exists(MODX_BASE_PATH.$path)) {
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


    public function helpPage()
    {
        $props = $this->availability();


        $tplFile = $this->idimage->config['corePath'].'elements/pages/help.tpl';

        if (!file_exists($tplFile)) {
            return null;
        }
        $tpl = file_get_contents($tplFile);

        $uniqid = uniqid();
        $chunk = $this->modx->newObject('modChunk', array('name' => "{tmp}-{$uniqid}"));
        $chunk->setCacheable(false);
        $output = $chunk->process($props, $tpl);

        return $output;
    }
}
