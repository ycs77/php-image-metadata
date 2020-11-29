<?php

namespace Ycs77\ImageMetadata\Tests\Format;

use Ycs77\ImageMetadata\Format\WebP;
use Ycs77\ImageMetadata\Metadata\Exif;
use Ycs77\ImageMetadata\Metadata\Xmp;

/**
 * @author Daniel Chesterton <daniel@chestertondevelopment.com>
 *
 * @coversDefaultClass \Ycs77\ImageMetadata\Format\WebP
 */
class WebPTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test that a non-WebP file throws an exception.
     *
     * @expectedException \Exception
     * @expectedExceptionMessage Invalid WebP file
     *
     * @covers ::fromFile
     * @covers ::__construct
     */
    public function testFromFileInvalidWebP()
    {
        WebP::fromFile(__DIR__ . '/../Fixtures/nometa.jpg');
    }

    public function testFromFile()
    {
        $webp = WebP::fromFile(__DIR__ . '/../Fixtures/meta.webp');
        $this->assertInstanceOf(WebP::class, $webp);

        $xmp = $webp->getXmp();

        $this->assertInstanceOf(XMP::class, $xmp);
        $this->assertSame('Headline', $xmp->getHeadline());
    }

    public function testChangeXmp()
    {
        $tmp = tempnam(sys_get_temp_dir(), 'WebP');

        $webp = WebP::fromFile(__DIR__ . '/../Fixtures/meta.webp');
        $webp->getXmp()->setHeadline('PHP headline');
        $webp->save($tmp);

        $newWebp = WebP::fromFile($tmp);

        $this->assertSame('PHP headline', $newWebp->getXmp()->getHeadline());
    }

    public function testGetExif()
    {
        $webp = WebP::fromFile(__DIR__ . '/../Fixtures/exif.webp');
        $exif = $webp->getExif();

        $this->assertInstanceOf(Exif::class, $exif);

        // todo: test actual value of exif
    }

    /**
     * @expectedException \Ycs77\ImageMetadata\Metadata\UnsupportedException
     * @expectedExceptionMessage WebP files do not support IPTC metadata
     *
     * @covers ::getIptc
     */
    public function testGetIptc()
    {
        $webp = WebP::fromFile(__DIR__ . '/../Fixtures/meta.webp');
        $webp->getIptc();
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Only extended WebP format is supported
     */
    public function ttestSimpleUnsupported()
    {
        WebP::fromFile(__DIR__ . '/../Fixtures/simple.webp');
    }

    public function testConvertsFromSimpleFormat()
    {
        // todo: mock Xmp class
        $xmp = new Xmp;

        $webp = WebP::fromFile(__DIR__ . '/../Fixtures/simple.webp');
        $webp->setXmp($xmp);

        var_dump($webp->getBytes());
    }
}
