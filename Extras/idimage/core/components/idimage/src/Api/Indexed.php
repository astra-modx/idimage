<?php
/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 24.02.2025
 * Time: 11:50
 */

namespace IdImage\Api;

use IdImage\Abstracts\ApiAbstract;
use IdImage\Api\Entities\EntityCatalog;
use IdImage\Interfaces\ApiInterfaces;

class Indexed extends ApiAbstract implements ApiInterfaces
{

    public function item(): self
    {
        return $this->get('indexed');
    }

    public function launch()
    {
        return $this->post('indexed/launch');
    }

    public function entity(): EntityCatalog
    {
        return $this->_entity(EntityCatalog::class);
    }

}
