<?php
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

    public function each(Closure $callback)
    {
        $collection = $this->xpdo->getCollection($this->_class, $this);
        foreach ($collection as $object) {
            $callback($object);
        }

        return $this;
    }


    public function ids()
    {
        $ids = [];
        $this->select('id');
        if ($this->prepare() && $this->stmt->execute()) {
            while ($row = $this->stmt->fetch(PDO::FETCH_ASSOC)) {
                $ids[] = (int)$row['id'];
            }
        }

        return $ids;
    }
}
