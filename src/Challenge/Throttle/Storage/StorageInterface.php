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
     * @param $ttl the ttl to set on the storage if it supports it, othwerwise some garbage collection will be required.
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