<?php

namespace Ycs77\ImageMetadata\Tests\Metadata\Reader;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Ycs77\ImageMetadata\Metadata\Iptc;

class IptcTest extends TestCase
{
    /**
     * @var Iptc
     */
    private $meta;

    protected function setUp(): void
    {
        $this->meta = Iptc::fromFile(__DIR__.'/../Fixtures/metapm.jpg');
    }

    public static function getMetaFields(): array
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
            ['source'],
            ['photographerTitle'],
            ['copyright'],
            ['objectName'],
            ['captionWriters'],
            ['instructions'],
            ['category'],
            ['supplementalCategories'],
            ['transmissionReference'],
            ['urgency'],
            ['keywords'],
        ];
    }

    public function testHeadline()
    {
        $this->assertEquals('Headline', $this->meta->getHeadline());
    }

    public function tsestCaption()
    {
        $this->assertEquals(
            'José Mourinho',
            $this->meta->getCaption()
        );
    }

    public function tesstKeywords()
    {
        $this->assertEquals(
            'Canvey Island, Carshalton Athletic, England, Essex, Football, Ryman Isthmian Premier League, Soccer, '.
            'Sport, Sports, The Prospects Stadium',
            $this->meta->getKeywords()
        );
    }

    public function tesstCategory()
    {
        $this->assertEquals('SPO', $this->meta->getCategory());
    }

    #[DataProvider('getMetaFields')]
    public function testGetSetMeta($field)
    {
        $setter = 'set'.ucfirst($field);

        $value = 'test';

        $iptc = new Iptc;
        $return = $iptc->$setter($value);

        $this->assertSame($iptc, $return);

        $getter = 'get'.ucfirst($field);

        $this->assertSame($value, $iptc->$getter());
    }

    #[DataProvider('getMetaFields')]
    public function testHasChanges($field)
    {
        $setter = 'set'.ucfirst($field);

        $value = 'test';

        $iptc = new Iptc;

        $this->assertFalse($iptc->hasChanges());

        $iptc->$setter($value);

        $this->assertTrue($iptc->hasChanges());
    }

    #[DataProvider('getMetaFields')]
    public function testNull($field)
    {
        $getter = 'get'.ucfirst($field);

        $iptc = new Iptc;

        $this->assertNull($iptc->$getter());
    }

    public function testAll()
    {
        $iptc = new Iptc;

        $this->assertSame([], $iptc->all());

        $iptc->setHeadline('Headline');
        $iptc->setCaption('Caption');

        $this->assertSame(
            ['2#105' => ['Headline'], '2#120' => ['Caption']],
            $iptc->all()
        );
    }
}
