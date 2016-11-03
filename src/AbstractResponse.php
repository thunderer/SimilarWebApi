<?php
namespace Thunder\SimilarWebApi;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
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
