<?php

class idImageIndexed extends xPDOSimpleObject
{
    public function entity()
    {
        return new \IdImage\Entities\EntityIndexed();
    }

    public function deactivate()
    {
        if (!$this->isNew()) {
            $this->xpdo->exec("UPDATE {$this->_table} SET active = '0'  WHERE id != ".$this->get('id'));
        }
    }

    public function unUseVersion()
    {
        if (!$this->isNew()) {
            $this->xpdo->exec("UPDATE {$this->_table} SET use_version = '0'  WHERE id != ".$this->get('id'));
        }
    }
}
