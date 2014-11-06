<?php
namespace Thunder\SimilarWebApi;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
interface ParserInterface
    {
    /**
     * @param string $content Raw response data
     *
     * @return AbstractResponse
     */
    public function getResponse($content);
    }
