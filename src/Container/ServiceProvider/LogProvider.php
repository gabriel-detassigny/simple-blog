<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Container\ServiceProvider;

use Exception;
use GabrielDeTassigny\SimpleContainer\ServiceProvider;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

class LogProvider implements ServiceProvider
{
    const LOG_PATH = '/../../../logs/';

    /** @var string */
    private $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return LoggerInterface
     * @throws Exception
     */
    public function getService(): object
    {
        $log = new Logger($this->name);
        $streamHandler = new StreamHandler(__DIR__  . self::LOG_PATH . $this->name . '.log', Logger::WARNING);
        $log->pushHandler($streamHandler);

        return $log;
    }
}