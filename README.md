# MetricBundle

Really simple bundle to add metrics with InfluxDB in your Symfony2 application.

# Installation

```
composer require mtxserv/metric-bundle
```

# Configuration

Add in `config_prod.yml`

```
metric:
    host: %app_metric_host%
    port: %app_metric_port% # UDP port
    enable_collector: true  # set true to collect data (request, execution time, memory, ..)
```

# Usage

```
$this->get('metric')->add('app_queue', [
    'value'     => 1,
]);
```

## Increment serie

```
$this->get('metric')->decrement('app_queue');
```

## Decrement serie

```
$this->get('metric')->increment('app_queue');
```

## Timing serie

```
$this->get('metric')->timing('app_queue', time());
```

## Measure serie

```
$this->get('metric')->measure('app_queue', 10.0);
```

## Add tags

An third argument is available on all methods to add tags.

Example:

```
$this->get('metric')->add('app_queue', [
    'value'     => 1,
], [
    'region' => 'eu',
]);
```

# Data Collector

To enable data collector (send on kernel.terminate event), set to true the enable_collector flag.

```
metric:
    enable_collector: true # set true to collect data (request, execution time, memory, ..)
```
