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

class EntityIndexed extends EntityAbsract
{

    protected int $version = 0;
    protected string $download_link = '';
    protected bool $completed = false;
    protected bool $launch = false;
    protected bool $upload = false;
    protected bool $run = false;
    protected int $size = 0;
    protected int $images = 0;
    protected int $closes = 0;
    protected bool $sealed = false;

    public function version()
    {
        return $this->version;
    }

    public function downloadLink()
    {
        return $this->download_link;
    }

    public function setVersion(int $version)
    {
        $this->version = $version;

        return $this;
    }

    public function setDownloadLink(string $url)
    {
        $this->download_link = $url;

        return $this;
    }

    public function filenameVersion()
    {
        return pathinfo($this->downloadLink(), PATHINFO_FILENAME).'.json';
    }


    public function readerIndexed()
    {
        return new ReaderIndexed($this);
    }


    public function setLaunch(bool $launch)
    {
        $this->launch = $launch;

        return $this;
    }

    public function isLaunch()
    {
        return $this->launch;
    }

    public function setCompleted(bool $completed)
    {
        $this->completed = $completed;

        return $this;
    }

    public function isCompleted()
    {
        return $this->completed;
    }

    public function setUpload(bool $upload)
    {
        $this->upload = $upload;

        return $this;
    }

    public function isUpload()
    {
        return $this->upload;
    }

    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    public function getSize()
    {
        return $this->size;
    }

    public function setRun(bool $run)
    {
        $this->run = $run;

        return $this;
    }

    public function isRun()
    {
        return $this->run;
    }


    public function setImages(int $images)
    {
        $this->images = $images;

        return $this;
    }

    public function getImages()
    {
        return $this->images;
    }

    public function setCloses(int $closes)
    {
        $this->closes = $closes;

        return $this;
    }

    public function getCloses()
    {
        return $this->closes;
    }

    public function setSealed(bool $sealed)
    {
        $this->sealed = $sealed;

        return $this;
    }

    public function isSealed()
    {
        return $this->sealed;
    }

    public function toArray()
    {
        return [
            'version' => $this->version,
            'download_link' => $this->download_link,
            'completed' => $this->completed,
            'launch' => $this->launch,
            'upload' => $this->upload,
            'size' => $this->size,
            'images' => $this->images,
            'closes' => $this->closes,
            'sealed' => $this->sealed,
            'run' => $this->run,
        ];
    }

    public function fromArray(array $array)
    {
        // all variables
        foreach ($array as $key => $value) {
            if (property_exists($this, $key)) {
                if (!is_null($value)) {
                    $this->$key = $value;
                }
            }
        }

        return $this;
    }
}
