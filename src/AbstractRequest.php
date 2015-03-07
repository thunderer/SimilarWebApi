<?php
namespace Thunder\SimilarWebApi;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
abstract class AbstractRequest
    {
    const API = 'http://api.similarweb.com';

    protected static $appStores = array('google_play_store' => '0', 'app_store' => '1');
    protected static $arguments = array('app', 'domain', 'period', 'start', 'end', 'main', 'store', 'page');
    protected static $periods = array('daily', 'weekly', 'monthly');

    protected $args = array();

    public function getCallUrl($format, $token)
        {
        $args = $this->args;
        $args['format'] = $format;
        $args['token'] = $token;
        $keys = array_map(function($item) {
            return '{'.$item.'}';
            }, array_keys($args));
        $values = array_values($args);

        return str_replace($keys, $values, $this->getUrl());
        }

    protected function validateArg($arg, $value)
        {
        if('domain' == $arg && !$value)
            {
            throw new \InvalidArgumentException('Empty domain!');
            }
        elseif('app' == $arg && !$value)
            {
            throw new \InvalidArgumentException('Empty app name!');
            }
        elseif('period' == $arg && !in_array($value, static::$periods))
            {
            $periods = implode(', ', static::$periods);
            $msg = 'Invalid period %s, expected one of %s!';
            throw new \InvalidArgumentException(sprintf($msg, $value, $periods));
            }
        elseif(in_array($arg, array('start', 'end')) && !preg_match('/[0-9]{2}-[0-9]{4}/', $value))
            {
            $msg = 'Invalid %s date %s, expected format MM-YYYY!';
            throw new \InvalidArgumentException(sprintf($msg, $arg, $value));
            }
        elseif('main' == $arg)
            {
            $value = (bool)$value ? 'true' : 'false';
            }
        elseif('page' == $arg && !ctype_digit((string)$value))
            {
            $msg = 'Invalid page number %s!';
            throw new \InvalidArgumentException(sprintf($msg, $value));
            }
        elseif('store' == $arg && !in_array($value, static::$appStores))
            {
            $msg = 'Invalid app store ID %s!';
            throw new \InvalidArgumentException(sprintf($msg, $value));
            }
        else if(!in_array($arg, static::$arguments))
            {
            $msg = 'Unknown argument %s (value %s)!';
            throw new \InvalidArgumentException(sprintf($msg, $arg, $value));
            }

        return $value;
        }

    /**
     * @return string API endpoint name as in documentation
     */
    abstract public function getName();

    /**
     * @return string API call URL with parameter placeholders
     */
    abstract public function getUrl();

    /**
     * @return array Mapping data for response parsing
     */
    abstract public function getMapping();
    }
