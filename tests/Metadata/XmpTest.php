<?php

namespace Ycs77\ImageMetadata\Tests\Metadata;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Ycs77\ImageMetadata\Metadata\Xmp;

class XmpTest extends TestCase
{
    public static function getDataForAllFile(): array
    {
        return [
            ['headline', 'Headline'],
            ['caption', 'José Mourinho'],
            ['keywords', ['A keyword', 'Another keyword']],
            ['category', 'SPO'],
            ['contactZip', 'NW1 1AA'],
            ['contactEmail', 'sales@example.com'],
            ['contactCountry', 'England'],
            ['contactAddress', '123 Street Road'],
            ['contactCity', 'London'],
            ['contactUrl', 'http://www.example.com'],
            ['contactPhone', '+44 7901 123456'],
            ['contactState', 'Greater London'],
            ['transmissionReference', 'JOB001'],
            ['objectName', 'OBJECT_NAME'],
            ['instructions', 'All rights reserved.'],
            ['captionWriters', 'Description Writers'],
            ['rightsUsageTerms', 'All rights reserved.'],
            ['event', 'Event Name'],
            ['city', 'London'],
            ['state', 'Greater London'],
            ['location', 'Buckingham Palace'],
            ['country', 'England'],
            ['countryCode', 'GBR'],
            ['IPTCSubjectCodes', ['subj:15054000']],
            ['photographerName', 'Photographer'],
            ['photographerTitle', 'Staff'],
            ['copyrightUrl', 'www.example.com'],
            ['source', 'example.com'],
            ['copyright', 'example.com'],
            ['credit', 'Photographer/Agency'],
            ['urgency', '2'],
            ['rating', '4'],
            ['creatorTool', 'Creator Tool'],
            ['intellectualGenre', 'Intellectual genre'],
            ['supplementalCategories', ['Football', 'Soccer', 'Sport']],
            ['personsShown', ['A person', 'Another person']],
            ['featuredOrganisationName', ['Featured Organisation']],
            ['featuredOrganisationCode', ['Featured Organisation Code']],
            ['IPTCScene', ['IPTC Scene']],
        ];
    }

    public static function getAltFields(): array
    {
        return [
            ['caption', 'dc:description'],
            ['objectName', 'dc:title'],
            ['copyright', 'dc:rights'],
            ['rightsUsageTerms', 'xmpRights:UsageTerms'],
        ];
    }

    public static function getAttrFields(): array
    {
        return [
            ['location', 'Iptc4xmpCore:Location'],
            ['contactPhone', 'Iptc4xmpCore:CiTelWork'],
            ['contactAddress', 'Iptc4xmpCore:CiAdrExtadr'],
            ['contactCity', 'Iptc4xmpCore:CiAdrCity'],
            ['contactState', 'Iptc4xmpCore:CiAdrRegion'],
            ['contactZip', 'Iptc4xmpCore:CiAdrPcode'],
            ['contactCountry', 'Iptc4xmpCore:CiAdrCtry'],
            ['contactEmail', 'Iptc4xmpCore:CiEmailWork'],
            ['contactUrl', 'Iptc4xmpCore:CiUrlWork'],
            ['city', 'photoshop:City'],
            ['state', 'photoshop:State'],
            ['country', 'photoshop:Country'],
            ['countryCode', 'Iptc4xmpCore:CountryCode'],
            ['credit', 'photoshop:Credit'],
            ['source', 'photoshop:Source'],
            ['copyrightUrl', 'xmpRights:WebStatement'],
            ['captionWriters', 'photoshop:CaptionWriter'],
            ['instructions', 'photoshop:Instructions'],
            ['category', 'photoshop:Category'],
            ['urgency', 'photoshop:Urgency'],
            ['rating', 'xmp:Rating'],
            ['creatorTool', 'xmp:CreatorTool'],
            ['photographerTitle', 'photoshop:AuthorsPosition'],
            ['transmissionReference', 'photoshop:TransmissionReference'],
            ['headline', 'photoshop:Headline'],
            ['event', 'Iptc4xmpExt:Event'],
            ['intellectualGenre', 'Iptc4xmpCore:IntellectualGenre'],
        ];
    }

    public static function getBagFields(): array
    {
        return [
            ['keywords', 'dc:subject'],
            ['personsShown', 'Iptc4xmpExt:PersonInImage'],
            ['iptcSubjectCodes', 'Iptc4xmpCore:SubjectCode'],
            ['supplementalCategories', 'photoshop:SupplementalCategories'],
        ];
    }

    #[DataProvider('getDataForAllFile')]
    public function testGetDataFromAllFile($field, $value)
    {
        $getter = 'get'.ucfirst($field);

        $xmp = $this->getXmpFromFile();
        $this->assertEquals($value, $xmp->$getter());

        $xmp = $this->getXmpFromFile2();
        $this->assertEquals($value, $xmp->$getter());
    }

    #[DataProvider('getAltFields')]
    public function testSetAltFields($field, $xmlField)
    {
        $this->assertValidList('rdf:Alt', $field, $xmlField, $field);
    }

    #[DataProvider('getBagFields')]
    public function testSetBagFields($field, $xmlField)
    {
        $this->assertValidList('rdf:Bag', $field, $xmlField, $field);
        $this->assertValidList('rdf:Bag', $field, $xmlField, [$field, $field]);
    }

    #[DataProvider('getAttrFields')]
    public function testSetAttrFields($field, $xmlField)
    {
        $value = 'A test string, with utf €åƒ∂, and some xml chars such as <>"';
        $expectedAttr = $xmlField.'="A test string, with utf €åƒ∂, and some xml chars such as &lt;&gt;&quot;"';
        $expectedElement = '<'.$xmlField.'>A test string, with utf €åƒ∂, and some xml chars such as &lt;&gt;"</'.$xmlField.'>';

        $setter = 'set'.ucfirst($field);

        // test with no meta data
        $xmp = new Xmp;
        $xmp->$setter($value);

        $this->assertStringContainsString($expectedAttr, $xmp->getString());

        // test with empty meta data
        $xmp = new Xmp('<x:xmpmeta xmlns:x="adobe:ns:meta/" />');
        $xmp->$setter($value);

        $this->assertStringContainsString($expectedAttr, $xmp->getString());

        // test with existing meta data
        $xmp = $this->getXmpFromFile();
        $xmp->$setter($value);

        $this->assertStringContainsString($expectedAttr, $xmp->getString());

        // test with existing meta data
        $xmp = $this->getXmpFromFile2();
        $xmp->$setter($value);

        $this->assertStringContainsString($expectedElement, $xmp->getString());
    }

    public function testSetPhotographerName()
    {
        $this->assertValidList('rdf:Seq', 'photographerName', 'dc:creator', 'Photographer Name');
    }

    public function testGetToolkit()
    {
        $xmp = $this->getXmpFromFile();

        $this->assertEquals('XMP Core 5.1.2', $xmp->getToolkit());
    }

    public function testEmptyToolkit()
    {
        $xmp = new Xmp;
        $this->assertNull($xmp->getToolkit());
    }

    public function testSetToolkit()
    {
        $xmp = new Xmp;
        $xmp->setToolkit('Toolkit 1.2.3');

        $this->assertStringContainsString('x:xmptk="Toolkit 1.2.3"', $xmp->getString());
    }

    public function testXmpContainsProcessingInstructions()
    {
        $this->assertXmpContainsProcessingInstructions(new Xmp);
        $this->assertXmpContainsProcessingInstructions(new Xmp('<x:xmpmeta xmlns:x="adobe:ns:meta/" />'));
        $this->assertXmpContainsProcessingInstructions($this->getXmpFromFile());
    }

    #[DataProvider('getDataForAllFile')]
    public function testFromArray($field, $value)
    {
        $getter = 'get'.ucfirst($field);

        $xmp = Xmp::fromArray([$field => $value]);

        $this->assertEquals($value, $xmp->$getter());
    }

    #[DataProvider('getDataForAllFile')]
    public function testGetNonExistentValue($field)
    {
        $getter = 'get'.ucfirst($field);

        $xmp = new Xmp;
        $this->assertNull($xmp->$getter());
    }

    /**
     * Test that changing a single piece of metadata changes state of hasChanges.
     */
    #[DataProvider('getDataForAllFile')]
    public function testHasChanges($field, $value)
    {
        $setter = 'set'.ucfirst($field);

        $xmp = new Xmp;

        $this->assertFalse($xmp->hasChanges());

        $xmp->$setter($value);

        $this->assertTrue($xmp->hasChanges());
    }

    /**
     * Test that a rdf:Bag item returns null when the tag is set but there are no items.
     */
    public function testGetEmptyBagValue()
    {
        $xmp = new Xmp('<x:xmpmeta xmlns:x="adobe:ns:meta/" x:xmptk="XMP Core 5.1.2">
             <rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">
              <rdf:Description rdf:about=""
                xmlns:photoshop="http://ns.adobe.com/photoshop/1.0/">
               <photoshop:SupplementalCategories />
              </rdf:Description>
             </rdf:RDF>
            </x:xmpmeta>
        ');

        $this->assertNull($xmp->getSupplementalCategories());
    }

    /**
     * Test that a rdf:Bag item returns null when the tag is set but there are no items.
     */
    public function testGetEmptySeqValue()
    {
        $xmp = new Xmp('<x:xmpmeta xmlns:x="adobe:ns:meta/" x:xmptk="XMP Core 5.1.2">
             <rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">
              <rdf:Description rdf:about=""
                xmlns:dc="http://purl.org/dc/elements/1.1/">
               <dc:creator />
              </rdf:Description>
             </rdf:RDF>
            </x:xmpmeta>
        ');

        $this->assertNull($xmp->getPhotographerName());
    }

    /**
     * Test that a rdf:Alt item returns null when the tag is set but there are no items.
     */
    public function testGetEmptyAltValue()
    {
        $xmp = new Xmp('<x:xmpmeta xmlns:x="adobe:ns:meta/" x:xmptk="XMP Core 5.1.2">
             <rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">
              <rdf:Description rdf:about=""
                xmlns:xmpRights="http://ns.adobe.com/xap/1.0/rights/">
               <xmpRights:UsageTerms />
              </rdf:Description>
             </rdf:RDF>
            </x:xmpmeta>
        ');

        $this->assertNull($xmp->getRightsUsageTerms());
    }

    public function testEmptyContactValue()
    {
        $xmp = new Xmp('<x:xmpmeta xmlns:x="adobe:ns:meta/" x:xmptk="XMP Core 5.1.2">
             <rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">
              <rdf:Description rdf:about=""
                xmlns:Iptc4xmpCore="http://iptc.org/std/Iptc4xmpCore/1.0/xmlns/">
               <Iptc4xmpCore:CreatorContactInfo />
              </rdf:Description>
             </rdf:RDF>
            </x:xmpmeta>
        ');

        $this->assertNull($xmp->getContactCity());
    }

    public function testAbout()
    {
        $xmp = new Xmp;

        // should be empty string by default
        $this->assertSame('', $xmp->getAbout());

        $xmp->setAbout('about');

        $this->assertSame('about', $xmp->getAbout());
    }

    public function testFormatOutput()
    {
        $xmp = new Xmp;

        $this->assertFalse($xmp->getFormatOutput());

        $return = $xmp->setFormatOutput(true);

        $this->assertSame($xmp, $return);
        $this->assertTrue($xmp->getFormatOutput());
    }

    public function testDeleteList()
    {
        $xmp = new Xmp;

        $xmp->setSupplementalCategories(['a category', 'another category']);
        $xmp->setSupplementalCategories([]);

        $this->assertStringNotContainsString('photoshop:SupplementalCategories', $xmp->getString());
    }

    #[DataProvider('getAttrFields')]
    public function testSetNullAttribute($field, $xmlField)
    {
        $setter = 'set'.ucfirst($field);

        $xmp = new Xmp;
        $xmp->$setter($field);
        $xmp->$setter(null);

        $this->assertStringNotContainsString($xmlField, $xmp->getString());

        $xmp = $this->getXmpFromFile();
        $xmp->$setter(null);

        $this->assertStringNotContainsString($xmlField, $xmp->getString());

        $xmp = $this->getXmpFromFile2();
        $xmp->$setter(null);

        $this->assertStringNotContainsString($xmlField, $xmp->getString());
    }

    public function testDateCreated()
    {
        $xmp = new Xmp;

        $this->assertNull($xmp->getDateCreated());

        $xmp = new Xmp;
        $xmp->setDateCreated($date = new \DateTime('now'));
        $this->assertEquals($date->format('c'), $xmp->getDateCreated()->format('c'));

        $xmp = new Xmp;
        $xmp->setDateCreated($date = new \DateTime('now'), 'Y');
        $this->assertEquals($date->format('Y'), $xmp->getDateCreated()->format('Y'));

        $xmp = new Xmp;
        $xmp->setDateCreated($date = new \DateTime('now'), 'Y-m');
        $this->assertEquals($date->format('Y-m'), $xmp->getDateCreated()->format('Y-m'));

        $xmp = new Xmp;
        $xmp->setDateCreated($date = new \DateTime('now'), 'Y-m-d');
        $this->assertEquals($date->format('Y-m-d'), $xmp->getDateCreated()->format('Y-m-d'));

        // test with invalid date
        $xmp = new Xmp('
            <x:xmpmeta xmlns:x="adobe:ns:meta/" x:xmptk="XMP Core 5.1.2">
              <rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">
                <rdf:Description rdf:about=""
                  xmlns:photoshop="http://ns.adobe.com/photoshop/1.0/"
                  photoshop:DateCreated="DATE" />
              </rdf:RDF>
            </x:xmpmeta>
        ');

        $this->assertFalse($xmp->getDateCreated());
    }

    /**
     * Test that the reader only accepts valid XMP root tag.
     */
    public function testInvalidXmlException()
    {
        $this->expectException(RuntimeException::class);

        new Xmp('<myelement />');
    }

    public function testFromFile()
    {
        $this->assertInstanceOf(Xmp::class, Xmp::fromFile(__DIR__.'/../Fixtures/all.XMP'));
    }

    private function assertXmpContainsProcessingInstructions(Xmp $xmp)
    {
        $this->assertStringContainsString("<?xpacket begin=\"\xef\xbb\xbf\" id=\"W5M0MpCehiHzreSzNTczkc9d\"?>", $xmp->getString());
        $this->assertStringContainsString('<?xpacket end="w"?>', $xmp->getString());
    }

    private function assertValidList($type, $field, $xmlField, $value)
    {
        $attributes = ($type == 'rdf:Alt') ? ' xml:lang="x-default"' : '';

        $expected = '<'.$xmlField.'><'.$type.'>';

        foreach ((array) $value as $li) {
            $expected .= '<rdf:li'.$attributes.'>'.$li.'</rdf:li>';
        }

        $expected .= '</'.$type.'></'.$xmlField.'>';

        $setter = 'set'.ucfirst($field);

        $xmp = new Xmp;
        $xmp->$setter($value);

        $this->assertStringContainsString($expected, $xmp->getString());

        // test setting value on existing meta data
        $xmp = $this->getXmpFromFile();
        $xmp->$setter($value);

        $this->assertStringContainsString($expected, $xmp->getString());

        // test setting value on existing meta data
        $xmp = $this->getXmpFromFile2();
        $xmp->$setter($value);

        $this->assertStringContainsString($expected, $xmp->getString());
    }

    /**
     * Gets XMP file where the data is written as attributes.
     *
     * @return Xmp
     */
    private function getXmpFromFile()
    {
        return new Xmp(file_get_contents(__DIR__.'/../Fixtures/all.XMP'));
    }

    /**
     * Gets XMP file where the data is written as elements.
     *
     * @return Xmp
     */
    private function getXmpFromFile2()
    {
        return new Xmp(file_get_contents(__DIR__.'/../Fixtures/all2.XMP'));
    }
}
