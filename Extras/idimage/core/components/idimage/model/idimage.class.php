<?php

use IdImage\Actions;
use IdImage\Exceptions\ExceptionJsonModx;
use IdImage\Sender;
use IdImage\Support\Query;

include_once MODX_CORE_PATH.'components/idimage/vendor/autoload.php';

class idImage
{
    /** @var modX $modx */
    public $modx;

    /** @var array() $config */
    public $config = array();


    /* @var Query $query */
    protected $query = null;

    /* @var Actions $actions */
    protected $api = null;

    /* @var \IdImage\Support\PhpThumb $phpThumb */
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
            'minimum_probability_score' => $this->modx->getOption('idimage_minimum_probability_score', $config, 70, true),
            'maximum_products_found' => $this->modx->getOption('idimage_maximum_products_found', $config, 50, true),
            'root_parent' => $this->modx->getOption('idimage_root_parent', $config, 0, true),
            'site_url' => $this->modx->getOption('idimage_site_url', $config, null),
            'send_file' => $this->modx->getOption('idimage_send_file', $config, false),
            'limit_upload' => $this->modx->getOption('idimage_limit_upload', $config, 10, true),
            'limit_creation' => $this->modx->getOption('idimage_limit_creation', $config, 1000, true),
            'limit_indexed' => $this->modx->getOption('idimage_limit_indexed', $config, 100, true),
            'limit_show_similar_products' => $this->modx->getOption('idimage_limit_show_similar_products', $config, 5, true),
            'limit_attempt' => $this->modx->getOption('idimage_limit_attempt', $config, 20, true),
            'default_thumb' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAoAAAAKCAYAAACNMs+9AAAAKUlEQVR42mNgGAWjYBSMglEwCIOhGEENEBsDgmrAAGQ9gP4HAEKaBUxFSYd7AAAAAElFTkSuQmCC',
        ], $config);

        if (empty($this->config['site_url'])) {
            $this->config['site_url'] = $this->modx->getOption('site_url');
        }

        $this->modx->addPackage('idimage', $this->config['modelPath']);
        $this->modx->lexicon->load('idimage:default');
        $this->modx->loadClass('idImageClose');
        $this->modx->loadClass('idImageTask');
    }

    public function siteUrl()
    {
        return rtrim($this->config['site_url'], '/');
    }

    public function isSendFile(): bool
    {
        return (boolean)$this->config['send_file'] ?? false;
    }

    public function hasToken()
    {
        return !empty($this->modx->getOption('idimage_token'));
    }

    public function statusMap()
    {
        return idImageClose::$statusMap;
    }

    public function statusMapTask()
    {
        return idImageTask::$statusMap;
    }

    public function minimumProbabilityScore(): int
    {
        return $this->config['minimum_probability_score'] ?? 70;
    }

    public function maximumProductsFound(): int
    {
        return $this->config['maximum_products_found'] ?? 50;
    }

    /**
     * Shorthand for the call of processor
     *
     * @access public
     *
     * @param  string  $action  Path to processor
     * @param  array  $data  Data to be transmitted to the processor
     *
     * @return mixed|modProcessorResponse The result of the processor
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

    public function api()
    {
        if (is_null($this->api)) {
            $this->api = new Actions($this);
        }

        return $this->api;
    }

    public function makeThumbnail(string $source, string $target): string
    {
        return \IdImage\Support\PhpThumb::makeThumbnail($this->modx, $source, $target);
    }

    public function balance()
    {
        $Response = $this->api()->ai()->balance()->send();
        if ($Response->isFail()) {
            $Response->exception();
        }

        return $Response->json('balance');
    }

    public function canToken()
    {
        if (!$this->hasToken()) {
            throw new ExceptionJsonModx($this->modx->lexicon('idimage_token_not_set'), 401);
        }
    }

    public function rootParent()
    {
        return $this->config['root_parent'] ?? 0;
    }

    public function limitUpload()
    {
        $limit = (int)$this->config['limit_upload'] ?? 10;
        if ($limit > 20) {
            $limit = 20;
        }

        return $limit;
    }

    public function limitCreation()
    {
        return (int)$this->config['limit_creation'] ?? 50;
    }


    public function limitIndexed()
    {
        return (int)$this->config['limit_indexed'] ?? 100;
    }



    public function sender()
    {
        return new Sender($this);
    }


    public function taskCollection()
    {
        return new \IdImage\TaskCollection($this);
    }

    public function limitShowSimilarProducts()
    {
        return (int)$this->config['limit_show_similar_products'] ?? 5;
    }

    public function attemptLimit()
    {
        $limit = (int)$this->config['limit_attempt'] ?? 100;
        if ($limit > 100) {
            $limit = 100;
        }

        return $limit;
    }

    public function limitPoll()
    {
        return 1000;
    }

}
