<?php
namespace Ycs77\ImageMetadata\Tests\Format;

use Ycs77\ImageMetadata\Format\PSD;
use Ycs77\ImageMetadata\Metadata\Xmp;

/**
 * @coversDefaultClass \Ycs77\ImageMetadata\Metadata\PSD
 */
class PsdTest extends \PHPUnit_Framework_TestCase
{
        /**
     * Test that PSD can read XMP embedded with Photoshop.
     */
    public function testGetXmpPhotoshop()
    {
        $psd = PSD::fromFile(__DIR__ . '/../Fixtures/metaphotoshop.psd');

        $xmp = $psd->getXmp();

        $this->assertInstanceOf(Xmp::class, $xmp);
        $this->assertSame('Headline', $xmp->getHeadline());
    }

    /**
     * Test that PSD class returns an empty XMP object when there is no XMP data.
     */
    public function testGetXmpNoMeta()
    {
        $psd = PSD::fromFile(__DIR__ . '/../Fixtures/nometa.psd');

        $xmp = $psd->getXmp();

        $this->assertInstanceOf(Xmp::class, $xmp);
        $this->assertNull($xmp->getHeadline());
    }
}