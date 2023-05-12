<?php

declare(strict_types=1);

namespace RickAndMorty\Controllers;

use RickAndMorty\Core\TwigView;
use RickAndMorty\Services\RickAndMortyClient;

//Not suggested to load twig here!
class Controller
{
    public function characters(): TwigView
    {
        $rickAndMortyClient = new RickAndMortyClient();
        $chars = $rickAndMortyClient->allCharacters();

        if (empty($chars)) {
            return new TwigView('404', []);
        }

        $totalChars = count($chars);
        $limit = 9;
        $currentPage = $_GET['page'] ?? 1;
        $numPages = ceil($totalChars / $limit);

        $params = [
            'Chars' => $chars,
            'current_page' => $currentPage,
            'num_pages' => $numPages,
            'limit' => $limit,
            'total_chars' => $totalChars,
        ];

        return new TwigView('characters', $params);
    }
    public function locations(): TwigView
    {
        $rickAndMortyClient = new RickAndMortyClient();
        $locations = $rickAndMortyClient->allLocations();

        if (empty($locations)) {
            return new TwigView('404', []);
        }

        $totalLocations = count($locations);
        $limit = 9;
        $currentPage = $_GET['page'] ?? 1;
        $numPages = ceil($totalLocations / $limit);

        $params = [
            'locations' => $locations,
            'currentPage' => $currentPage,
            'numPages' => $numPages,
            'limit' => $limit,
            'totalLocations' => $totalLocations,
        ];

        return new TwigView('locations', $params);
    }
    public function episodes(): TwigView
    {
        $rickAndMortyClient = new RickAndMortyClient();
        $episodes = $rickAndMortyClient->allEpisodes();

        if (empty($episodes)) {
            return new TwigView('404', []);
        }

        $totalEpisodes = count($episodes);
        $limit = 9;
        $currentPage = $_GET['page'] ?? 1;
        $numPages = ceil($totalEpisodes / $limit);

        $params = [
            'episodes' => $episodes,
            'currentPage' => $currentPage,
            'numPages' => $numPages,
            'limit' => $limit,
            'totalEpisodes' => $totalEpisodes,
        ];

        return new TwigView('episodes', $params);
    }
    public function character($vars): TwigView
    {
        $id = $vars['id'];
        $rickAndMortyClient = new RickAndMortyClient();
        $char = $rickAndMortyClient->singleCharacter($id);

        if ($char !== null) {
            $params = [
                'char' => $char,
            ];

            return new TwigView('character', $params);
        }

        return new TwigView('404', []);
    }
    public function episode($vars): TwigView
    {
        $id = $vars['id'];
        $rickAndMortyClient = new RickAndMortyClient();
        $episode = $rickAndMortyClient->singleEpisode($id);

        if ($episode !== null) {
            $episodeCharacterIds = $episode->getCharacterIds();
            $charsFromEpisode = $rickAndMortyClient->allCharacters($episodeCharacterIds);
            $params = [
                'episode' => $episode,
                'chars' => $charsFromEpisode,
            ];

            return new TwigView('episode', $params);
        }

        return new TwigView('404', []);
    }
    public function location($vars): TwigView
    {
        $id = $vars['id'];
        $rickAndMortyClient = new RickAndMortyClient();
        $location = $rickAndMortyClient->singleLocation($id);
        $episodeCharacterIds = $location->getResidentsIds();

        if (count($episodeCharacterIds) === 0) {
            $charsFromEpisode = [];
        } elseif (count($episodeCharacterIds) === 1) {
            $charId = (string) $episodeCharacterIds[0];
            $charsFromEpisode = $rickAndMortyClient->singleCharacter($charId);
        } else {
            $charsFromEpisode = $rickAndMortyClient->allCharacters($episodeCharacterIds);
        }

        $params = [
            'location' => $location,
            'chars' => $charsFromEpisode,
        ];

        return new TwigView('location', $params);
    }
    public function filteredCharacters(): TwigView
    {
        $name = $_GET['name'] ?? null;
        $status = $_GET['status'] ?? null;
        $species = $_GET['species'] ?? null;
        $type = $_GET['type'] ?? null;
        $gender = $_GET['gender'] ?? null;

        $query = http_build_query([
            'name' => $name,
            'status' => $status,
            'species' => $species,
            'type' => $type,
            'gender' => $gender
        ]);

        $rickAndMortyClient = new RickAndMortyClient();
        $chars = $rickAndMortyClient->filteredCharacters($query);

        if (!empty($chars)) {
            $params = [
                'Chars' => $chars,
            ];
            return new TwigView('characters', $params);
        }

        return new TwigView('404', []);
    }
    public function filteredLocations(): TwigView
    {
        $name = $_GET['name'] ?? null;
        $type = $_GET['type'] ?? null;
        $dimension = $_GET['dimension'] ?? null;

        $query = http_build_query([
            'name' => $name,
            'type' => $type,
            'dimension' => $dimension
        ]);

        $rickAndMortyClient = new RickAndMortyClient();
        $locations = $rickAndMortyClient->filteredLocations($query);

        if (!empty($locations)) {
            $params = [
                'locations' => $locations,
            ];
            return new TwigView('locations', $params);
        }

        return new TwigView('404', []);
    }
    public function filteredEpisodes(): TwigView
    {
        $name = $_GET['name'] ?? null;
        $episode = $_GET['episode'] ?? null;

        $query = http_build_query([
            'name' => $name,
            'episode' => $episode,
        ]);

        $rickAndMortyClient = new RickAndMortyClient();
        $episodes = $rickAndMortyClient->filteredEpisodes($query);

        if (!empty($episodes)) {
            $params = [
                'episodes' => $episodes,
            ];
            return new TwigView('episodes', $params);
        }

        return new TwigView('404', []);
    }

}