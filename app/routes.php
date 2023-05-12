<?php

use RickAndMorty\Controllers\Controller;

return [
    ['GET', '/', [Controller::class, 'characters']],
    ['GET', '/locations', [Controller::class, 'locations']],
    ['GET', '/episodes', [Controller::class, 'episodes']],
    ['GET', '/character/{id}', [Controller::class, 'character']],
    ['GET', '/episode/{id}', [Controller::class, 'episode']],
    ['GET', '/location/{id}', [Controller::class, 'location']],
    ['GET', '/filteredchar/', [Controller::class, 'filteredCharacters']],
    ['GET', '/filteredlocations/', [Controller::class, 'filteredLocations']],
    ['GET', '/filteredepisodes/', [Controller::class, 'filteredEpisodes']],
];

