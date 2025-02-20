<?php

class idImage
{
    /** @var modX $modx */
    public $modx;

    /** @var pdoFetch $pdoTools */
    public $pdoTools;

    /** @var array() $config */
    public $config = array();

    /** @var array $initialized */
    public $initialized = array();

    /** @var modError|null $error = */
    public $error = null;


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
        ], $config);

        $this->modx->addPackage('idimage', $this->config['modelPath']);
        $this->modx->lexicon->load('idimage:default');


        if ($this->pdoTools = $this->modx->getService('pdoFetch')) {
            $this->pdoTools->setConfig($this->config);
        }

        if (!class_exists('idimageResponse')) {
            include_once $corePath.'model/idimageclient.class.php';
        }

        if (!class_exists('idImageHander')) {
            include_once $corePath.'model/idimagehander.class.php';
        }
        if (!class_exists('idImageOperation')) {
            include_once $corePath.'model/idimageoperation.class.php';
        }
        if (!class_exists('xPDOQueryIdImage')) {
            include_once $corePath.'helpers/xpdoqueryidimage.class.php';
        }

        $this->modx->loadClass('idImageClose');
    }

    public function query()
    {
        return new xPDOQueryIdImage($this->modx, 'idImageClose');
    }

    public function statusMap()
    {
        return idImageClose::$statusMap;
    }

    public function statusMapComparison()
    {
        return idImageClose::$statusMapComparison;
    }

    /**
     * Initializes component into different contexts.
     *
     * @param  string  $ctx  The context to load. Defaults to web.
     * @param  array  $scriptProperties  Properties for initialization.
     *
     * @return bool
     */
    public function initialize($ctx = 'web', $scriptProperties = array())
    {
        $this->config = array_merge($this->config, $scriptProperties);

        $this->config['pageId'] = $this->modx->resource->id;

        switch ($ctx) {
            case 'mgr':
                break;
            default:
                if (!defined('MODX_API_MODE') || !MODX_API_MODE) {
                    $config = $this->makePlaceholders($this->config);
                    if ($css = $this->modx->getOption('idimage_frontend_css')) {
                        $this->modx->regClientCSS(str_replace($config['pl'], $config['vl'], $css));
                    }

                    $config_js = preg_replace(array('/^\n/', '/\t{5}/'),
                        '',
                        '
							idimage = {};
							idimageConfig = '.$this->modx->toJSON($this->config).';
					');


                    $this->modx->regClientStartupScript("<script type=\"text/javascript\">\n".$config_js."\n</script>", true);
                    if ($js = trim($this->modx->getOption('idimage_frontend_js'))) {
                        if (!empty($js) && preg_match('/\.js/i', $js)) {
                            $this->modx->regClientScript(preg_replace(array('/^\n/', '/\t{7}/'),
                                '',
                                '
							<script type="text/javascript">
								if(typeof jQuery == "undefined") {
									document.write("<script src=\"'.$this->config['jsUrl'].'web/lib/jquery.min.js\" type=\"text/javascript\"><\/script>");
								}
							</script>
							'),
                                true);
                            $this->modx->regClientScript(str_replace($config['pl'], $config['vl'], $js));
                        }
                    }
                }

                break;
        }

        return true;
    }


    /**
     * @return bool
     */
    public function loadServices()
    {
        $this->error = $this->modx->getService('error', 'error.modError', '', '');

        return true;
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
     * Method loads custom classes from specified directory
     *
     * @return void
     * @var string $dir Directory for load classes
     *
     */
    public function loadCustomClasses($dir)
    {
        $files = scandir($this->config['customPath'].$dir);
        foreach ($files as $file) {
            if (preg_match('/.*?\.class\.php$/i', $file)) {
                include_once($this->config['customPath'].$dir.'/'.$file);
            }
        }
    }


    /**
     * Добавление ошибок
     * @param  string  $message
     * @param  array  $data
     */
    public function addError($message, $data = array())
    {
        $message = $this->modx->lexicon($message, $data);
        $this->error->addError($message);
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->modx->error->getErrors();
    }

    /**
     * Вернут true если были ошибки
     * @return boolean
     */
    public function hasError()
    {
        return $this->modx->error->hasError();
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

    public function initPhpThumb()
    {
        if (!class_exists('modPhpThumb')) {
            if (file_exists(MODX_CORE_PATH.'model/phpthumb/modphpthumb.class.php')) {
                /** @noinspection PhpIncludeInspection */
                require MODX_CORE_PATH.'model/phpthumb/modphpthumb.class.php';
            } else {
                $this->modx->getService('phpthumb', 'modPhpThumb');
            }
        }
    }

    public function makeThumbnail(string $path, Closure $callback, array $options = [])
    {
        $this->initPhpThumb();

        $options = array_merge([
            'w' => 224,
            'h' => 224,
            'q' => 50,
            'zc' => 'T',
            'bg' => '000000',
            'f' => 'jpg',
        ], $options);
        $content = file_get_contents($path);


        $phpThumb = new modPhpThumb($this->modx);
        $phpThumb->initialize();


        $phpThumb->initialize();

        $tf = tempnam(MODX_BASE_PATH, 'idimage_');
        file_put_contents($tf, $content);
        $phpThumb->setSourceFilename($tf);

        foreach ($options as $k => $v) {
            $phpThumb->setParameter($k, $v);
        }

        $output = false;
        if ($phpThumb->GenerateThumbnail() && $phpThumb->RenderOutput()) {
            $this->modx->log(
                modX::LOG_LEVEL_INFO,
                '[miniShop2] phpThumb messages for .'.print_r($phpThumb->debugmessages, true)
            );
            $output = $phpThumb->outputImageData;
        } else {
            $this->modx->log(
                modX::LOG_LEVEL_ERROR,
                '[miniShop2] Could not generate thumbnail for '.print_r($phpThumb->debugmessages, true)
            );
        }

        if (file_exists($phpThumb->sourceFilename)) {
            @unlink($phpThumb->sourceFilename);
        }
        @unlink($tf);

        $pathTmp = MODX_BASE_PATH.'assets/tests.jpg';
        file_put_contents($pathTmp, $output);
        unset($output);

        try {
            $callback($pathTmp);
        } catch (Exception $e) {
            if (file_exists($pathTmp)) {
                unlink($pathTmp);
            }

            throw $e;
        }

        return true;
    }

    protected $client = null;


    public function client()
    {
        if (is_null($this->client)) {
            $this->client = new idimageClient($this->modx);
        }

        return $this->client;
    }

    public function hash(string $path)
    {
        $sizes = @getimagesize($path);
        $sizes['size'] = filesize($path);

        return md5(json_encode($sizes));
    }

    public function handler()
    {
        return new idImageHander($this);
    }

    protected $operation;

    public function operation()
    {
        if (is_null($this->operation)) {
            $this->operation = new idImageOperation($this);
        }

        return $this->operation;
    }
}
