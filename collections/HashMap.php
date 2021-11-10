<?php


namespace collections;


/**
 * Wrapper around array with extended access.
 */
class HashMap implements \ArrayAccess {


  /**
   * @var array
   */
  private $_data;
  /**
   * @var string
   */
  private $_separator;


  /**
   * @param array $data
   * @param string $separator
   */
  private function __construct(array $data = [], string $separator = '/') {

    $this->_data = $data;
    $this->_separator = $separator;
  }


  /**
   * Create instance from array.
   * @param array $data
   * @param string $separator
   * @return self
   */
  public static function fromArray(array $data = [], string $separator = '/'): self {

    return new static($data, $separator);
  }


  /**
   * Create empty storage.
   * @param string $separator
   * @return self
   */
  public static function empty(string $separator = '/'): self {

    return new static([], $separator);
  }


  /**
   * {@inheritDoc}
   * @see \ArrayAccess::offsetExists()
   */
  public function offsetExists($offset): bool {

    return $this->has($offset);
  }


  /**
   * {@inheritDoc}
   * @see \ArrayAccess::offsetGet()
   */
  public function offsetGet($offset) {

    return $this->get($offset);
  }


  /**
   * {@inheritDoc}
   * @see \ArrayAccess::offsetSet()
   */
  public function offsetSet($offset, $value): void {

    $this->set($offset, $value);
  }


  /**
   * {@inheritDoc}
   * @see \ArrayAccess::offsetUnset()
   */
  public function offsetUnset($offset): void {

    throw new \Exception('Not implemented');
  }


  /**
   * Get input array.
   * @return array
   */
  public function toArray(): array {

    return $this->_data;
  }


  /**
   * Check if value is array.
   * @param string $key
   * @return bool
   */
  public function isArray(string $key): bool {

    return $this->has($key) && is_array($this->get($key));
  }


  /**
   * Get value from array.
   * @param string $key
   * @param mixed $defaultValue
   * @param mixed $emptyValue
   */
  public function get(string $key, $defaultValue = null, $emptyValue = null) {

    $keys  = explode($this->_separator, $key);
    $value = $this->_data;

    foreach ($keys as $key) {
      if (array_key_exists($key, $value)) {
        $value = $value[$key];
      } else {
        return $defaultValue;
      }
    }

    if (empty($value) && $emptyValue !== null) {
      return $emptyValue;
    } else {
      return $value;
    }
  }


  /**
   * Set array item value.
   * @param string $key
   * @param mixed $defaultValue
   * @return bool
   */
  public function set(string $key, $newValue) {

    $keys  = explode($this->_separator, $key);
    $value = & $this->_data;

    foreach ($keys as $key) {

      if (! is_array($value)) {
        $value = [ $key => [] ];
      }

      $value = & $value[$key];
    }

    $value = $newValue;
  }


  /**
   * Check array has key.
   * @param string $key
   * @return bool
   */
  public function has(string $key): bool {

    $keys  = explode($this->_separator, $key);
    $value = $this->_data;

    foreach ($keys as $key) {
      if (! is_array($value)) {
        return false;
      } elseif (array_key_exists($key, $value)) {
        $value = $value[$key];
      } else {
        return false;
      }
    }

    return true;
  }


  /**
   * Is value under $key equal to $value.
   * @param string $key
   * @param mixed $value
   * @return bool
   */
  public function isEqual(string $key, $value): bool {

    return $this->get($key) == $value;
  }


  /**
   * Is value under $key identical to $value.
   * @param string $key
   * @param mixed $value
   * @return bool
   */
  public function isIdentical(string $key, $value): bool {

    return $this->get($key) === $value;
  }


  /**
   * Get item value by key and remove it from storage.
   * @param string $key
   * @param mixed $defaultValue
   * @param mixed $emptyValue
   * @return mixed
   */
  public function pluck(string $key, $defaultValue = null, $emptyValue = null) {

    $value = $this->get($key, $defaultValue, $emptyValue);
    $this->delete($key);

    return $value;
  }


  /**
   * Delete item by key.
   * @param string $key
   */
  public function delete(string $key) {

    if (! $this->has($key)) {
      return;
    }

    $keys   = explode($this->_separator, $key);
    $length = count($keys);
    $value  = & $this->_data;

    foreach ($keys as $i => $key) {
      if ($i + 1 === $length) {
        unset($value[$key]);
      } elseif (array_key_exists($key, $value)) {
        $value = & $value[$key];
      }
    }
  }


  /**
   * Check storage is empty.
   * @return bool
   */
  public function isEmpty(): bool {

    return count($this->_data) === 0;
  }
}
