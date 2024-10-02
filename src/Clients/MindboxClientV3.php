<?php


namespace Mindbox\Clients;

use Mindbox\HttpClients\IHttpClient;
use Psr\Log\LoggerInterface;

/**
 * Клиент для отправки запросов к v3 API Mindbox.
 * Class MindboxClientV3Api
 *
 * @package Mindbox\Clients
 */
class MindboxClientV3 extends AbstractMindboxClient
{
    /**
     * Версия API Mindbox с которой работает клиент.
     */
    const API_VERSION = 'v3';

    /**
     * Базовый URL на который будут отправляться запросы.
     */
    const BASE_V3_URL = 'https://{{url}}/v3/operations/';

    /**
     * Имя схемы авторизации по секретному ключу.
     */
    const SECRET_KEY_AUTHORIZATION_SCHEME_NAME = 'SecretKey';

    /**
     * @var string URL
     */
    private $url;

    /**
     * @var string Уникальный идентификатор устройства
     */
    private $customer_device_uuid;

    /**
     * @var string IP пользователя
     */
    private $customer_ip;

    /**
     * @var string Уникальный идентификатор сайта/мобильного приложения/и т.п.
     */
    private $endpointId;

    /**
     * Конструктор MindboxRequest.
     *
     * @param string $endpointId Уникальный идентификатор сайта/мобильного приложения/и т.п.
     * @param string $secretKey Секретный ключ.
     * @param IHttpClient $httpClient Экземпляр HTTP клиента.
     * @param LoggerInterface $logger Экземпляр логгера.
     * @param string $domainZone
     * @param string $domain
     */
    public function __construct($endpointId, $secretKey, IHttpClient $httpClient, LoggerInterface $logger, $domainZone, $domain = 'api.mindbox')
    {
        parent::__construct($secretKey, $httpClient, $logger);
        $this->url = $this->getApiUrl($domain, $domainZone);
        $this->endpointId = $endpointId;
    }

    /**
     * Подготовка массива заголовков запроса.
     *
     * @param bool $addDeviceUUID Флаг: добявлять ли в запрос заголовок X-Customer-IP.
     *
     * @return array
     */
    protected function prepareHeaders($addDeviceUUID = true)
    {
        $headers = parent::prepareHeaders();

        if ($addDeviceUUID) {
            $headers = array_merge($headers, [
                'X-Customer-IP' => $this->getCustomerIP(),
            ]);
        }

        return $headers;
    }

    /**
     * Получение IP пользователя.
     *
     * @return string
     */
    private function getCustomerIP()
    {
        if (is_null($this->customer_ip)) {
            return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
        }
        return $this->customer_ip;
    }

    /**
     * @param string $customer_ip
     */
    public function setCustomerIp(string $customer_ip)
    {
        $this->customer_ip = $customer_ip;
    }

    /**
     * Подготовка полного URL запроса.
     *
     * @param string $url         Дополнительный URL.
     * @param array  $queryParams GET параметры запроса.
     * @param bool   $isSync      Синхронный/асинхронный запрос.
     *
     * @return string
     */
    protected function prepareUrl($url, array $queryParams, $isSync = true)
    {
        return $this->url . ($isSync ? 'sync' : 'async') . '?' . http_build_query($queryParams);
    }

    /**
     * Подготовка массива GET параметров запроса.
     *
     * @param string $operation     Название операции.
     * @param array  $queryParams   GET параметры, переданные пользователем.
     * @param bool   $addDeviceUUID Флаг: добавлять ли параметр DeviceUUID.
     *
     * @return array
     */
    protected function prepareQueryParams($operation, array $queryParams, $addDeviceUUID = true)
    {
        $queryParams = array_merge($queryParams, [
            'endpointId' => $this->endpointId,
            'operation'  => $operation,
        ]);
        if ($addDeviceUUID) {
            $queryParams['deviceUUID'] = $this->getDeviceUUID();
        }

        return $queryParams;
    }

    /**
     * Получение уникального идентификатора устройства.
     *
     * @return string
     */
    private function getDeviceUUID()
    {
        if (is_null($this->customer_device_uuid)) {
            return isset($_COOKIE['mindboxDeviceUUID']) ? $_COOKIE['mindboxDeviceUUID'] : '';
        }
        return $this->customer_device_uuid;
    }

    /**
     * @param string $customer_device_uuid
     */
    public function setCustomerDeviceUuid(string $customer_device_uuid)
    {
        $this->customer_device_uuid = $customer_device_uuid;
    }

    /**
     * Подготовка заголовка Authorization.
     *
     * @return string
     */
    protected function prepareAuthorizationHeader()
    {
        return static::SECRET_KEY_AUTHORIZATION_SCHEME_NAME . ' ' . $this->secretKey;
    }

    /**
     * Конвертация тела запроса в формат, пригодный для HTTP клиента (json).
     *
     * @param \Mindbox\DTO\DTO|null $body Тело запроса в виде DTO.
     *
     * @return string
     */
    protected function prepareBody(\Mindbox\DTO\DTO $body = null)
    {
        return $body ? $body->toJson() : '';
    }

    /**
     * Конвертация тела ответа из json в массив.
     *
     * @param string $rawBody Сырое тело ответа.
     *
     * @return array
     */
    protected function prepareResponseBody($rawBody)
    {
        return $rawBody ? json_decode($rawBody, true) : [];
    }

    /**
     * Преобразование API URL
     *
     * @return string
     */
    protected function getApiUrl(string $domain, string $domainZone)
    {
        $domainZone = $domainZone === 'api-ru' ? 'cloud' : $domainZone;

        $url = str_replace('{{url}}', $domain . '.' . $domainZone, self:: BASE_V3_URL);

        return $url;
    }
}
