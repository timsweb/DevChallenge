<?php

namespace Challenge\Throttle\Storage;

/**
 * @author Tim Williams <tim@wordery.com>
 */
class Session implements StorageInterface
{

    /**
     * @parma $identifier string the identifier of the request. e.g. username or IP address
     * @param $state an array of state information
     * @param $ttl the ttl to set on the storage if it supports it. We're not doing anything with it here.
     */
    public function store($identifier, array $state, $ttl)
    {
        if (!isset($_SESSION['throttling'][$identifier])) {
            $_SESSION['throttling'][$identifier] = [];
        }
        $_SESSION['throttling'][$identifier] = $state;
    }

    /**
     * @param $identifier
     * @return array of int
     */
    public function retrieve($identifier)
    {
        if (!isset($_SESSION['throttling'][$identifier])) {
            return [];
        }
        return $_SESSION['throttling'][$identifier];
    }

    /**
     * Clear this identifiers history.
     * @param $identifier
     */
    public function delete($identifier)
    {
        unset($_SESSION['throttling'][$identifier]);
        return $this;
    }
}