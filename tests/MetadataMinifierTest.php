<?php

/*
 * This file is part of composer/metadata-minifier.
 *
 * (c) Composer <https://github.com/composer>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Composer\Test\MetadataMinifier;

use Composer\MetadataMinifier\MetadataMinifier;
use Composer\Package\CompletePackage;
use Composer\Package\Dumper\ArrayDumper;
use PHPUnit\Framework\TestCase;

class MetadataMinifierTest extends TestCase
{
    /**
     * @return void
     */
    public function testMinifyExpand()
    {
        $package1 = new CompletePackage('foo/bar', '2.0.0.0', '2.0.0');
        $package1->setScripts(array('foo' => array('bar')));
        $package1->setLicense(array('MIT'));
        $package2 = new CompletePackage('foo/bar', '1.2.0.0', '1.2.0');
        $package2->setLicense(array('GPL'));
        $package2->setHomepage('https://example.org');
        $package3 = new CompletePackage('foo/bar', '1.0.0.0', '1.0.0');
        $package3->setLicense(array('GPL'));
        $dumper = new ArrayDumper();

        $minified = array(
            array('name' => 'foo/bar', 'version' => '2.0.0', 'version_normalized' => '2.0.0.0', 'type' => 'library', 'scripts' => array('foo' => array('bar')), 'license' => array('MIT')),
            array('version' => '1.2.0', 'version_normalized' => '1.2.0.0', 'license' => array('GPL'), 'homepage' => 'https://example.org', 'scripts' => '__unset'),
            array('version' => '1.0.0', 'version_normalized' => '1.0.0.0', 'homepage' => '__unset'),
        );

        $source = array($dumper->dump($package1), $dumper->dump($package2), $dumper->dump($package3));

        $this->assertSame($minified, MetadataMinifier::minify($source));
        $this->assertSame($source, MetadataMinifier::expand($minified));
    }
}
