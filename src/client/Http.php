<?php

namespace mix\client;

use mix\base\BaseObject;

/**
 * Http类
 * @author 刘健 <coder.liu@qq.com>
 */
class Http extends BaseObject
{

    // 超时时间
    public $timeout;

    // 请求头
    public $headers;

    // cookies
    public $cookies;

    // 请求 URL
    protected $_requestUrl;

    // 请求方法
    protected $_requestMethod;

    // 请求头
    protected $_requestHeaders;

    // 请求包体
    protected $_requestBody;

    // 响应头
    protected $_headers;

    // 响应包体
    protected $_body;

    // Http状态码
    protected $_statusCode;

    // 错误信息
    protected $_error;

    // 设置请求头
    public function setHeader($key, $value)
    {
        $this->headers[$key] = $value;
    }

    // 设置 Cookie
    public function setCookie($key, $value)
    {
        $this->cookies[$key] = $value;
    }

    // GET 请求
    public function get($url)
    {
        return $this->request($url, 'GET');
    }

    // POST 请求
    public function post($url, $body)
    {
        return $this->request($url, 'POST', $body);
    }

    // 执行请求
    public function request($url, $method, $body = null)
    {
        // 构建请求参数
        $requestHeaders = self::buildRequestHeaders($this->headers);
        $requestCookies = self::buildRequestCookies($this->cookies);
        $requestBody    = self::buildRequestBody($body);
        // 构造请求
        $ch = curl_init();
        // 基础配置
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        // 请求配置
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        // 参数配置
        isset($this->timeout) and curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        empty($requestHeaders) or curl_setopt($ch, CURLOPT_HTTPHEADER, $requestHeaders);
        empty($requestCookies) or curl_setopt($ch, CURLOPT_COOKIE, $requestCookies);
        empty($requestBody) or curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody);
        // 忽略SSL
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        // 响应数据
        $response              = curl_exec($ch);
        $headerSize            = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $this->_requestUrl     = $url;
        $this->_requestMethod  = $method;
        $this->_requestHeaders = trim(curl_getinfo($ch)['request_header']);
        $this->_requestBody    = $requestBody;
        $this->_error          = curl_error($ch);
        $this->_statusCode     = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $this->_headers        = trim(substr($response, 0, $headerSize));
        $this->_body           = substr($response, $headerSize);
        // 关闭请求
        curl_close($ch);
        // 返回
        return empty($this->_error) ? true : false;
    }

    // 返回请求 URL
    public function getRequestUrl()
    {
        return $this->_requestUrl;
    }

    // 返回请求方法
    public function getRequestMethod()
    {
        return $this->_requestMethod;
    }

    // 返回响应头
    public function getRequestHeaders()
    {
        return self::headersStringToArray($this->_requestHeaders);
    }

    // 返回响应包体
    public function getRequestBody()
    {
        return $this->_requestBody;
    }

    // 返回响应头
    public function getHeaders()
    {
        return self::headersStringToArray($this->_headers);
    }

    // 返回响应包体
    public function getBody()
    {
        return $this->_body;
    }

    // 返回Http状态码
    public function getStatusCode()
    {
        return $this->_statusCode;
    }

    // 返回错误信息
    public function getError()
    {
        return $this->_error;
    }

    // 构建请求正文
    protected static function buildRequestBody($data)
    {
        if (empty($data)) {
            return '';
        }
        return http_build_query($data);
    }

    // 构建请求头
    protected static function buildRequestHeaders($data)
    {
        $headers = [];
        if (empty($data)) {
            return $headers;
        }
        foreach ($data as $key => $value) {
            $headers[] = "{$key}: {$value}";
        }
        return $headers;
    }

    // 构建请求的 Cookies
    protected static function buildRequestCookies($data)
    {
        if (empty($data)) {
            return '';
        }
        return http_build_query($data, '', ';');
    }

    // 头信息字符转数组
    protected static function headersStringToArray($headersString)
    {
        $headersString = explode("\n", $headersString);
        array_shift($headersString);
        $headerArray = [];
        foreach ($headersString as $header) {
            list($key, $value) = explode(':', $header);
            $headerArray[$key] = trim($value);
        }
        return $headerArray;
    }

}
