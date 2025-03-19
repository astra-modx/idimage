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

    protected ?string $operation = null;

    private ?string $task_id = null;

    private string $status = self::STATUS_PENDING;


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
    private ?string $picture = null;
    private ?string $picturePath = null;
    /**
     * @var mixed
     */
    private ?int $id = null;
    protected ?array $similar = null;

    public function __construct()
    {
    }

    public function fromArray(array $data): self
    {
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


    public function getTaskId(): ?string
    {
        return $this->task_id;
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
            'id' => $this->id,
            'task_id' => $this->task_id,
            'status' => $this->status,
            'offer_id' => $this->offer_id,
            'errors' => $this->errors,
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

    public function setOfferId(string $offer_id)
    {
        $this->offer_id = $offer_id;

        return $this;
    }

    public function getOfferId(): string
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
        return $this->picturePath;
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

    public function setSimilar(array $similar)
    {
        $this->similar = $similar;

        return $this;
    }

    public function getSimilar()
    {
        return $this->similar;
    }


    public function setOperation(string $string)
    {
        $this->operation = $string;

        return $this;
    }

    public function getOperation(): string
    {
        return $this->operation;
    }

    protected bool $exists = false;
    protected ?array $response = null;

    public function setResponse(array $item)
    {
        $this->response = $item;
        $this->exists = true;

        return $this;
    }

    public function getResponse(): array
    {
        return $this->response;
    }

    public function exists()
    {
        return $this->exists;
    }
}
