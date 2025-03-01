<?php
/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 22.02.2025
 * Time: 12:48
 */

namespace IdImage\Api\Entities;


use IdImage\Abstracts\EntityAbsract;

class EntityCatalog extends EntityAbsract
{
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

    public function version()
    {
        return (int)$this->get('version');
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

}
