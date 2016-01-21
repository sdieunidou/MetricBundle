# Configuration

Add in `config_prod.yml`

```
metric:
    host: %app_metric_host%
    port: %app_metric_port%
    enable_collector: true # set true to collect data (request, execution time, memory, ..)
```

# Usage

```
$this->get('metric')->add('app_queue', [
    'value'     => 1,
]);
```

## Add tags

```
$this->get('metric')->add('app_queue', [
    'value'     => 1,
], [
    'region' => 'eu',
]);
```
