<?php

namespace Inspirum\Balikobot\Model\Aggregates;

use ArrayAccess;
use ArrayIterator;
use Countable;
use Inspirum\Balikobot\Model\Values\Package;
use IteratorAggregate;

/**
 * @implements \ArrayAccess<int,\Inspirum\Balikobot\Model\Values\Package>
 * @implements \IteratorAggregate<int,\Inspirum\Balikobot\Model\Values\Package>
 */
class PackageCollection implements ArrayAccess, Countable, IteratorAggregate
{
    /**
     * Packages
     *
     * @var array<int,\Inspirum\Balikobot\Model\Values\Package>
     */
    private $packages = [];

    /**
     * Shipper code
     *
     * @var string
     */
    private $shipper;

    /**
     * PackageCollection constructor
     *
     * @param string $shipper
     */
    public function __construct(string $shipper)
    {
        $this->shipper = $shipper;
    }

    /**
     * Add package to collection
     *
     * @param \Inspirum\Balikobot\Model\Values\Package $package
     *
     * @return void
     */
    public function add(Package $package): void
    {
        // clone package
        $package = clone $package;

        // set collection EID
        if ($package->hasEID() === false) {
            $package->setEID($this->newEID());
        }

        // add package to collection
        $this->packages[] = $package;
    }

    /**
     * Get packages shipper
     *
     * @return string
     */
    public function getShipper(): string
    {
        return $this->shipper;
    }

    /**
     * Get the collection of packages as a plain array
     *
     * @return array<array<string,mixed>>
     */
    public function toArray(): array
    {
        return array_map(function (Package $package) {
            return $package->toArray();
        }, $this->packages);
    }

    /**
     * Get new EID for package batch
     *
     * @return string
     */
    private function newEID(): string
    {
        return substr(time() . uniqid(), -20, 20);
    }

    /**
     * Determine if an item exists at an offset
     *
     * @param int $key
     *
     * @return bool
     */
    public function offsetExists($key): bool
    {
        return array_key_exists($key, $this->packages);
    }

    /**
     * Get an item at a given offset
     *
     * @param int $key
     *
     * @return \Inspirum\Balikobot\Model\Values\Package
     */
    public function offsetGet($key): Package
    {
        return $this->packages[$key];
    }

    /**
     * Set the item at a given offset
     *
     * @param int                                      $key
     * @param \Inspirum\Balikobot\Model\Values\Package $value
     *
     * @return void
     */
    public function offsetSet($key, $value): void
    {
        $this->packages[$key] = $value;
    }

    /**
     * Unset the item at a given offset
     *
     * @param int $key
     *
     * @return void
     */
    public function offsetUnset($key): void
    {
        unset($this->packages[$key]);
    }

    /**
     * Count elements of an object
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->packages);
    }

    /**
     * Get an iterator for the items
     *
     * @return \ArrayIterator<int,\Inspirum\Balikobot\Model\Values\Package>
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->packages);
    }
}
