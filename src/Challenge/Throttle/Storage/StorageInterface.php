<?php

namespace Challenge\Throttle\Storage;

/**
 * @author Tim Williams <tim@wordery.com>
 */
interface StorageInterface
{
    /**
     * @parma $identifier string the identifier of the request. e.g. username or IP address
     * @param $requests array of int timestamps of reqests
     * @param $ttl the ttl to set on the storage if it supports it. It's safe to ignore this as the Throttler class handles garbage collection
     */
    public function store($identifier, array $requests, $ttl);

    /**
     * @param $identifier
     * @return array of int
     */
    public function retrieve($identifier);

    /**
     * Clear this identifiers history.
     * @param $identifier
     */
    public function delete($identifier);
}