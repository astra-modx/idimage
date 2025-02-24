<?php
/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 22.02.2025
 * Time: 12:48
 */

namespace IdImage\Entities;


use IdImage\Abstracts\EntityAbsract;
use IdImage\Helpers\ReaderIndexed;

class EntityCatalog extends EntityAbsract
{
    protected $data = [];

    public function id()
    {
        return $this->get('id');
    }

    public function name()
    {
        return $this->get('name');
    }

    public function code()
    {
        return $this->get('code');
    }

    public function active()
    {
        return (bool)$this->get('active');
    }

    public function uploadApi()
    {
        return (bool)$this->get('upload_api');
    }

    public function created_at()
    {
        return $this->get('created_at');
    }

    public function updated_at()
    {
        return $this->get('created_at');
    }

    public function get(string $key)
    {
        if (isset($this->data[$key])) {
            return $this->data[$key];
        }

        return null;
    }


    public function toArray()
    {
        return $this->data;
    }

    public function fromArray(array $array)
    {
        // all variables
        foreach ($array as $key => $value) {
            $this->data[$key] = $value;
        }

        return $this;
    }
}
