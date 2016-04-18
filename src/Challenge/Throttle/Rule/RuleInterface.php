<?php
namespace Throttle\Rule;

/**
 * @author Tim Williams <tim@wordery.com>
 */
interface RuleInterface
{
    /**
     * Check to see if we are under a trottled condition.
     * @param array of int $requestTimestamps array of recent requests for a single identifier.
     * @return boolean true if request throttled, false otherwise.
     */
    public function throttled();

    /**
     * @return int seconds to set ttl of data
     */
    public function getTtl();

    /**
     * Get a name to key on when storing this state info
     * @return string
     */
    public function getKey();

    /**
     * Update state to log event happening
     * @param  float $timestamp
     * @return void
     */
    public function log($timestamp);

    /**
     * Get the current state
     * @return array
     */
    public function getState();


    /**
     * Set the current state from storage
     * @param array $state
     */
    public function setState(array $state);
}