<?php

namespace Challenge\Throttle;

/**
 * A tool to determine if a request should be allowed or not.
 */
class Throttler
{
    protected $_ruleChain = [];

    /**
     * @var Storage\StorageInterface
     */
    protected $_storage;


    /**
     * Add a rule to this throttler
     * @param \Challenge\Throttle\Rule\RuleInterface $rule
     * @return \Challenge\Throttle\Throttler
     */
    public function addRule(Rule\RuleInterface $rule)
    {
        $this->_ruleChain[] = $rule;
        return $this;
    }

    /**
     * @return array
     */
    public function getRuleChain()
    {
        return $this->_ruleChain;
    }

    /**
     *
     * @return Storage\StorageInterface
     */
    public function getStorage()
    {
        return $this->_storage;
    }

    /**
     * @param array $ruleChain
     * @return \Challenge\Throttle\Throttler
     */
    public function setRuleChain(array $ruleChain)
    {
        $this->_ruleChain = $ruleChain;
        return $this;
    }

    /**
     * @param \Challenge\Throttle\Storage\StorageInterface $storage
     * @return \Challenge\Throttle\Throttler
     */
    public function setStorage(Storage\StorageInterface $storage)
    {
        $this->_storage = $storage;
        return $this;
    }

    /**
     * Test if the current identifier is under a throttled condition.
     *
     * @param string $identifier
     * @return boolean true if request allowed, false otherwise
     * @throws \RuntimeException
     */
    public function throttled($identifier)
    {
        $requests = $this->getStorage()->retrieve($identifier);
        if (!is_array($requests)) {
            throw new \RuntimeException('requests array retrieved from storage must be an array');
        }
        foreach ($this->_ruleChain as $rule) {
            if ($rule->throttled($requests)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return int
     */
    protected function _getBackwardWindow()
    {
        $pastCutoff = PHP_INT_MAX;
        foreach ($this->_ruleChain as $rule) {
            if ($rule->getWindow() < $pastCutoff) {
                $pastCutoff = $rule->getWindow();
            }
        }
        return $pastCutoff;
    }

    /**
     * @return int
     */
    protected function _getForwardWindow()
    {
        return (int) (time() - $this->_getBackwardWindow()) * 1.5;
    }

    /**
     * Log a requst event for the given identifier
     * @param string $identifier
     * @return \Challenge\Throttle\Throttler
     * @throws \RuntimeException
     */
    public function log($identifier)
    {
        $requests = $this->getStorage()->retrieve($identifier);
        if (!is_array($requests)) {
            throw new \RuntimeException('requests array retrieved from storage must be an array');
        }

        $requests[] = microtime(true);
        $backWindow = $this->_getBackwardWindow();
        $requests = array_values(array_filter($requests, function ($value) use ($backWindow) {
            return $value >= $backWindow;
        }));
        $this->_storage->store($identifier, $requests, $this->_getForwardWindow());
        return $this;
    }

    public function clear($identifier)
    {
        $this->getStorage()->delete($identifier);
        return $this;
    }

    /**
     * Call function when allowed
     * @param  string   $identifier
     * @param  callable $request
     * @param  float  $pollFrequency in seconds
     * @return mixed
     */
    public function throttleRequest($identifier, callable $request, $pollFrequency = 0.5)
    {
        while ($this->throttled($identifier)) {
            usleep($pollFrequency * 1000000);
        }
        $return = call_user_func($request);
        $this->log($identifier);
        return $return;
    }
}