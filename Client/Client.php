<?php

namespace MetricBundle\Client;

use InfluxDB\Adapter\WritableInterface as Writer;

/**
 * Class Client.
 */
class Client
{
    /**
     * @var Writer
     */
    protected $writer;

    /**
     * @var array
     */
    protected $buffer;

    /**
     * Client constructor.
     *
     * @param Writer $writer
     */
    public function __construct(Writer $writer)
    {
        $this->writer = $writer;
        $this->buffer = [];
    }

    /**
     * @return Writer
     */
    public function getWriter()
    {
        return $this->writer;
    }

    /**
     * Send buffer
     */
    public function send()
    {
        $buffer = $this->buffer;
        $this->buffer = [];
        foreach ($buffer as $data) {
            $this->getWriter()->send($data);
        }
    }

    /**
     * @param string $name
     * @param array $tags
     */
    public function increment($name, array $tags = [])
    {
        $this->add($name, [
            'value' => 1
        ], $tags);
    }

    /**
     * @param string $name
     * @param array $tags
     */
    public function decrement($name, array $tags = [])
    {
        $this->add($name, [
            'value' => -1
        ], $tags);
    }

    /**
     * @param string $name
     * @param int $time
     * @param array $tags
     */
    public function timing($name, $time = null, array $tags = [])
    {
        if (null !== $time) {
            $time = time();
        }
        
        $this->add($name, [
            'value' => $time
        ], $tags);
    }

    /**
     * @param string $name
     * @param $value
     * @param array $tags
     */
    public function measure($name, $value, array $tags = [])
    {
        $this->add($name, [
            'value' => $value
        ], $tags);
    }

    /**
     * Add metric to buffer
     *
     * @param $name
     * @param array $values
     * @param array $tags
     */
    public function add($name, array $values = [], array $tags = [])
    {
        $data = $name;

        if (!is_array($name)) {
            $data =[];
            $data['points'][0]['measurement'] = $name;
            $data['points'][0]['fields'] = $values;
        }

        if (!empty($tags) && is_array($tags)) {
            if (empty($data['tags'])) {
                $data['tags'] = $tags;
            } else {
                $data['tags'] = array_merge($data['tags'], $tags);
            }
        }

        $this->buffer[] = $data;
    }
}
