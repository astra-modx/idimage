<?php

namespace IdImage\Helpers;

use Closure;
use PDO;
use xPDOQuery_mysql;

if (!class_exists('xPDOQuery_mysql')) {
    include_once MODX_CORE_PATH.'xpdo/om/mysql/xpdoquery.class.php';
}

/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 20.02.2025
 * Time: 10:43
 */
class xPDOQueryIdImage extends xPDOQuery_mysql
{

    public function count()
    {
        return $this->xpdo->getCount($this->_class, $this);
    }

    public function each($callback)
    {
        $collection = $this->xpdo->getCollection($this->_class, $this);
        foreach ($collection as $object) {
            $callback($object);
        }

        return $this;
    }

    public function collection(Closure $callback)
    {
        if ($this->prepare() && $this->stmt->execute()) {
            while ($row = $this->stmt->fetch(PDO::FETCH_ASSOC)) {
                $callback($row);
            }
        }

        return $this;
    }


    public function ids($field = 'id', $addSelect = true)
    {
        $ids = [];
        if ($addSelect) {
            $this->select($field);
        }
        if ($this->prepare() && $this->stmt->execute()) {
            while ($row = $this->stmt->fetch(PDO::FETCH_ASSOC)) {
                $ids[] = (int)$row[$field];
            }
        }

        return $ids;
    }

    public function toArray()
    {
        $items = [];

        $this->collection(function ($item) use (&$items) {
            $items[] = $item;
        });

        return $items;
    }
}
