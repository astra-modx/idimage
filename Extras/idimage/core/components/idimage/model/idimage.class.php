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
        ], $config);

        $this->modx->addPackage('idimage', $this->config['modelPath']);
        $this->modx->lexicon->load('idimage:default');

        $this->modx->loadClass('idImageClose');
    }

    public function statusMap()
    {
        return idImageClose::$statusMap;
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

}
