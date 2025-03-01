<?php
/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 24.02.2025
 * Time: 11:50
 */

namespace IdImage\Support;

use IdImage\Interfaces\ActionInterfaces;

class IndexedAction extends \IdImage\Abstracts\ActionAbsract implements ActionInterfaces
{

    public function getList($callback)
    {
        if (!is_callable($callback)) {
            return $this;
        }
        $callback($this);

        return $this->getActions();
    }

}
