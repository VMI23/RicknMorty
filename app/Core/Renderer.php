<?php

declare(strict_types=1);

namespace RickAndMorty\Core;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Renderer
{
    private Environment $twig;

    public function __construct(string $basePath)
    {
        $loader = new FilesystemLoader($basePath);
        $this->twig = new Environment($loader);
    }

    public function render(TwigView $view)
    {
        return $this->twig->render($view->getPath() . '.twig', $view->getData());
    }
}