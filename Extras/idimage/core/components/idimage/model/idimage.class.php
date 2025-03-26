<?php

use IdImage\Actions;
use IdImage\Exceptions\ExceptionJsonModx;
use IdImage\Indexer;
use IdImage\Sender;
use IdImage\Support\Query;
use IdImage\Support\SimilarExtractor;

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

    /* @var string $version версия приложения для регистрации js */
    public $version = 0;


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
            'limit_task' => $this->modx->getOption('idimage_limit_task', $config, 1000, true),
            'enable' => (bool)$this->modx->getOption('idimage_enable', $config, false),
            'indexed_service' => (bool)$this->modx->getOption('idimage_indexed_service', $config, false),
            'indexed_type' => (string)$this->modx->getOption('idimage_indexed_type', $config, false),
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

                break;
            case 'OnDocFormRender':
                if ($scriptProperties['mode'] !== 'upd') {
                    return false;
                }

                /** @var modResource $resource */
                $resource = $scriptProperties['resource'];
                if ($resource->get('class_key') != 'msProduct') {
                    return false;
                }

                $this->modx->controller->addLexiconTopic('idimage:manager');
                $this->modx->controller->addLexiconTopic('idimage:tabs');
                $this->modx->controller->addLexiconTopic('idimage:gallery');


                $this->loadVersion();
                $this->addCss('mgr/main.css');
                $this->addCss('mgr/help.css');
                $this->addJavascript('mgr/idimage.js');
                $this->addJavascript('mgr/misc/utils.js');
                $this->addJavascript('mgr/misc/combo.js');
                $this->addJavascript('mgr/misc/category.tree.js');
                $this->addJavascript('mgr/misc/default.cyclic.js');
                $this->addJavascript('mgr/misc/default.grid.js');
                $this->addJavascript('mgr/misc/default.window.js');
                $this->addJavascript('mgr/widgets/closes/grid.js');
                $this->addJavascript('mgr/widgets/closes/windows.js');


                $this->addJavascript('mgr/widgets/gallery/gallery.form.js');
                $this->addJavascript('mgr/widgets/gallery/gallery.panel.js');
                $this->addJavascript('mgr/widgets/gallery/gallery.toolbar.js');
                $this->addJavascript('mgr/widgets/gallery/gallery.view.js');
                $this->addJavascript('mgr/widgets/gallery/gallery.window.js');
                $this->addJavascript('mgr/widgets/customtab.js');

                $config = $this->config;
                $config['record'] = [
                    'id' => $resource->get('id'),
                ];
                $config['init_tab'] = false;
                $config['pageSize'] = 100;

                $close_id = null;
                $closeData = [
                    'active' => $this->modx->lexicon('no'),
                    'maximum_products_found' => $this->option('maximum_products_found').' '.$this->modx->lexicon('idimage_gallery_unit'),
                    'minimum_probability_score' => $this->option('minimum_probability_score').'%',
                    'embedding_exists' => $this->modx->lexicon('no'),
                    'similar_exists' => $this->modx->lexicon('no'),
                ];

                /* @var idImageClose $close */
                if ($close = $this->modx->getObject('idImageClose', ['pid' => $resource->get('id')])) {
                    $close_id = $close->get('id');
                    $closeData['embedding_exists'] = $this->modx->lexicon($close->isEmbedding() ? 'yes' : 'no');
                    $closeData['similar_exists'] = $this->modx->lexicon($close->isSimilar() ? 'yes' : 'no');
                    $closeData['active'] = $this->modx->lexicon($close->get('active') ? 'yes' : 'no');
                }

                $config['close'] = $closeData;
                $config['close_id'] = $close_id;

                $this->addHtml(
                    '<script type="text/javascript">
                        idimage.config = '.json_encode($config).';
                        idimage.config.connector_url = "'.$this->config['connectorUrl'].'";
                        </script>'
                );

                // Reg button ms2gallery
                /*    $this->addHtml('
                                    <script type="text/javascript">
                                    // <![CDATA[
                                    Ext.ComponentMgr.onAvailable(\'minishop2-gallery-page-toolbar\', function () {


                                    console.log(idimage);
                                        //if (!msGallerySearch.config.disable_minishop2) {
                                         //   msGallerySearch.minishop2 = true
                                          //  msGallerySearchToolbarMiniShop2 = new msGallerySearch.toolbar.Minishop()
                                       // }
                                    })
                                    // ]]>
                                </script>');*/
                /** @var modResource $resource */
                /*$templates = array_map('trim', explode(',', $this->modx->getOption('ms2gallery_disable_for_templates')));

                $disable = $mode == 'new' ||
                    ($templates[0] != '' && in_array($resource->get('template'), $templates)) ||
                    ($resource->class_key == 'msProduct' &&
                        $this->modx->getOption('ms2gallery_disable_for_ms2', null, true) &&
                        !$this->modx->getOption('ms2gallery_sync_ms2', null, false)
                    );*/
                break;
        }

        return true;
    }

    public function loadVersion()
    {
        $signature = 'idimage';

        /* @var transport.modTransportPackage $object */
        $q = $this->modx->newQuery('transport.modTransportPackage');
        $q->where(array(
            'package_name' => $signature,
        ));
        $q->sortby('installed', 'DESC');
        if ($package = $this->modx->getObject('transport.modTransportPackage', $q)) {
            $version = $package->get(array('version_major', 'version_minor', 'version_patch'));
            $this->version = implode('.', $version);
        }
    }

    /**
     * @param  string  $src
     */
    public function addHtml($src)
    {
        $this->modx->controller->addHtml($src);
    }

    /**
     * @param  string  $src
     */
    public function addCss($src)
    {
        $this->modx->controller->addCss($this->config['cssUrl'].$src.'?version='.$this->version);
    }


    /**
     * @param  string  $src
     */
    public function addJavascript($src)
    {
        $this->modx->controller->addJavascript($this->config['jsUrl'].$src.'?version='.$this->version);
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

        return 30;
    }

    public function limitCreation()
    {
        return (int)$this->config['limit_creation'] ?? 50;
    }

    public function limitTask()
    {
        return (int)$this->config['limit_task'] ?? 1000;
    }


    public function limitIndexed()
    {
        return (int)$this->config['limit_indexed'] ?? 100;
    }

    public function isIndexedService()
    {
        return $this->option('indexed_service');
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

    public function attemptFailureLimit()
    {
        return 3;
    }

    public function limitPoll()
    {
        return 1000;
    }

    public function option(string $key, $default = null)
    {
        // indexed_service
        if (!array_key_exists($key, $this->config)) {
            return $default;
        }

        return $this->config[$key];
    }

    protected ?\IdImage\Indexer $indexer = null;

    public function indexer()
    {
        if (is_null($this->indexer)) {
            $this->indexer = new Indexer($this);
        }

        return $this->indexer;
    }

    protected ?\IdImage\Support\SimilarExtractor $extractor = null;

    public function extractor()
    {
        if (is_null($this->extractor)) {
            $this->extractor = new SimilarExtractor($this);
        }

        return $this->extractor;
    }

    public function settingKeys()
    {
        $values = [
            'indexed_type' => 'index_all',
            'token' => '',
            'maximum_products_found' => '',
        ];

        return $values;
    }

    protected $optionsCache = [
        xPDO::OPT_CACHE_KEY => 'default/idimage',
        xPDO::OPT_CACHE_HANDLER => 'xPDOFileCache',
    ];
    protected $cacheProductsIndexed = 'idimage_total_products_indexed';


    public function setTotalProductsIndexed(int $count)
    {
        $data = [
            'total' => $count,
        ];

        return $this->modx->getCacheManager()->set($this->cacheProductsIndexed, $data, 10000, $this->optionsCache);
    }

    public function getTotalProductsIndexed()
    {
        $data = $this->modx->getCacheManager()->get($this->cacheProductsIndexed, $this->optionsCache);

        return $data['total'] ?? 0;
    }

}
