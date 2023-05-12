<?php

declare(strict_types=1);

namespace RickAndMorty\Models;

use stdClass;

class Character
{
    private int $id;
    private string $name;
    private string $status;
    private string $species;
    private string $type;
    private string $gender;
    private stdClass $origin;
    private stdClass $location;
    private string $imageUrl;
    private array $allEpisodes;

    /**
     * @param int $id
     * @param string $name
     * @param string $status
     * @param string $species
     * @param string $type
     * @param string $gender
     * @param stdClass $origin
     * @param stdClass $location
     * @param string $imageUrl
     * @param array $allEpisodes
     */
    public function __construct(
        int $id,
        string $name,
        string $status,
        string $species,
        string $type,
        string $gender,
        stdClass $origin,
        stdClass $location,
        string $imageUrl,
        array $allEpisodes
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->status = $status;
        $this->species = $species;
        $this->type = $type;
        $this->gender = $gender;
        $this->origin = $origin;
        $this->location = $location;
        $this->imageUrl = $imageUrl;
        $this->allEpisodes = $allEpisodes;
    }


    public function getId(): int
    {
        return $this->id;
    }


    public function getName(): string
    {
        return $this->name;
    }


    public function getStatus(): string
    {
        return $this->status;
    }


    public function getSpecies(): string
    {
        return $this->species;
    }


    public function getType(): string
    {
        return $this->type;
    }


    public function getGender(): string
    {
        return $this->gender;
    }


    public function getOriginName(): string
    {
        return $this->origin->name;
    }

    public function getOriginId(): string
    {
        return basename($this->origin->url);
    }


    public function getLocationName(): string
    {
        return $this->location->name;
    }

    public function getLocationId(): string
    {
        return basename($this->location->url);
    }


    public function getImageUrl(): string
    {
        return $this->imageUrl;
    }

    public function getAllEpisodeIds(): array
    {
        $episodeIds = [];
        foreach ($this->allEpisodes as $key => $episode) {
            $episodeIds[] = $key;
        }
        return $episodeIds;
    }

    public function getFirstSeen(): ?string
    {
        $allEpisodes = $this->getAllEpisodes();
        $firstEpisode = reset($allEpisodes); // Get the first element of the array

        return $firstEpisode !== false ? $firstEpisode : null;
    }

    public function getAllEpisodes(): array
    {
        return $this->allEpisodes;
    }

    public function getFirstSeenKey(): ?int
    {
        $allEpisodes = $this->getAllEpisodes();
        $firstEpisodeKey = key($allEpisodes); // Get the key of the first element

        return $firstEpisodeKey !== null ? $firstEpisodeKey : null;
    }


}


