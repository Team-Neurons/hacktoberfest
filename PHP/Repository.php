<?php

use ArrayAccess;

class Repository implements ArrayAccess {

    /**
     * All of the items that will be stored in the repository
     *
     * @var array
     */
    protected $items = [];

    /**
     * Create a new repository
     *
     * @param array $items
     */
    public function __construct(array $items = []) {
        $this->items = $items;
    }

    /**
     * Check if repository has the given key
     *
     * @param string $key
     *
     * @return bool
     */
    public function has($key) {   
        return isset($this->items[$key]);
    }

    /**
     * Get the value of given key from repository
     *
     * @param array|string $key
     * @param mixed $default
     */
    public function get($key, $default = null) {

        if (is_array($key)) {
            return $this->getMany($key);
        }

        return $this->items[$key] ?? $default;
    }

    /**
     * Get the values of given keys from repository
     *
     * @param array $keys
     * @return array
     */
    public function getMany($keys) {

        $config = [];

        foreach ($keys as $key => $default) {

            if (is_numeric($key)) {
                // Check if key has a default value, if not then
                // it's an indexed array, make it associative
                [$key, $default] = [$default, null];
            }

            $config[$key] = $this->get($key);
        }

        return $config;

    }

    /**
     * Set a given value
     *
     * @param array|string $key
     * @param mixed $value
     * @return void
     */
    public function set($key, $value = null) {
        $this->items[$key] = $value;
    }

    /**
     * Get all of the items
     *
     * @return array
     */
    public function all() {
        return $this->items;
    }

    /**
     * Determine if the given option exists
     *
     * @param string $key
     *
     * @return bool
     */
    public function offsetExists($key) {
        return $this->has($key);
    }

    /**
     * Get an option
     *
     * @param string $key
     *
     * @return mixed
     */
    public function offsetGet($key) {
        return $this->get($key);
    }

    /**
     * Set an option
     *
     * @param string $key
     * @param mixed $value
     */
    public function offsetSet($key, $value) {
        $this->set($key, $value);
    }

    /**
     * Unset an option
     *
     * @param string $key
     */
    public function offsetUnset($key) {
        $this->set($key, null);
    }
}
