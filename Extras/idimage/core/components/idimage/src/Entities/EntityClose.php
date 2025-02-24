<?php
/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 22.02.2025
 * Time: 12:48
 */

namespace IdImage\Entities;


use IdImage\Abstracts\EntityAbsract;

class EntityClose extends EntityAbsract
{

    protected $offer_id;
    protected $received = false;
    protected $picture;
    protected $tags = [];
    protected $action = 'add';

    public function action()
    {
        return $this->action;
    }

    public function delete()
    {
        $this->action = 'delete';

        return $this;
    }

    public function validate()
    {
        if ($this->action() === 'add') {
            $res = $this->isLocalUrl();
            if ($res !== true) {
                return $res;
            }
        }


        /*  $res = $this->checkHttpStatus();
          if ($res !== true) {
              return $res;
          }*/

        return true;
    }

    public function setPicture(string $picture)
    {
        $this->picture = $picture;

        return $this;
    }

    public function getPicture()
    {
        return $this->picture;
    }

    public function setReceived(bool $received)
    {
        $this->received = $received;

        return $this;
    }

    public function getReceived()
    {
        return $this->received;
    }

    public function setTags(array $tags)
    {
        $this->tags = $tags;

        return $this;
    }

    public function getTags()
    {
        return $this->tags;
    }

    public function setOfferId(string $offer_id)
    {
        $this->offer_id = $offer_id;

        return $this;
    }

    public function getOfferId()
    {
        return $this->offer_id;
    }

    public function toArray()
    {
        if ($this->action() === 'delete') {
            $data = [
                'offer_id' => $this->getOfferId(),
            ];
        } else {
            $data = [
                'offer_id' => $this->getOfferId(),
                'picture' => $this->getPicture(),
            ];

            if ($tags = $this->getTags()) {
                $data['tags'] = $tags;
            }
        }


        return $data;
    }

    public function setStatus(int $status)
    {
        $this->status = $status;

        return $this;
    }


    public function getStatus()
    {
        return $this->status;
    }

    public function setError(array $item)
    {
        $this->error = $item;

        return $this;
    }

    private $error = null;

    public function getError()
    {
        return $this->error;
    }

    public function getErrors()
    {
        return $this->error;
    }

    public function isError()
    {
        return $this->error !== null;
    }

}
