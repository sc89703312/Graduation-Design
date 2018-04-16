<?php
/**
 * Created by PhpStorm.
 * User: Echo
 * Date: 2018/4/12
 * Time: 16:51
 */

namespace demo\test;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;


class CallService
{

    private $route_table_address = 'http://localhost:9999/';

    private $lastUniqueTraceId = 0;

    private $traceServiceList = [];

    /**
     * @param $serviceName
     * @param $uri
     * @param string $method
     * @param array $params
     * @param array $option
     * @param array $config
     * @param bool $isAsync
     * @return mixed
     * @throws \Exception
     */
    public function http($serviceName, $uri, $method = 'get', $params = array(), $option = [], $config = ['timeout' => 10], $isAsync = false)
    {
        $trace = new TraceService();
        $trace->start();

        $trace->setServerName($serviceName);
        $trace->setRequestUri($uri);
        $trace->setRequestType(TraceService::TYPE_HTTP);
        $trace->setRequestMethod($method);
        $trace->setRequestParams($params);
        $trace->setExtendInfo([]);

        $this->lastUniqueTraceId ++;
        $this->traceServiceList[$this->lastUniqueTraceId] = $trace;

        $span_id = $trace->spanId();
        $trace->setServerSpanId($span_id);
        $trace->setClientSpanId($span_id . $this->lastUniqueTraceId);

        $params[TraceService::REQUEST_FIELD_TRACE_ID] = $trace->traceId();
        $params[TraceService::FIELD_SPAN_ID] = $span_id . $this->lastUniqueTraceId;

        $client = new Client();
        try {
            if ($params) {
                $option['query'] = $params;
            }
            $url = $this->route_table_address . $serviceName . $uri;
            $response = $client->$method($url, $option);
            return $this->handleResponse($response, $this->lastUniqueTraceId);

        } catch (GuzzleException $exception) {
            $trace->setErrorCode($exception->getCode());
            $trace->setErrorMessage($exception->getMessage());
            $trace->setRequestStatus(TraceService::STATUS_FAIL);
            $trace->dump();

            throw new \Exception($serviceName . ' Not Registered', 400);
        }
    }

    public function handleResponse(Response $response, $id)
    {
        $result = null;
        $trace = $this->traceServiceList[$id];
        $httpStatus = $response->getStatusCode();

        //contents 不能多次获取 --请勿更改
        $content = $response->getBody()->getContents();
        $header = $response->getHeaders();

        if(isset($header['Host']) && !empty($header['Host'])) {
            $host_info = $header['Host'][0];
            $host_ip = explode(':', $host_info)[0];
            $host_port = explode(':', $host_info)[1];
            $trace->setServerIp($host_ip);
            $trace->setServerPost($host_port);
        }

        $trace->setResponseCode($httpStatus);

        if ($httpStatus == 200) {
            $result = [
                'header'    => $header,
                'content'   => $content
            ];
            $trace->setRequestStatus(TraceService::STATUS_OK);
        } else {
            $trace->setRequestStatus(TraceService::STATUS_FAIL);
        }
        $trace->setResponseResult($content);
        $trace->dump();

        var_dump('complete');

        return $result;
    }
}