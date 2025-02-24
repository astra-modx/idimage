<?php
/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 22.02.2025
 * Time: 12:48
 */

namespace IdImage\Abstracts;


abstract class ActionAbsract
{

    protected $actions = [];
    private string $action;
    private string $prefix;
    private \xPDOObject $object;

    public function __construct(\xPDOObject $object, string $prefix)
    {
        $this->object = $object;
        $this->prefix = $prefix;
        $this->action = ucfirst($prefix);
    }


    public function getActions()
    {
        return $this->actions;
    }


    public function object()
    {
        return $this->object;
    }

    public function get($k, $format = null, $formatTemplate = null)
    {
        return $this->object()->get($k, $format, $formatTemplate);
    }


    public function add(string $key, $icon = null, $button = true, $menu = true, $cls = null)
    {
        $icon = is_string($icon) ? 'icon '.$icon : '';
        $cls = is_string($cls) ? $cls : '';


        $lex = strtolower('idimage_'.$this->prefix.'_action_'.$key);
        $this->actions[] = [
            'cls' => $cls,
            'icon' => $icon,
            'title' => $this->object->xpdo->lexicon($lex),
            'action' => $key.$this->action,
            'button' => $button,
            'menu' => $menu,
        ];

        return $this;
    }

}
