<?php
namespace Thunder\SimilarWebApi;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
abstract class AbstractParser implements ParserInterface
    {
    protected $name;
    protected $mapping;

    public function __construct($name, array $mapping)
        {
        $this->name = $name;
        $this->mapping = $mapping;
        }

    public function getResponse($content)
        {
        $response = $this->parse($content);
        $class = 'Thunder\\SimilarWebApi\\Response\\'.$this->name;

        if(!class_exists($class, true))
            {
            throw new \RuntimeException(sprintf('Failed to load response class %s!', $class));
            }

        return new $class($response);
        }

    abstract protected function parse($content);
    }
