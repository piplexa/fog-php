<?php

namespace Pplexa\Fog;

/**
 * FogClient
 *
 * Класс для работы с Fog API
 *
 * @package Pplexa\Fog
 */
class FogClient
{
    /**
     * @var string
     * URL Fog API
     */
    private $api_url = 'http://api_url';
    /**
     * @var string
     * Токен для доступа к Fog API
     */
    private $fog_api_token = 'your-api';

    /**
     * @var string
     * Токен для доступа к Fog API (для пользователя)
     */
    private $fog_user_token = 'your-user-token';

    /**
     * @var string
     * Логин для доступа к Fog (панель управления)
     */
    private $user = '';

    /**
     * @var string
     * Пароль для доступа к Fog (панель управления)
     */
    private $password = '';

    /**
     * @var array
     * Опции для вызова cURL
     */
    private array $_OPT_CURL = [];
   
    public function __construct(string $api_url, string $fog_api_token = 'your-api', string $fog_user_token = 'your-user-token', string $user = '', string $password = '')
    {
        $this->api_url = rtrim($api_url, '/');
        $this->fog_api_token = $fog_api_token;
        $this->fog_user_token = $fog_user_token;
        $this->user = $user;
        $this->password = $password;

        $this->_OPT_CURL = [
            CURLOPT_URL =>  $this->api_url,
            CURLOPT_HTTPHEADER => [
                'fog-api-token: ' . $this->fog_api_token,
                'fog-user-token: ' . $this->fog_user_token,
            ],
            CURLOPT_RETURNTRANSFER => true
        ];
    }

    /**
     * Вызов метода API
     * @param string $method
     * @param array $params
     * @return string
     * @throws \RuntimeException
     */
    private function _call_api_method( string $method, array $params = []): string
    {
        $ch = curl_init();
        $this->_OPT_CURL[CURLOPT_URL] = $this->api_url . $method;
        curl_setopt_array($ch, $this->_OPT_CURL);
//        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));  // передаем параметры в теле запроса
        $response = curl_exec($ch);
        curl_close($ch);

        if ($response === false) {
            throw new \RuntimeException('Failed to fetch Fog API data');
        }

        return $response;
    }

    /**
     * Проверка работы FOG api
     * @return bool
     */
    public function checkStatusApi (): bool
    {
        $response = $this->_call_api_method('/fog/system/info');

        if ( trim($response) === 'success'){
            return true;
        } else {
            throw new \RuntimeException('API is unavailable');
        }
    }

    /**
     * Список доступных образов
     * @return array
     */
    public function images (): ?array
    {
        $response = $this->_call_api_method('/fog/image/');

        $res = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('Failed to parse image list: invalid JSON response');
        }
        
        return $res;
    }

    /**
     * Список доступных компьютеров
     * @return array
     */
    public function hosts (): array
    {
        $response = $this->_call_api_method('/fog/image/image');

        $res = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('Failed to parse host list: invalid JSON response');
        }
        
        return $res;
    }

    /**
     * Свободное место на нодах
     * @return array
     */
    public function freespace (): array
    {
        return [
            'disk1' => '50GB',
            'disk2' => '100GB',
            'disk3' => '200GB'
        ];
    }

    /**
     * Создание образа выделенного компьютера
     * @param string $host
     * @param string $desc
     * @return bool
     */
    public function capture(string $host, string $desc): bool
    {
        // Логика захвата образа
        return true;
    }

    /**
     * Установка образа на выделенный компьютер
     */
    public function deploy(string $image, string $host): bool
    {
        // Логика развертывания образа
        return true;
    }
    
}