<?php

namespace IdImage\Support;

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
        $i = 0;
        $collection = $this->xpdo->getCollection($this->_class, $this);
        foreach ($collection as $object) {
            $callback($object);
            $i++;
        }
        $this->totalIteration = $i;

        return $this;
    }

    public function collection(Closure $callback)
    {
        $i = 0;
        if ($this->prepare() && $this->stmt->execute()) {
            while ($row = $this->stmt->fetch(PDO::FETCH_ASSOC)) {
                $callback($row);
                $i++;
            }
        }
        $this->totalIteration = $i;

        return $this;
    }

    protected $totalIteration = 0;

    public function totalIteration()
    {
        return $this->totalIteration;
    }

    public function ids($field = 'id', $addSelect = true)
    {
        $ids = [];
        if ($addSelect) {
            if (!empty($this->query['from']['joins'])) {
                $field = $this->_class.'.'.$field. ' as '.$field;
            }
            $this->select($field);
        }

        if (strripos($field, 'as ') !== false) {
            if (preg_match('/\bas\s+(\w+)/i', $field, $matches)) {
                $field = $matches[1];
            }
        }

        if ($this->prepare() && $this->stmt->execute()) {
            while ($row = $this->stmt->fetch(PDO::FETCH_ASSOC)) {
                $value = $row[$field];
                $ids[] = $field == 'id' ? (int)$value : $value;
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
