<?php

use IdImage\Actions;
use IdImage\Exceptions\ExceptionJsonModx;
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
        ], $config);

        if (empty($this->config['site_url'])) {
            $this->config['site_url'] = $this->modx->getOption('site_url');
        }

        $this->modx->addPackage('idimage', $this->config['modelPath']);
        $this->modx->lexicon->load('idimage:default');
        $this->modx->loadClass('idImageClose');
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

    public function phpThumb()
    {
        if (is_null($this->phpThumb)) {
            $this->phpThumb = new \IdImage\Support\PhpThumb($this->modx);
        }

        return $this->phpThumb;
    }

    public function makeThumbnail(string $path, Closure $callback): void
    {
        \IdImage\Support\PhpThumb::makeThumbnail($this->modx, $path, $callback);
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

}
