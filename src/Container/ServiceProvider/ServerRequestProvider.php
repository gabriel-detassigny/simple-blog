<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Container\ServiceProvider;

use GuzzleHttp\Psr7\ServerRequest;
use Psr\Http\Message\ServerRequestInterface;

class ServerRequestProvider implements ServiceProvider
{
    /**
     * @return ServerRequestInterface
     */
    public function getService()
    {
        return ServerRequest::fromGlobals();
    }
}