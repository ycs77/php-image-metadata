<?php

namespace Ycs77\ImageMetadata\Tests\Format;

use Exception;
use PHPUnit\Framework\TestCase;
use Ycs77\ImageMetadata\Format\PNG;
use Ycs77\ImageMetadata\Metadata\UnsupportedException;
use Ycs77\ImageMetadata\Metadata\Xmp;

class PNGTest extends TestCase
{
    /**
     * Test that a non-PNG file throws an exception.
     */
    public function testFromFileInvalidPNG()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid PNG file signature');

        PNG::fromFile(__DIR__.'/../Fixtures/nometa.jpg');
    }

    public function testGetXmpWithMetadataWrittenInPhotoshop()
    {
        $png = PNG::fromFile(__DIR__.'/../Fixtures/metaphotoshop.png');

        $xmp = $png->getXmp();

        $this->assertInstanceOf(XMP::class, $xmp);
        $this->assertEquals('Author', $xmp->getPhotographerName());
    }

    public function testGetXmpWithMetaWrittenInPhotoMechanic()
    {
        $png = PNG::fromFile(__DIR__.'/../Fixtures/metapm.png');

        $xmp = $png->getXmp();

        $this->assertInstanceOf(XMP::class, $xmp);
        $this->assertEquals('Headline', $xmp->getHeadline());
    }

    public function testGetXmpNoMeta()
    {
        $png = PNG::fromFile(__DIR__.'/../Fixtures/nometa.png');

        $xmp = $png->getXmp();

        $this->assertInstanceOf(XMP::class, $xmp);

        // check it's an empty XMP string
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>
<?xpacket begin="﻿" id="W5M0MpCehiHzreSzNTczkc9d"?>
<x:xmpmeta xmlns:x="adobe:ns:meta/"/>
<?xpacket end="w"?>
', $xmp->getString());
    }

    public function testFromFileValidPNG()
    {
        $png = PNG::fromFile(__DIR__.'/../Fixtures/nometa.png');

        $this->assertInstanceOf(PNG::class, $png);
    }

    public function testFromFileWithMalformedChunks()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid CRC for chunk with type: IHDR');

        PNG::fromFile(__DIR__.'/../Fixtures/malformedchunks.png');
    }

    public function testSavePNGWithNewMetaData()
    {
        $png = PNG::fromFile(__DIR__.'/../Fixtures/nometa.png');

        $png->getXmp()->setHeadline('PHP headline');

        $tmp = tempnam(sys_get_temp_dir(), 'PNG');

        $png->save($tmp);

        $newPng = PNG::fromFile($tmp);
        $this->assertEquals('PHP headline', $newPng->getXmp()->getHeadline());
    }

    public function testSavePNGWithUpdatedMetaData()
    {
        $png = PNG::fromFile(__DIR__.'/../Fixtures/metapm.png');
        $png->getXmp()->setHeadline('PHP headline');

        $tmp = tempnam(sys_get_temp_dir(), 'PNG');

        $png->save($tmp);

        $newPng = PNG::fromFile($tmp);
        $this->assertEquals('PHP headline', $newPng->getXmp()->getHeadline());
    }

    public function testSavePNGWithNewXmpObject()
    {
        $tmp = tempnam(sys_get_temp_dir(), 'PNG');

        $xmp = new Xmp;
        $xmp->setHeadline('PHP headline');

        $png = PNG::fromFile(__DIR__.'/../Fixtures/nometa.png');
        $png->setXmp($xmp);
        $png->save($tmp);

        $newPng = PNG::fromFile($tmp);
        $this->assertEquals('PHP headline', $newPng->getXmp()->getHeadline());
    }

    public function testSavePNGWithoutChanges()
    {
        $file = __DIR__.'/../Fixtures/nometa.png';
        $tmp = tempnam(sys_get_temp_dir(), 'PNG');

        $png = PNG::fromFile($file);
        $png->save($tmp);

        $this->assertEquals(file_get_contents($file), file_get_contents($tmp));
    }

    public function testGetExif()
    {
        $this->expectException(UnsupportedException::class);
        $this->expectExceptionMessage('PNG files do not support EXIF metadata');

        $png = PNG::fromFile(__DIR__.'/../Fixtures/nometa.png');
        $png->getExif();
    }

    public function testGetIptc()
    {
        $this->expectException(UnsupportedException::class);
        $this->expectExceptionMessage('PNG files do not support IPTC metadata');

        $png = PNG::fromFile(__DIR__.'/../Fixtures/nometa.png');
        $png->getIptc();
    }
}
