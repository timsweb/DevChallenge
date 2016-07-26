<?php
namespace Challenge\Throttle\Storage;

/**
 * @author Tim Williams <tim@wordery.com>
 */
interface StorageInterface
{
    /**
     * @parma $identifier string the identifier of the request. e.g. username or IP address
     * @param $state an array of state information
     * @param $ttl the ttl to set on the storage if it supports it. HINT: "if it supports it" (you don't need to worry about that if you are using $_SESSION for the sake of this test)
     */
    public function store($identifier, array $state, $ttl);

    /**
     * @param $identifier
     * @return array state
     */
    public function retrieve($identifier);

    /**
     * Clear this identifiers history.
     * @param $identifier
     */
    public function delete($identifier);
}
