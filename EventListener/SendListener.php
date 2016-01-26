<?php

namespace MetricBundle\EventListener;

use MetricBundle\Client\Client;
use Symfony\Component\HttpKernel\Event\PostResponseEvent;

/**
 * Class SendListener.
 */
class SendListener
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var bool
     */
    protected $enableCollector;

    /**
     * SendListener constructor.
     *
     * @param Client $client
     * @param bool $enableCollector
     */
    public function __construct(Client $client, $enableCollector = true)
    {
        $this->client = $client;
        $this->enableCollector = $enableCollector;
    }

    /**
     * @param PostResponseEvent $event
     */
    public function onKernelTerminate(PostResponseEvent $event)
    {
        if ($event->isMasterRequest()) {
            if ($this->enableCollector) {
                $this->collectData($event);
            }
        }
        $this->client->send();
    }

    /**
     * @param PostResponseEvent $event
     */
    protected function collectData(PostResponseEvent $event)
    {
        $request   = $event->getRequest();
        $this->client->add('app.profiler', [
            'memory'         => $this->getMemoryPeak(),
            'execution_time' => $this->getExecutionTime($event),
        ], [
            'method' => $request->server->get('REQUEST_METHOD'),
            'client_ip' => $request->server->get('REMOTE_ADDR'),
            'https' => $request->server->get('HTTPS'),
            'uri' => $request->server->get('DOCUMENT_URI'),
            'host' => $request->server->get('HTTP_HOST'),
        ]);
    }

    /**
     * @return int
     */
    protected function getMemoryPeak()
    {
        return memory_get_peak_usage(true);
    }

    /**
     * @param PostResponseEvent $event
     *
     * @return float
     */
    protected function getExecutionTime(PostResponseEvent $event)
    {
        $request   = $event->getRequest();
        $startTime = $request->server->get('REQUEST_TIME_FLOAT', $request->server->get('REQUEST_TIME'));
        $time      = microtime(true) - $startTime;
        return round($time * 1000);
    }
}
