<?php

declare(strict_types=1);

namespace App\Entity\Request;

class ItemRequest
{
    private ?int $id;
    private ?string $data;

    public function __construct()
    {
        $this->id = null;
        $this->data = null;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getData(): ?string
    {
        return $this->data;
    }

    public function setData(string $data): self
    {
        $this->data = $data;

        return $this;
    }
}
