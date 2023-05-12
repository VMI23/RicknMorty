<?php

declare(strict_types=1);

namespace RickAndMorty\Models;

class Location
{
    private int $id;
    private string $name;
    private string $type;
    private string $dimension;
    private array $residents;


    public function __construct(int $id, string $name, string $type, string $dimension, array $residents)
    {
        $this->id = $id;
        $this->name = $name;
        $this->type = $type;
        $this->dimension = $dimension;
        $this->residents = $residents;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getDimension(): string
    {
        return $this->dimension;
    }

    public function getResidents(): array
    {
        return $this->residents;
    }

    public function getResidentsIds(): array
    {
        $ids = [];
        foreach ($this->residents as $key => $resident) {
            $ids[] = $key;
        }
        return $ids;
    }

}