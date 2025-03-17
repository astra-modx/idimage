<?php
/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 10.03.2025
 * Time: 16:33
 */

namespace IdImage\Entites;


use idImageTask;

class TaskEntity
{

    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_PENDING = 'pending';
    private ?idImageTask $task = null;

    private ?string $task_id = null;

    private string $status = self::STATUS_PENDING;

    private string $type = 'basic';


    private ?array $embedding = null;
    /**
     * @var true
     */
    private bool $received = false;
    /**
     * @var mixed|null
     */
    private ?array $errors = null;
    private ?string $offer_id = null;
    private ?string $etag = null;
    private ?string $picture = null;
    private ?string $picturePath = null;
    /**
     * @var mixed
     */
    private ?int $id = null;
    private string $tmpPath;
    protected ?array $similar = null;

    public function __construct()
    {
    }

    public function fromArray(array $data): self
    {
        if (isset($data['id'])) {
            $this->setId($data['id']);
        }
        if (isset($data['task_id'])) {
            $this->setTaskId($data['task_id']);
        }

        if (isset($data['etag'])) {
            $this->setEtag($data['etag']);
        }
        if (isset($data['offer_id'])) {
            $this->setOfferId($data['offer_id']);
        }
        if (isset($data['status'])) {
            $this->setStatus($data['status']);
        }

        $this->errors = $data['errors'] ?? null;
        $result = $data['result'] ?? null;

        if (isset($data['picture_path'])) {
            $this->setPicturePath($data['picture_path']);
        }
        if (isset($data['picture'])) {
            $this->setPicture($data['picture']);
        }

        if (!empty($result)) {

            $this->received = true;
            $type = $result['type'] ?? 'basic';
            $this->setType($type);
            $embedding = $result['embedding'] ?? null;
            if ($embedding) {
                $this->setEmbedding($embedding);
            }

            $similar = $data['similar'] ?? null;
            if ($similar) {
                $this->setSimilar($similar);
            }
        }

        return $this;
    }


    public function getTaskId(): string
    {
        return $this->task_id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getEmbedding(): ?array
    {
        return $this->embedding;
    }


    public function toArray(): array
    {
        return [
            'task_id' => $this->task_id,
            'status' => $this->status,
            'etag' => $this->etag,
            'offer_id' => $this->offer_id,
            'errors' => $this->errors,
            'type' => $this->type,
            'picture' => $this->picture,
            'picture_path' => $this->picturePath,
            'received' => $this->received,
            'embedding' => $this->embedding,
            'similar' => $this->similar,
        ];
    }


    public function setStatus(string $string)
    {
        $this->status = $string;

        return $this;
    }

    public function setTaskId(string $id)
    {
        $this->task_id = $id;

        return $this;
    }

    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    public function setEmbedding(array $embedding)
    {
        $this->embedding = $embedding;

        return $this;
    }

    public function setReceived(bool $true)
    {
        $this->received = $true;

        return $this;
    }

    public function isReceived()
    {
        return $this->received;
    }

    public function setUpload(string $url)
    {
        $this->url = $url;

        return $this;
    }

    public function getUpload()
    {
        return $this->url;
    }

    public function getEtag()
    {
        return $this->etag;
    }

    public function setEtag($etag)
    {
        $this->etag = $etag;

        return $this;
    }

    public function getErrors(): ?array
    {
        return $this->errors;
    }

    public function setErrors($errors)
    {
        $this->setStatus(idImageTask::STATUS_FAILED);

        if ($errors instanceof \Throwable) {
            $this->errors = [
                'msg' => '[Throwable] '.$errors->getMessage(),
            ];
        } elseif ($errors instanceof \Exception) {
            $this->errors = [
                'msg' => '[Exception] '.$errors->getMessage(),
            ];
        } elseif (is_array($errors) || is_null($errors)) {
            $this->errors = $errors;
        } elseif (is_string($errors)) {
            $this->errors = [
                'msg' => $errors,
            ];
        }

        return $this;
    }

    public function setOfferId($offer_id)
    {
        $this->offer_id = $offer_id;

        return $this;
    }

    public function getOfferId()
    {
        return $this->offer_id;
    }

    public function setPicture(?string $link)
    {
        $this->picture = $link;

        return $this;
    }

    public function getPicture()
    {
        return $this->picture;
    }

    public function setPicturePath(string $picturePath)
    {
        $this->picturePath = $picturePath;

        return $this;
    }

    public function getPicturePath()
    {
        return MODX_BASE_PATH.$this->picturePath;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setTmpPath(string $uploadPath)
    {
        $this->tmpPath = $uploadPath;

        return $this;
    }

    public function getTmpPath()
    {
        return $this->tmpPath;
    }

    public function setSimilar(array $similar)
    {
        $this->similar = $similar;

        return $this;
    }

    public function getSimilar()
    {
        return $this->similar;
    }

}
