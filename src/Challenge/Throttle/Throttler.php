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

    protected $_rulesLoaded = false;

    /**
     * Add a rule to this throttler
     * @param \Challenge\Throttle\Rule\RuleInterface $rule
     * @return \Challenge\Throttle\Throttler
     */
    public function addRule(Rule\RuleInterface $rule)
    {
        $this->_ruleChain[] = $rule;
        $this->_rulesLoaded = false;
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
     * Generator to load iterate rules after applying their state from storage
     * @return \Challenge\Throttle\Rule\RuleInterface
     */
    protected function _loadRules($identifier)
    {
        foreach ($this->_ruleChain as $rule) {
            if (!(isset($this->_rulesLoaded[$identifier]) && $this->_rulesLoaded[$identifier])) {
                $state = $this->getStorage()->retrieve($this->_getStorageKey($identifier, $rule->getKey()));
                if (!is_array($state)) {
                    throw new \RuntimeException('state retrieved from storage must be an array');
                }
                $rule->setState($state);
            }
            yield $rule;
        }
        $this->_rulesLoaded[$identifier] = true;
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
        foreach ($this->_loadRules($identifier) as $rule) {
            if ($rule->throttled($identifier)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Mix the identifier and the rule key to have a lookup string to use in storage
     * @param  string $identifier
     * @param  string $ruleKey
     * @return string
     */
    protected function _getStorageKey($identifier, $ruleKey)
    {
        return sprintf('%s.%s', $identifier, $ruleKey);
    }

    /**
     * Log a requst event for the given identifier
     * @param string $identifier
     * @return \Challenge\Throttle\Throttler
     * @throws \RuntimeException
     */
    public function log($identifier)
    {
        $when = microtime(true);
        foreach($this->_loadRules($identifier) as $rule) {
            $rule->log($when);
            $this->_storage->store($this->_getStorageKey($identifier, $rule->getKey()), $rule->getState(), $rule->getTtl());
        }
        return $this;
    }

    public function clear($identifier)
    {
        foreach($this->_ruleChain as $rule) {
            $this->getStorage()->delete($this->_getStorageKey($identifier, $rule->getKey()));
        }
        return $this;
    }

    /**
     * Call function when allowed
     * @param  string   $identifier
     * @param  callable $request
     * @param  float  $pollFrequency
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