<?php

namespace Ycs77\ImageMetadata\Tests\Format;

use Exception;
use PHPUnit\Framework\TestCase;
use Ycs77\ImageMetadata\Format\WebP;
use Ycs77\ImageMetadata\Metadata\Exif;
use Ycs77\ImageMetadata\Metadata\UnsupportedException;
use Ycs77\ImageMetadata\Metadata\Xmp;

class WebPTest extends TestCase
{
    /**
     * Test that a non-WebP file throws an exception.
     */
    public function testFromFileInvalidWebP()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid WebP file');

        WebP::fromFile(__DIR__.'/../Fixtures/nometa.jpg');
    }

    public function testFromFile()
    {
        $webp = WebP::fromFile(__DIR__.'/../Fixtures/meta.webp');
        $this->assertInstanceOf(WebP::class, $webp);

        $xmp = $webp->getXmp();

        $this->assertInstanceOf(XMP::class, $xmp);
        $this->assertSame('Headline', $xmp->getHeadline());
    }

    public function testChangeXmp()
    {
        $this->markTestSkipped('WebP is not used and does not want to be fixed.');

        $tmp = tempnam(sys_get_temp_dir(), 'WebP');

        $webp = WebP::fromFile(__DIR__.'/../Fixtures/meta.webp');
        $webp->getXmp()->setHeadline('PHP headline');
        $webp->save($tmp);

        $newWebp = WebP::fromFile($tmp);

        $this->assertSame('PHP headline', $newWebp->getXmp()->getHeadline());
    }

    public function testGetExif()
    {
        $webp = WebP::fromFile(__DIR__.'/../Fixtures/exif.webp');
        $exif = $webp->getExif();

        $this->assertInstanceOf(Exif::class, $exif);

        // todo: test actual value of exif
    }

    public function testGetIptc()
    {
        $this->expectException(UnsupportedException::class);
        $this->expectExceptionMessage('WebP files do not support IPTC metadata');

        $webp = WebP::fromFile(__DIR__.'/../Fixtures/meta.webp');
        $webp->getIptc();
    }

    public function ttestSimpleUnsupported()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Only extended WebP format is supported');

        WebP::fromFile(__DIR__.'/../Fixtures/simple.webp');
    }

    public function testConvertsFromSimpleFormat()
    {
        $this->markTestSkipped('Not implemented yet');

        // todo: mock Xmp class
        $xmp = new Xmp;

        $webp = WebP::fromFile(__DIR__.'/../Fixtures/simple.webp');
        $webp->setXmp($xmp);

        // var_dump($webp->getBytes());
    }
}
