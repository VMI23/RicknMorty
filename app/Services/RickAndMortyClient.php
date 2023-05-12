<?php

declare(strict_types=1);

namespace RickAndMorty\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use RickAndMorty\Cache;
use RickAndMorty\Models\Character;
use RickAndMorty\Models\Episode;
use RickAndMorty\Models\Location;
use stdClass;

class RickAndMortyClient
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://rickandmortyapi.com/api/',
        ]);
    }

    public function allCharacters(?array $ids = null): array
    {
        try {
            $episodes = $this->listOfEpisodes();

            $charRange = $ids
                ? (count($ids) === 1 ? $ids[0] : implode(',', $ids))
                : implode(',', range(1, 826));

            $characters = [];

            $cacheKey = md5('characters');
            if (!Cache::has($cacheKey)) {
                $responseJson = $this->client->get("character/$charRange")->getBody()->getContents();
                Cache::remember($cacheKey, $responseJson, 5);
            } else {
                $responseJson = Cache::get($cacheKey);
            }

            $response = json_decode($responseJson);
            foreach ($response as $character) {
                $characters[$character->id] = $this->setCharacter($character, $episodes);
            }

            return $characters;
        } catch (GuzzleException $e) {
            return [];
        }
    }

    function listOfEpisodes(): array
    {
        try {
            $episodes = [];
            $episodeIds = implode(",", range(1, 51));
            $cacheKey = md5('episodes');

            if (!Cache::has($cacheKey)) {
                $responseJson = $this->client->get("episode/$episodeIds")->getBody()->getContents();
                Cache::remember($cacheKey, $responseJson, 5);
            } else {
                $responseJson = Cache::get($cacheKey);
            }

            $responseEpisodes = json_decode($responseJson);
            foreach ($responseEpisodes as $episode) {
                $episodes[$episode->id] = $episode->name;
            }

            return $episodes;
        } catch (GuzzleException $e) {
            return [];
        }
    }

    public function setCharacter(stdClass $character, array $episodes): Character
    {
        $characterEps = [];
        foreach ($character->episode as $episodeUrl) {
            $eId = basename($episodeUrl);
            $characterEps[$eId] = $episodes[$eId];
        }

        return new Character(
            $character->id,
            $character->name,
            $character->status,
            $character->species,
            $character->type,
            $character->gender,
            $character->origin,
            $character->location,
            $character->image,
            $characterEps
        );
    }

    public function allLocations(?array $ids = null): array
    {
        try {
            $characters = $this->listOfCharacters();

            $locRange = $ids
                ? (count($ids) === 1 ? $ids[0] : implode(',', $ids))
                : implode(',', range(1, 126));

            $locations = [];

            $cacheKey = md5('locations');
            if (!Cache::has($cacheKey)) {
                $responseJson = $this->client->get("location/$locRange")->getBody()->getContents();
                Cache::remember($cacheKey, $responseJson, 5);
            } else {
                $responseJson = Cache::get($cacheKey);
            }

            $response = json_decode($responseJson);
            foreach ($response as $location) {
                $locations[$location->id] = $this->setLocation($location, $characters);
            }

            return $locations;
        } catch (GuzzleException $e) {
            return [];
        }
    }

    function listOfCharacters(): array
    {
        try {
            $charRange = implode(',', range(1, 826));
            $characters = [];

            $cacheKey = md5('characters');
            if (!Cache::has($cacheKey)) {
                $responseJson = $this->client->get("character/$charRange")->getBody()->getContents();
                Cache::remember($cacheKey, $responseJson, 5);
            } else {
                $responseJson = Cache::get($cacheKey);
            }

            $response = json_decode($responseJson);
            foreach ($response as $character) {
                $characters[$character->id] = $character->name;
            }

            return $characters;
        } catch (GuzzleException $e) {
            return [];
        }
    }

    public function setLocation(stdClass $location, array $characters): Location
    {
        $locationChars = [];
        foreach ($location->residents as $url) {
            $charId = basename($url);
            $locationChars[$charId] = $characters[$charId];
        }

        return new Location(
            $location->id,
            $location->name,
            $location->type,
            $location->dimension,
            $locationChars
        );
    }

    public function allEpisodes(?array $ids = null): array
    {
        try {
            $characters = $this->listOfCharacters();
            $episodes = [];

            $epRange = $ids
                ? implode(',', $ids)
                : implode(',', range(1, 51));

            $cacheKey = md5('episodes');
            if (!Cache::has($cacheKey)) {
                $responseJson = $this->client->get("episode/$epRange")->getBody()->getContents();
                Cache::remember($cacheKey, $responseJson, 5);
            } else {
                $responseJson = Cache::get($cacheKey);
            }

            $response = json_decode($responseJson);
            foreach ($response as $episode) {
                $episodes[$episode->id] = $this->setEpisode($episode, $characters);
            }

            return $episodes;
        } catch (GuzzleException $e) {
            return [];
        }
    }

    public function setEpisode(stdClass $episode, array $characters): Episode
    {
        $episodeChars = [];
        foreach ($episode->characters as $url) {
            $charId = basename($url);
            $episodeChars[$charId] = $characters[$charId];
        }

        return new Episode(
            $episode->id,
            $episode->name,
            $episode->air_date,
            $episode->episode,
            $episodeChars
        );
    }

    public function singleCharacter(string $id): ?Character
    {
        try {
            $episodes = $this->listOfEpisodes();

            $cacheKey = md5('character');
            if (!Cache::has($cacheKey)) {
                $responseJson = $this->client->get("character/$id")->getBody()->getContents();
                Cache::remember($cacheKey, $responseJson, 5);
            } else {
                $responseJson = Cache::get($cacheKey);
            }

            $response = json_decode($responseJson);
            return $this->setCharacter($response, $episodes);
        } catch (GuzzleException $e) {
            return null;
        }
    }

    public function singleLocation(string $id): ?Location
    {
        try {
            $characters = $this->listOfCharacters();

            $cacheKey = md5('location');
            if (!Cache::has($cacheKey)) {
                $responseJson = $this->client->get("location/$id")->getBody()->getContents();
                Cache::remember($cacheKey, $responseJson, 5);
            } else {
                $responseJson = Cache::get($cacheKey);
            }

            $response = json_decode($responseJson);
            return $this->setLocation($response, $characters);
        } catch (GuzzleException $e) {
            return null;
        }
    }

    public function singleEpisode(string $id): ?Episode
    {
        try {
            $characters = $this->listOfCharacters();

            $cacheKey = md5('episode');
            if (!Cache::has($cacheKey)) {
                $responseJson = $this->client->get("episode/$id")->getBody()->getContents();
                Cache::remember($cacheKey, $responseJson, 5);
            } else {
                $responseJson = Cache::get($cacheKey);
            }

            $response = json_decode($responseJson);
            return $this->setEpisode($response, $characters);
        } catch (GuzzleException $e) {
            return null;
        }
    }

    public function filteredCharacters($query): array
    {
        $episodes = $this->listOfEpisodes();
        $charactersArray = [];

        $uri = "character/?$query";

        $cacheKey = $query;
        if (!Cache::has($cacheKey)) {
            $responseJson = $this->client->get($uri)->getBody()->getContents();
            Cache::remember($cacheKey, $responseJson, 120);
        } else {
            $responseJson = Cache::get($cacheKey);
        }

        $response = json_decode($responseJson);
        $characters = $response->results;

        foreach ($characters as $character) {
            $charactersArray[$character->id] = $this->setCharacter($character, $episodes);
        }

        return $charactersArray;
    }

    public function filteredEpisodes(string $query): array
    {
        try {
            $characters = $this->listOfCharacters();
            $episodes = [];

            $uri = "episode/?$query";

            $cacheKey = $query;
            if (!Cache::has($cacheKey)) {
                $responseJson = $this->client->get($uri)->getBody()->getContents();
                Cache::remember($cacheKey, $responseJson, 120);
            } else {
                $responseJson = Cache::get($cacheKey);
            }

            $response = json_decode($responseJson);
            foreach ($response->results as $episode) {
                $episodes[$episode->id] = $this->setEpisode($episode, $characters);
            }
            return $episodes;
        } catch (GuzzleException $e) {
            return [];
        }
    }

    public function filteredLocations(string $query): array
    {
        try {
            $characters = $this->listOfCharacters();
            $locations = [];

            $uri = "location/?$query";

            $cacheKey = $query;
            if (!Cache::has($cacheKey)) {
                $responseJson = $this->client->get($uri)->getBody()->getContents();
                Cache::remember($cacheKey, $responseJson, 120);
            } else {
                $responseJson = Cache::get($cacheKey);
            }

            $response = json_decode($responseJson);
            foreach ($response->results as $location) {
                $locations[$location->id] = $this->setLocation($location, $characters);
            }

            return $locations;
        } catch (GuzzleException $e) {
            return [];
        }
    }

}