<?php
namespace Thunder\SimilarWebApi;

abstract class AbstractResponse
    {
    protected $response;

    public function __construct(RawResponse $response)
        {
        $this->response = $response;
        }

    public function getRawResponse()
        {
        return $this->response;
        }
    }
