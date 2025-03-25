<?php

class idImageSimilar extends xPDOSimpleObject
{
    public function allSimilar()
    {
        return !empty($this->get('data')) ? $this->get('data') : null;
    }
}
