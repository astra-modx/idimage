<?php

use IdImage\Actions;
use IdImage\Client;
use IdImage\Operation;
use IdImage\Query;

include_once MODX_CORE_PATH.'components/idimage/vendor/autoload.php';

class idImage
{
    /** @var modX $modx */
    public $modx;

    /** @var array() $config */
    public $config = array();

    /* @var Client $client */
    protected $client = null;

    /* @var Query $query */
    protected $query = null;

    /* @var Operation $query */
    protected $operation = null;

    /* @var Actions $actions */
    protected $actions = null;

    /* @var \IdImage\Extractor $extractor */
    protected $extractor = null;

    /* @var \IdImage\Helpers\PhpThumb $phpThumb */
    protected $phpThumb = null;


    /**
     * @param  modX  $modx
     * @param  array  $config
     */
    function __construct(modX &$modx, array $config = [])
    {
        $this->modx =& $modx;
        $corePath = MODX_CORE_PATH.'components/idimage/';
        $assetsUrl = MODX_ASSETS_URL.'components/idimage/';

        $this->config = array_merge([
            'corePath' => $corePath,
            'modelPath' => $corePath.'model/',
            'processorsPath' => $corePath.'processors/',
            'customPath' => $corePath.'custom/',

            'connectorUrl' => $assetsUrl.'connector.php',
            'assetsUrl' => $assetsUrl,
            'cssUrl' => $assetsUrl.'css/',
            'jsUrl' => $assetsUrl.'js/',
            'mode_upload' => $this->modx->getOption('idimage_mode_upload', $config, 'picture'),
            'path_versions' => MODX_CORE_PATH.'cache/idimage/versions/',
            'site_url' => $this->modx->getOption('idimage_site_url', $config, null),
            'cloud' => $this->modx->getOption('idimage_cloud', $config, false),
            'extract_path' => $this->modx->getOption('idimage_extract_path', $config, MODX_CORE_PATH.'cache/idimage/indexed', true),
        ], $config);


        if (empty($this->config['site_url'])) {
            $this->config['site_url'] = $this->modx->getOption('site_url');
        }

        $this->modx->addPackage('idimage', $this->config['modelPath']);
        $this->modx->lexicon->load('idimage:default');

        $this->modx->loadClass('idImageClose');
    }

    public function pathVersions()
    {
        return $this->config['path_versions'];
    }

    public function hasToken()
    {
        return !empty($this->modx->getOption('idimage_token'));
    }

    public function statusMap()
    {
        return idImageClose::$statusMap;
    }

    public function statusMapService()
    {
        return idImageClose::$statusServiceMap;
    }

    public function siteUrl()
    {
        return rtrim($this->config['site_url'], '/');
    }

    public function validateSiteUrl()
    {
        $siteUrl = $this->siteUrl();
        $parsedUrl = parse_url($siteUrl);

        if (!$parsedUrl || !isset($parsedUrl['host'])) {
            return 'Not local address';
        }

        $host = strtolower($parsedUrl['host']);

        // Проверяем, является ли хост локальным
        if ($host === 'localhost' || $host === '127.0.0.1') {
            return false;
        }

        return true;
    }

    public function isCloudUpload()
    {
        return (bool)$this->config['cloud'];
    }

    /**
     * Shorthand for the call of processor
     *
     * @access public
     *
     * @param  string  $action  Path to processor
     * @param  array  $data  Data to be transmitted to the processor
     *
     * @return mixed The result of the processor
     */
    public function runProcessor($action = '', $data = array())
    {
        if (empty($action)) {
            return false;
        }
        #$this->modx->error->reset();
        $processorsPath = !empty($this->config['processorsPath'])
            ? $this->config['processorsPath']
            : MODX_CORE_PATH.'components/idimage/processors/';

        return $this->modx->runProcessor($action, $data, array(
            'processors_path' => $processorsPath,
        ));
    }

    /**
     * Обработчик для событий
     * @param  modSystemEvent  $event
     * @param  array  $scriptProperties
     */
    public function loadHandlerEvent(modSystemEvent $event, $scriptProperties = array())
    {
        switch ($event->name) {
            case 'OnHandleRequest':
            case 'OnLoadWebDocument':
                break;
        }
    }


    public function client()
    {
        if (is_null($this->client)) {
            $this->client = new Client($this->modx);
        }

        return $this->client;
    }


    public function hash(string $path)
    {
        $sizes = @getimagesize($path);
        $sizes['size'] = filesize($path);

        return md5(json_encode($sizes));
    }

    public function query()
    {
        if (is_null($this->query)) {
            $this->query = new Query($this);
        }

        return $this->query;
    }


    public function operation()
    {
        if (is_null($this->operation)) {
            $this->operation = new Operation($this);
        }

        return $this->operation;
    }

    public function actions()
    {
        if (is_null($this->actions)) {
            $this->actions = new Actions($this->modx);
        }

        return $this->actions;
    }

    public function extractor()
    {
        if (is_null($this->extractor)) {
            $this->extractor = new \IdImage\Extractor();
        }

        return $this->extractor;
    }

    public function phpThumb()
    {
        if (is_null($this->phpThumb)) {
            $this->phpThumb = new \IdImage\Helpers\PhpThumb($this->modx);
        }

        return $this->phpThumb;
    }

}
