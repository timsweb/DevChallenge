<?php
namespace Challenge\Throttle\Rule;

/**
 * Allow one request every x seconds.
 * @author Tim Williams <tim@wordery.com>
 */
class RateLimit implements RuleInterface
{
    protected $_timespan;

    protected $_lastRequest = 0;

    public function __construct($timeBetweenRequests = 2)
    {
        $this->_timespan = $timeBetweenRequests;
    }

    public function getTtl()
    {
        return $this->_timespan;
    }

    /**
     * Check time of last request aginst now
     * @param array $requestTimestamps
     * @return boolean
     */
    public function throttled()
    {
        if (!$this->_lastRequest) {
            return false;
        }
        return ($this->_lastRequest + $this->_timespan) >= microtime(1);
    }

    public function log($timestamp)
    {
        $this->_lastRequest = $timestamp;
        return $this;
    }


    public function getKey()
    {
        return 'rateLimit';
    }

    public function getState()
    {
        return [$this->_lastRequest];
    }

    public function setState(array $state)
    {
        if (empty($state) || !isset($state[0])) {
            return;
        }
        $this->_lastRequest = $state[0];
    }
}