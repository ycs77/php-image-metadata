<?php

namespace Ycs77\ImageMetadata\Tests\Format;

use Mockery as M;
use Ycs77\ImageMetadata\Image;
use Ycs77\ImageMetadata\Metadata\Aggregate;
use Ycs77\ImageMetadata\Metadata\Exif;
use Ycs77\ImageMetadata\Metadata\Iptc;
use Ycs77\ImageMetadata\Metadata\UnsupportedException;
use Ycs77\ImageMetadata\Metadata\Xmp;

/**
 * @author Daniel Chesterton <daniel@chestertondevelopment.com>
 *
 * @coversDefaultClass \Ycs77\ImageMetadata\AbstractImage
 */
class AbstractImageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::getAggregate
     */
    public function testGetAggregate()
    {
        $image = $this->getMockForAbstractImage();
        $image->expects($this->once())->method('getXmp')->will($this->returnValue(m::mock(Xmp::class)));
        $image->expects($this->once())->method('getIptc')->will($this->returnValue(m::mock(Iptc::class)));
        $image->expects($this->once())->method('getExif')->will($this->returnValue(m::mock(Exif::class)));

        $aggregate = $image->getAggregate();

        $this->assertInstanceOf(Aggregate::class, $aggregate);
    }

    /**
     * @covers ::getAggregate
     */
    public function testGetAggregateWithUnsupportedTypes()
    {
        $image = $this->getMockForAbstractImage();
        $image->expects($this->once())->method('getXmp')->will($this->throwException(new UnsupportedException));
        $image->expects($this->once())->method('getIptc')->will($this->throwException(new UnsupportedException));
        $image->expects($this->once())->method('getExif')->will($this->throwException(new UnsupportedException));

        $aggregate = $image->getAggregate();

        $this->assertInstanceOf(Aggregate::class, $aggregate);
    }

    /**
     * @covers ::save
     * @covers ::setFilename
     */
    public function testSave()
    {
        $tmp = tempnam(sys_get_temp_dir(), 'PNG');

        $image = $this->getMockForAbstractImage();
        $image->expects($this->once())->method('getBytes')->will($this->returnValue('Test'));

        $this->assertSame($image, $image->setFilename($tmp)); // test fluid interface

        $image->save();

        $this->assertEquals('Test', file_get_contents($tmp));
    }

    /**
     * @covers ::save
     */
    public function testSaveWithFilename()
    {
        $tmp = tempnam(sys_get_temp_dir(), 'PNG');

        $image = $this->getMockForAbstractImage();
        $image->expects($this->once())->method('getBytes')->will($this->returnValue('Test'));
        $image->save($tmp);

        $this->assertEquals('Test', file_get_contents($tmp));
    }

    /**
     * @covers ::save
     * @expectedException \Exception
     * @expectedExceptionMessage Must provide a filename
     */
    public function testSaveWithNoFilename()
    {
        $image = $this->getMockForAbstractImage();
        $image->save();
    }

    /**
     * @return Image|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getMockForAbstractImage()
    {
        return $this->getMockForAbstractClass(Image::class);
    }
}
