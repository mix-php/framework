<?php

namespace mix\http;

use mix\base\Component;

/**
 * Response组件基类
 * @author 刘健 <coder.liu@qq.com>
 */
class BaseResponse extends Component
{

    // 格式值
    const FORMAT_HTML = 'html';
    const FORMAT_JSON = 'json';
    const FORMAT_JSONP = 'jsonp';
    const FORMAT_XML = 'xml';
    const FORMAT_RAW = 'raw';

    // 默认输出格式
    public $defaultFormat = self::FORMAT_HTML;

    /**
     * @var \mix\http\Json
     */
    public $json;

    /**
     * @var \mix\http\Jsonp
     */
    public $jsonp;

    /**
     * @var \mix\http\Xml
     */
    public $xml;

    // 当前输出格式
    public $format;

    // 状态码
    public $statusCode = 200;

    // 内容
    public $content = '';

    // HTTP 响应头
    public $headers = [];

    // 是否已经发送
    protected $_isSent = false;

    // 设置Header信息
    public function setHeader($key, $value)
    {
        $this->headers[$key] = $value;
    }

    // 预处理
    protected function prepare()
    {
        $content = $this->content;
        // 空转换
        is_null($content) and $content = '';
        // 数组转换
        if (is_array($content)) {
            switch ($this->format) {
                case self::FORMAT_JSON:
                    $content = $this->json->encode($content);
                    break;
                case self::FORMAT_JSONP:
                    $content = $this->jsonp->encode($content);
                    break;
                case self::FORMAT_XML:
                    $content = $this->xml->encode($content);
                    break;
            }
        }
        // 设置 Header 信息
        switch ($this->format) {
            case self::FORMAT_HTML:
                $this->setHeader('Content-Type', 'text/html; charset=utf-8');
                break;
            case self::FORMAT_JSON:
                $this->setHeader('Content-Type', 'application/json; charset=utf-8');
                break;
            case self::FORMAT_JSONP:
                $this->setHeader('Content-Type', 'application/json; charset=utf-8');
                break;
            case self::FORMAT_XML:
                $this->setHeader('Content-Type', 'text/xml; charset=utf-8');
                break;
        }
        // 修改内容
        $this->content = $content;
    }

}
