<?php

namespace Challenge\Throttle\Rule;

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
    public function throttled(array $requestTimestamps);

    /**
     * @return int the last timestamp that you care about. Anything before this can be discarded
     */
    public function getWindow();
}