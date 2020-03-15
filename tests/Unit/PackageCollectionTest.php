<?php

namespace Inspirum\Balikobot\Tests\Integration\Balikobot;

use Inspirum\Balikobot\Model\Aggregates\PackageCollection;
use Inspirum\Balikobot\Model\Values\Package;
use Inspirum\Balikobot\Tests\AbstractTestCase;

class PackageCollectionTest extends AbstractTestCase
{
    public function testCreateEid()
    {
        $packages = new PackageCollection('cp');

        $packages->add(new Package(['test' => 1]));

        $this->assertNotEmpty($packages->offsetGet(0)->getEid());
    }

    public function testCreateUniqueEid()
    {
        $packages1 = new PackageCollection('cp');
        $packages2 = new PackageCollection('cp');
        $packages3 = new PackageCollection('cp');

        $package = new Package(['test' => 1]);

        $packages1->add($package);
        $packages2->add($package);
        $packages3->add($package);

        $this->assertTrue($packages1->offsetGet(0)->getEid() !== $packages2->offsetGet(0)->getEid());
        $this->assertTrue($packages1->offsetGet(0)->getEid() !== $packages3->offsetGet(0)->getEid());
        $this->assertTrue($packages2->offsetGet(0)->getEid() !== $packages3->offsetGet(0)->getEid());
    }

    public function testAddedPackagesHasCollectionEid()
    {
        $packages = new PackageCollection('cp', '0001');

        $packages->add(new Package(['test' => 1]));
        $packages->add(new Package(['test' => 2]));

        /* @var \Inspirum\Balikobot\Model\Values\Package[] $packages */
        foreach ($packages as $package) {
            $this->assertEquals('0001', $package->getEID());
        }

        $this->assertEquals('cp', $packages->getShipper());
        $this->assertEquals(2, $packages->count());
    }

    public function testAddedPackagesAreClones()
    {
        $packages = new PackageCollection('cp', '0001');

        $package = new Package(['test' => 1]);

        $packages->add($package);

        $package->offsetSet('test', 2);

        $packages->add($package);

        $this->assertEquals(
            [
                0 => [
                    'eid'  => '0001',
                    'test' => 1,
                ],
                1 => [
                    'eid'  => '0001',
                    'test' => 2,
                ],
            ],
            $packages->toArray()
        );
    }

    public function testSupportCustomEIDForPackage()
    {
        $packages = new PackageCollection('cp', '0001');

        $packages->add(new Package(['test' => 1, 'eid' => '0002']));
        $packages->add(new Package(['test' => 2]));

        $this->assertEquals(
            [
                0 => [
                    'eid'  => '0002',
                    'test' => 1,
                ],
                1 => [
                    'eid'  => '0001',
                    'test' => 2,
                ],
            ],
            $packages->toArray()
        );
    }

    public function testSupportArrayAccess()
    {
        $packages = new PackageCollection('cp');

        $packages->add(new Package(['test' => 1, 'eid' => '0001']));
        $packages->add(new Package(['test' => 2, 'eid' => '0002']));

        $this->assertEquals('0002', $packages->offsetGet(1)->getEID());
        $this->assertEquals(2, $packages->count());
        $this->assertTrue($packages->offsetExists(1));

        $packages->offsetUnset(1);

        $this->assertFalse($packages->offsetExists(1));
    }

    public function testSupportIteratorAggregate()
    {
        $packages = new PackageCollection('cp');

        $packages->add(new Package(['test' => 1, 'eid' => '0001']));
        $packages->add(new Package(['test' => 2, 'eid' => '0002']));

        $iterator = $packages->getIterator();

        $this->assertEquals(2, $iterator->count());
        $this->assertEquals('0001', $iterator->current()->getEID());
    }
}
