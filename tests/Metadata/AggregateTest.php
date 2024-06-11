<?php

namespace Ycs77\ImageMetadata\Tests\Metadata;

use Exception;
use Mockery as m;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Ycs77\ImageMetadata\Metadata\Aggregate;
use Ycs77\ImageMetadata\Metadata\Iptc;
use Ycs77\ImageMetadata\Metadata\Xmp;

/**
 * Unit tests for {@see \Ycs77\ImageMetadata\Metadata\Aggregate}.
 */
class AggregateTest extends TestCase
{
    public static function getXmpAndIptcFields(): array
    {
        return [
            ['headline'],
            ['caption'],
            ['location'],
            ['city'],
            ['state'],
            ['country'],
            ['countryCode'],
            ['photographerName'],
            ['credit'],
            ['photographerTitle'],
            ['source'],
            ['copyright'],
            ['objectName'],
            ['captionWriters'],
            ['instructions'],
            ['category'],
            ['supplementalCategories'],
            ['transmissionReference'],
            ['urgency'],
            ['keywords'],
            ['dateCreated'],
        ];
    }

    /**
     * Test the meta fields which only have a value for XMP and IPTC, which is majority.
     */
    #[DataProvider('getXmpAndIptcFields')]
    public function testGetXmpIptcField($field)
    {
        $method = 'get'.ucfirst($field);

        $xmpValue = ($field == 'dateCreated') ? new \DateTime : 'XMP value';
        $iptcValue = ($field == 'dateCreated') ? new \DateTime : 'IPTC value';

        $xmp = m::mock(Xmp::class);
        $xmp->shouldReceive($method)->once()->andReturn($xmpValue);

        $iptc = m::mock(Iptc::class);
        $iptc->shouldReceive($method)->once()->andReturn($iptcValue);

        $aggregate = new Aggregate($xmp, $iptc);

        $this->assertEquals($xmpValue, $aggregate->$method());

        // change priority so IPTC is first
        $aggregate->setPriority(['iptc', 'xmp']);

        $this->assertEquals($iptcValue, $aggregate->$method());

        // change priority so nothing should be returned
        $aggregate->setPriority([]);

        $this->assertEquals(null, $aggregate->$method());
    }

    #[DataProvider('getXmpAndIptcFields')]
    public function testXmpIptcFallThrough($field)
    {
        $method = 'get'.ucfirst($field);

        $xmp = m::mock(Xmp::class);
        $xmp->shouldReceive($method)->once()->andReturnNull();

        $iptc = m::mock(Iptc::class);
        $iptc->shouldReceive($method)->once()->andReturn('IPTC value');

        $aggregate = new Aggregate($xmp, $iptc);

        // should always be IPTC as XMP returns null
        $this->assertEquals('IPTC value', $aggregate->$method());
    }

    /**
     * Test that all fields return null if no providers are set.
     */
    #[DataProvider('getXmpAndIptcFields')]
    public function testNullWhenNoProviders($field)
    {
        $reader = new Aggregate;

        $getter = 'get'.ucfirst($field);

        $this->assertNull($reader->$getter());
    }

    #[DataProvider('getXmpAndIptcFields')]
    public function testSetXmpIptcField($field)
    {
        $method = 'set'.ucfirst($field);
        $value = ($field == 'dateCreated') ? new \DateTime : 'value';

        $xmp = m::mock(Xmp::class);
        $xmp->shouldReceive($method)->once()->with($value);

        $iptc = m::mock(Iptc::class);
        $iptc->shouldReceive($method)->once()->with($value);

        $aggregate = new Aggregate($xmp, $iptc);

        $return = $aggregate->$method($value);

        $this->assertSame($aggregate, $return);
    }

    #[DataProvider('getXmpAndIptcFields')]
    public function testSetXmpIptcFieldWhenNoProviders($field)
    {
        $method = 'set'.ucfirst($field);
        $value = ($field == 'dateCreated') ? new \DateTime : 'value';

        $aggregate = new Aggregate;

        $return = $aggregate->$method($value);

        $this->assertSame($aggregate, $return);
    }

    public function testInvalidPriority()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Priority can only contain xmp, iptc or exif');

        $reader = new Aggregate;
        $reader->setPriority(['test']);
    }
}
