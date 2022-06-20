# Подготовка и отправка запроса к Mindbox API v2.1

Вы можете отправить произвольный запрос к Mindbox API v2.1.
Параметры запроса:
* method - HTTP метод запроса: 'POST', 'GET';
* operationName - название операции в Mindbox;
* body - тело запроса в виде DTO, необязательный параметр;
* additionalUrl - дополнительный URL, конкатенируется с базовым URL: https://{{domain}}/v2.1/orders/{additionalUrl}, необязательный параметр;
* queryParams - массив дополнительных GET параметров, необязательный параметр.

Базовый DTO для формирования тела запроса к v2.1 - \Mindbox\DTO\V2\Requests\OrderRequestDTO() и его потомки:
* OrderCreateRequestDTO;
* OrderUpdateRequestDTO;
* PreorderRequestDTO.

Запросы к Mindbox API v2.1 отправляются в формате XML, в кодировке UTF-8.

## Пример отправки произвольного запроса к API v2.1

``` php
$logger = new \Mindbox\Loggers\MindboxFileLogger('{logsDir}');

$mindbox = new \Mindbox\Mindbox([
    'endpointId'   => '{endpointId}',
    'secretKey'    => '{secretKey}',
    'domain'       => '{domain}',
], $logger);

$customer = new \Mindbox\DTO\V2\Requests\CustomerRequestDTO();
$customer->setEmail('test@test.ru');
$customer->setMobilePhone('79374134389');
$customer->setId('bitrixId', '123456');
$customer->setId('mindboxId', '1028');

$order = new \Mindbox\DTO\V2\Requests\OrderCreateRequestDTO();
$order->setCustomer($customer);

/* Формирование состава заказа */

try {
    $response = $mindbox->getClientV2()
        ->prepareRequest('POST', 'Website.CreateOrder', $order, 'create')
        ->sendRequest();
} catch (\Mindbox\Exceptions\MindboxClientException $e) {
    echo $e->getMessage();

    return;
}

var_dump($response->getRequest());
var_dump($response->getBody());
```