# Стандартные операции с обновлением списка продуктов в корзине

Для наиболее часто используемых операций связанных с изменением состава корзины реализован хелпер ProductListHelper.

Хелпер является обёрткой над универсальными методами отправки произвольного запроса.

## Примеры использования ProductListHelper

[Добавление продукта в корзину](https://developers.mindbox.ru/docs/prodlistactionxml):

``` php

/* Инициализация SDK */

$addProductToListRequest = new \Mindbox\DTO\V3\Requests\AddProductToListRequestDTO();

$product = new Mindbox\DTO\V3\Requests\ProductRequestDTO();
$product->setName('name');

/* Формирование данных о продукте */

$addProductToListRequest->setProduct($product);

try {
    $response = $mindbox->productList()
        ->addToCart(
            $addProductToListRequest, // AddProductToListRequestDTO
            'Website.AddToCart', // название операции
            false // добавлять ли DeviceUUID в запрос, необязательный параметр
        )->sendRequest();
        
    $requestBody = $response->getRequest()->getBody();
    $responseBody = $response->getBody();
} catch (\Mindbox\Exceptions\MindboxClientException $e) {
    echo $e->getMessage();
}
```

[Удаление продукта из корзины](https://developers.mindbox.ru/docs/prodlistactionxml):

``` php

/* Инициализация SDK */

$removeProductFromListRequest = new \Mindbox\DTO\V3\Requests\RemoveProductFromListRequestDTO();

$product = new Mindbox\DTO\V3\Requests\ProductRequestDTO();
$product->setName('name');

/* Формирование данных о продукте */

$removeProductFromListRequest->setProduct($product);

try {
    $response = $mindbox->productList()
        ->removeFromCart(
            $removeProductFromListRequest, // RemoveProductFromListRequestDTO
            'Website.RemoveFromCart', // название операции
            false // добавлять ли DeviceUUID в запрос, необязательный параметр
        )->sendRequest();
        
    $requestBody = $response->getRequest()->getBody();
    $responseBody = $response->getBody();
} catch (\Mindbox\Exceptions\MindboxClientException $e) {
    echo $e->getMessage();
}
```

[Изменение количества продукта в корзине](https://developers.mindbox.ru/docs/prodlistactionxml):

``` php

/* Инициализация SDK */

$setProductCountInListRequest = new \Mindbox\DTO\V3\Requests\SetProductCountInListRequestDTO();

$product = new Mindbox\DTO\V3\Requests\ProductRequestDTO();
$product->setName('name');

/* Формирование данных о продукте */

$setProductCountInListRequest->setProduct($product);

try {
    $response = $mindbox->productList()
        ->setProductCount(
            $setProductCountInListRequest, // SetProductCountInListRequestDTO
            'Website.SetProductCount', // название операции
            false // добавлять ли DeviceUUID в запрос, необязательный параметр
        )->sendRequest();
    
    $requestBody = $response->getRequest()->getBody();
    $responseBody = $response->getBody();
} catch (\Mindbox\Exceptions\MindboxClientException $e) {
    echo $e->getMessage();
}
```

[Установка состава корзины](https://developers.mindbox.ru/docs/prodlistactionxml):

``` php

/* Инициализация SDK */

$product = new Mindbox\DTO\V3\Requests\ProductListItemRequestDTO();
$product->setCount(6);

$products[] = $product;

/* Формирование данных о составе корзины */

$products = new \Mindbox\DTO\V3\Requests\ProductListItemRequestCollection($products);

$customerIdentity = new \Mindbox\DTO\V3\Requests\CustomerIdentityRequestDTO();
$customerIdentity->setId('mindboxId', 'some_mindboxId');

try {
    $response = $mindbox->productList()
        ->setProductList(
            $products, // ProductListItemRequestCollection
            'Website.SetProductList', // название операции
            $customerIdentity, // идентификаторы потребителя CustomerIdentityRequestDTO, необязательный параметр
            false // добавлять ли DeviceUUID в запрос, необязательный параметр
        )->sendRequest();
    
    $requestBody = $response->getRequest()->getBody();
    $responseBody = $response->getBody();
} catch (\Mindbox\Exceptions\MindboxClientException $e) {
    echo $e->getMessage();
}
```