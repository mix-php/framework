<?php

namespace mix\client;

use mix\base\BaseObject;

/**
 * Http类
 * @author 刘健 <coder.liu@qq.com>
 */
class HttpCoroutine extends BaseObject
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
        return $this->execute($url, 'GET');
    }

    // POST 请求
    public function post($url, $body)
    {
        return $this->execute($url, 'POST', $body);
    }

    // 执行请求
    protected function execute($url, $method, $body = null)
    {
        // 构建请求参数
        $urlInfo     = self::parseUrl($url);
        $requestBody = self::buildRequestBody($body);
        // 构造请求
        $http = new \Swoole\Coroutine\Http\Client($urlInfo['host'], $urlInfo['port'], $urlInfo['scheme'] == 'https');
        // 参数配置
        isset($this->timeout) and $http->set(['timeout' => $this->timeout]);
        empty($this->headers) or $http->setHeaders($this->headers);
        empty($this->cookies) or $http->setCookies($this->cookies);
        // 执行请求
        if ($method == 'GET') {
            $http->get($urlInfo['fullpath']);
        }
        if ($method == 'POST') {
            $http->post($urlInfo['fullpath'], $body);
        }
        // 获取响应数据
        $this->_requestUrl     = $url;
        $this->_requestMethod  = $method;
        $this->_requestHeaders = $http->requestHeaders;
        $this->_requestBody    = $requestBody;
        $this->_error          = $http->errCode == 0 ? '' : socket_strerror($http->errCode);
        $this->_statusCode     = $http->statusCode;
        $this->_headers        = $http->headers;
        $this->_body           = $http->body;
        // 关闭请求
        $http->close();
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
        $requestHeaders = $this->_requestHeaders;
        $cookies        = $this->cookies;
        if (!empty($cookies)) {
            $tmpCookies = [];
            foreach ($cookies as $key => $value) {
                $tmpCookies[] = "{$key}={$value}";
            }
            $requestHeaders['Cookie'] = implode('; ', $tmpCookies);
        }
        return $requestHeaders;
    }

    // 返回响应包体
    public function getRequestBody()
    {
        return $this->_requestBody;
    }

    // 返回响应头
    public function getHeaders()
    {
        return $this->_headers;
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

    // 构建请求路径
    protected static function parseUrl($url)
    {
        $urlInfo             = parse_url($url);
        $path                = isset($urlInfo['path']) ? $urlInfo['path'] : '';
        $query               = isset($urlInfo['query']) ? '?' . $urlInfo['query'] : '';
        $fragment            = isset($urlInfo['fragment']) ? '#' . $urlInfo['fragment'] : '';
        $urlInfo['port']     = isset($urlInfo['port']) ? $urlInfo['port'] : 80;
        $urlInfo['fullpath'] = "{$path}{$query}{$fragment}";
        return $urlInfo;
    }

    // 构建请求正文
    protected static function buildRequestBody($data)
    {
        if (empty($data)) {
            return '';
        }
        return http_build_query($data);
    }

}
