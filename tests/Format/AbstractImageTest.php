<?php

namespace Ycs77\ImageMetadata\Tests\Format;

use Exception;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Ycs77\ImageMetadata\Image;
use Ycs77\ImageMetadata\Metadata\Aggregate;
use Ycs77\ImageMetadata\Metadata\Exif;
use Ycs77\ImageMetadata\Metadata\Iptc;
use Ycs77\ImageMetadata\Metadata\UnsupportedException;
use Ycs77\ImageMetadata\Metadata\Xmp;

class AbstractImageTest extends TestCase
{
    public function testGetAggregate()
    {
        $image = $this->getMockForAbstractImage();
        $image->shouldReceive('getXmp')->once()->andReturn(m::mock(Xmp::class));
        $image->shouldReceive('getIptc')->once()->andReturn(m::mock(Iptc::class));
        $image->shouldReceive('getExif')->once()->andReturn(m::mock(Exif::class));

        $aggregate = $image->getAggregate();

        $this->assertInstanceOf(Aggregate::class, $aggregate);
    }

    public function testGetAggregateWithUnsupportedTypes()
    {
        $image = $this->getMockForAbstractImage();
        $image->shouldReceive('getXmp')->once()->andThrow(new UnsupportedException);
        $image->shouldReceive('getIptc')->once()->andThrow(new UnsupportedException);
        $image->shouldReceive('getExif')->once()->andThrow(new UnsupportedException);

        $aggregate = $image->getAggregate();

        $this->assertInstanceOf(Aggregate::class, $aggregate);
    }

    public function testSave()
    {
        $tmp = tempnam(sys_get_temp_dir(), 'PNG');

        $image = $this->getMockForAbstractImage();
        $image->shouldReceive('getBytes')->once()->andReturn('Test');

        $this->assertSame($image, $image->setFilename($tmp)); // test fluid interface

        $image->save();

        $this->assertEquals('Test', file_get_contents($tmp));
    }

    public function testSaveWithFilename()
    {
        $tmp = tempnam(sys_get_temp_dir(), 'PNG');

        $image = $this->getMockForAbstractImage();
        $image->shouldReceive('getBytes')->once()->andReturn('Test');
        $image->save($tmp);

        $this->assertEquals('Test', file_get_contents($tmp));
    }

    public function testSaveWithNoFilename()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Must provide a filename');

        $image = $this->getMockForAbstractImage();
        $image->save();
    }

    /**
     * @return Image|\Mockery\LegacyMockInterface
     */
    private function getMockForAbstractImage()
    {
        return m::mock(Image::class)->makePartial();
    }
}
