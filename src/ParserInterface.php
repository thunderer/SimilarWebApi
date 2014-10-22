<?php
namespace Thunder\SimilarWebApi;

interface ParserInterface
    {
    /**
     * @param string $content Raw response data
     *
     * @return AbstractResponse
     */
    public function getResponse($content);
    }
