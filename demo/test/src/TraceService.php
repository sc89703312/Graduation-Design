<?php
/**
 * Created by PhpStorm.
 * User: Echo
 * Date: 2018/4/13
 * Time: 10:44
 */

namespace demo\test;

use Log;
use demo\test\utils\RequestInfoUtil;

class TraceService
{
    const REQUEST_FIELD_TRACE_ID = "request_field_trace_id";

    const PRIMARY_KEY = "platform_power_trace_log";

    const VALUE_DEFAULT = "-";

    const STATUS_OK = "OK";
    const STATUS_FAIL = "FAIL";

    const TYPE_HTTP = "HTTP";
    const TYPE_GRPC = "GRPC";

    const FIELD_TRACE_ID = "trace_id";
    const FIELD_CLIENT_SPAN_ID = 'client_span_id';
    const FIELD_SERVER_SPAN_ID = 'server_span_id';
    const FIELD_SPAN_ID = 'span_id';
    const FIELD_CLIENT_NAME = "client_name";
    const FIELD_SERVER_NAME = "server_name";
    const FIELD_REQUEST_TYPE = "request_type";
    const FIELD_REQUEST_METHOD = "request_method";
    const FIELD_REQUEST_PARAMS = "request_params";
    const FIELD_REQUEST_STATUS = "request_status";
    const FIELD_START_TIME = "start_time";
    const FIELD_CLIENT_IP = "client_ip";
    const FIELD_CLIENT_PORT = "client_port";
    const FIELD_SERVER_IP = "server_ip";
    const FIELD_SERVER_PORT = "server_port";
    const FIELD_CLIENT_REQUEST_URI = "client_request_uri";
    const FIELD_SERVER_REQUEST_URI = "server_request_uri";
    const FIELD_USE_TIME = "use_time";
    const FIELD_RESPONSE_CODE = "response_code";
    const FIELD_ERROR_CODE = "error_code";
    const FIELD_ERROR_MESSAGE = "error_message";
    const FIELD_RESPONSE_RESULT = "response_result";
    const FIELD_EXTEND_INFO = "extend_info";


    private $startTime = 0;

    private $traceInfo = [];

    private $uniqueId = 0;

    public function start()
    {
        $this->startTime = $this->microtimeFloat();
    }

    public function dump()
    {
        $logData = [
            self::FIELD_TRACE_ID => $this->traceId(),
            self::FIELD_SERVER_SPAN_ID => $this->spanId(),
            self::FIELD_CLIENT_SPAN_ID => $this->getClientSpanId(),
            self::FIELD_CLIENT_NAME => $this->getClientName(),
            self::FIELD_SERVER_NAME => $this->getServerName(),
            self::FIELD_REQUEST_TYPE => $this->getRequestType(),
            self::FIELD_REQUEST_METHOD => $this->getRequestMethod(),
            self::FIELD_REQUEST_PARAMS => $this->getRequestParams(),
            self::FIELD_REQUEST_STATUS => $this->getRequestStatus(),
            self::FIELD_START_TIME => $this->startTime,
            self::FIELD_CLIENT_IP => $this->getClientIp(),
            self::FIELD_CLIENT_PORT => $this->getClientPort(),
            self::FIELD_SERVER_IP => $this->getServerIp(),
            self::FIELD_SERVER_PORT => $this->getServerPort(),
            self::FIELD_CLIENT_REQUEST_URI => $this->getClientRequestUri(),
            self::FIELD_SERVER_REQUEST_URI => $this->getRequestUri(),
            self::FIELD_USE_TIME => $this->microtimeFloat() - $this->startTime,
            self::FIELD_RESPONSE_CODE => $this->getResponseCode(),
            self::FIELD_ERROR_CODE => $this->getErrorCode(),
            self::FIELD_ERROR_MESSAGE => $this->getErrorMessage(),
            self::FIELD_RESPONSE_RESULT => $this->getResponseResult(),
            self::FIELD_EXTEND_INFO => $this->getExtendInfo()
        ];

        Log::info($logData);
    }


    public function traceId()
    {
        if (!empty($_REQUEST[self::REQUEST_FIELD_TRACE_ID])) {
            $traceId = $_REQUEST[self::REQUEST_FIELD_TRACE_ID];
        } else {
            if (extension_loaded("uuid")) {
                $traceId = \uuid_create();
            } else {
                $traceId = uniqid($this->microtimeFloat());
            }
            $_REQUEST[self::REQUEST_FIELD_TRACE_ID] = $traceId;
        }

        return $traceId;
    }

    public function spanId()
    {
        if(!empty($_REQUEST[self::FIELD_SPAN_ID])) {
            $span_id = $_REQUEST[self::FIELD_SPAN_ID];
        } else {
            $span_id = '0';
            $_REQUEST[self::FIELD_SPAN_ID] = $span_id;
        }
        return $span_id;
    }

    private function microtimeFloat()
    {
        return RequestInfoUtil::getTimes() * 1000;
    }

    public function getClientRequestUri()
    {
        $requestUri = explode("?", $_SERVER["REQUEST_URI"])[0];
        $requestUri = $requestUri ? strtolower($requestUri) : self::VALUE_DEFAULT;
        return $requestUri;
    }

    public function getProjectPort()
    {
        return !empty($_SERVER["SERVER_PORT"]) ? $_SERVER["SERVER_PORT"] : self::VALUE_DEFAULT;
    }

    public function setServerName($serverName)
    {
        $this->traceInfo["server_name"] = $serverName;
    }

    public function getServerName()
    {
        return !empty($this->traceInfo["server_name"]) ? $this->traceInfo["server_name"] : self::VALUE_DEFAULT;
    }

    public function getClientName()
    {
        return config('info.Name');
    }

    public function setServerIp($ip)
    {
        $this->traceInfo["server_ip"] = $ip;
    }

    public function getServerIp()
    {
        return !empty($this->traceInfo["server_ip"]) ? $this->traceInfo["server_ip"] : self::VALUE_DEFAULT;
    }

    public function getClientIp()
    {
        return config('info.address');
    }

    public function setServerPost($port)
    {
        $this->traceInfo["server_port"] = $port;
    }

    public function getServerPort()
    {
        return !empty($this->traceInfo["server_port"]) ? $this->traceInfo["server_port"] : self::VALUE_DEFAULT;
    }

    public function getClientPort()
    {
        return config('info.port');
    }

    public function setRequestUri($requestUri)
    {
        $this->traceInfo["request_uri"] = strtolower($requestUri);
    }

    public function getRequestUri()
    {
        return !empty($this->traceInfo["request_uri"]) ? $this->traceInfo["request_uri"] : self::VALUE_DEFAULT;
    }

    public function setRequestMethod($requestMethod)
    {
        $this->traceInfo["request_method"] = strtoupper($requestMethod);
    }

    public function getRequestMethod()
    {
        return !empty($this->traceInfo["request_method"]) ? $this->traceInfo["request_method"] : self::VALUE_DEFAULT;
    }

    public function setRequestParams($requestParams)
    {
        $this->traceInfo["request_params"] = $requestParams;
    }

    public function getRequestParams()
    {
        return !empty($this->traceInfo["request_params"]) ? $this->traceInfo["request_params"] : self::VALUE_DEFAULT;
    }

    public function setRequestType($requestType)
    {
        $this->traceInfo["request_type"] = $requestType;
    }

    public function getRequestType()
    {
        return !empty($this->traceInfo["request_type"]) ? $this->traceInfo["request_type"] : self::VALUE_DEFAULT;
    }

    public function setExtendInfo($extendInfo)
    {
        $this->traceInfo["extend_info"] = $extendInfo;
    }

    public function getExtendInfo()
    {
        return !empty($this->traceInfo["extend_info"]) ? $this->traceInfo["extend_info"] : self::VALUE_DEFAULT;
    }

    public function setResponseCode($responseCode)
    {
        $this->traceInfo["response_code"] = $responseCode;
    }


    public function getResponseCode()
    {
        return !empty($this->traceInfo["response_code"]) ? $this->traceInfo["response_code"] : self::VALUE_DEFAULT;
    }

    public function setRequestStatus($responseStatus)
    {
        $this->traceInfo["request_status"] = $responseStatus;
    }

    public function getRequestStatus()
    {
        return !empty($this->traceInfo["request_status"]) ? $this->traceInfo["request_status"] : self::VALUE_DEFAULT;
    }

    public function setResponseResult($responseResult)
    {
        $this->traceInfo["response_result"] = $responseResult;
    }


    public function getResponseResult()
    {
        return !empty($this->traceInfo["response_result"]) ? $this->traceInfo["response_result"] : self::VALUE_DEFAULT;
    }

    public function setErrorCode($errorCode)
    {
        $this->traceInfo["error_code"] = $errorCode;
    }

    public function getErrorCode()
    {
        return !empty($this->traceInfo["error_code"]) ? $this->traceInfo["error_code"] : self::VALUE_DEFAULT;
    }

    public function setErrorMessage($errorMessage)
    {
        $this->traceInfo["error_message"] = $errorMessage;
    }

    public function getErrorMessage()
    {
        return !empty($this->traceInfo["error_message"]) ? $this->traceInfo["error_message"] : self::VALUE_DEFAULT;
    }

    public function setUniqueId($uniqueId)
    {
        $this->uniqueId = $uniqueId;
    }

    public function getUniqueId()
    {
        return $this->uniqueId;
    }

    public function setServerSpanId($id)
    {
        $this->traceInfo["server_span_id"] = $id;
    }

    public function getServerSpanId()
    {
        return !empty($this->traceInfo["server_span_id"]) ? $this->traceInfo["server_span_id"] : self::VALUE_DEFAULT;
    }

    public function setClientSpanId($id)
    {
        $this->traceInfo["client_span_id"] = $id;
    }

    public function getClientSpanId()
    {
        return !empty($this->traceInfo["client_span_id"]) ? $this->traceInfo["client_span_id"] : self::VALUE_DEFAULT;
    }
}