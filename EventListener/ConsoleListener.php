<?php

namespace MetricBundle\EventListener;

use MetricBundle\Client\Client;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;

/**
 * Class ConsoleListener.
 */
class ConsoleListener
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * ConsoleListener constructor.
     *
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param ConsoleTerminateEvent $event
     */
    public function onTerminate(ConsoleTerminateEvent $event)
    {
        $this->client->send();
    }
}
