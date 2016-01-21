<?php

namespace MetricBundle\EventListener;

use MetricBundle\Client\Client;
use Symfony\Component\HttpKernel\Event\FinishRequestEvent;

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
     * @param FinishRequestEvent $event
     */
    public function onKernelTerminate(FinishRequestEvent $event)
    {
        if ($event->isMasterRequest()) {
            if ($this->enableCollector) {
                $this->collectData($event);
            }

            $this->client->send();
        }
    }

    /**
     * @param FinishRequestEvent $event
     */
    protected function collectData(FinishRequestEvent $event)
    {
        $request   = $event->getRequest();

        $this->client->add('plplp', [
            'memory'         => $this->getMemoryPeak(),
            'execution_time' => $this->getExecutionTime($event),
        ], [
            'action' => $request->attributes->get('_controller'),
            'method' => $request->getMethod(),
            'client_ip' => $request->getClientIp(),
            'scheme' => $request->getScheme(),
            'uri' => $request->getUri(),
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
     * @param FinishRequestEvent $event
     *
     * @return float
     */
    protected function getExecutionTime(FinishRequestEvent $event)
    {
        $request   = $event->getRequest();
        $startTime = $request->server->get('REQUEST_TIME_FLOAT', $request->server->get('REQUEST_TIME'));
        $time      = microtime(true) - $startTime;
        return round($time * 1000);
    }
}
