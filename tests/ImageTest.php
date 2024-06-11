<?php

namespace Ycs77\ImageMetadata\Tests;

use Exception;
use PHPUnit\Framework\TestCase;
use Ycs77\ImageMetadata\Format\JPEG;
use Ycs77\ImageMetadata\Format\PNG;
use Ycs77\ImageMetadata\Image;

class ImageTest extends TestCase
{
    public function testPNG()
    {
        $image = Image::fromFile(__DIR__.'/Fixtures/nometa.png');
        $this->assertInstanceOf(PNG::class, $image);
    }

    public function testJPG()
    {
        $image = Image::fromFile(__DIR__.'/Fixtures/nometa.jpg');
        $this->assertInstanceOf(JPEG::class, $image);
    }

    public function testUppercase()
    {
        $image = Image::fromFile(__DIR__.'/Fixtures/UPPERCASE.JPG');
        $this->assertInstanceOf(JPEG::class, $image);
    }

    public function testJPEG()
    {
        $image = Image::fromFile(__DIR__.'/Fixtures/nometa.jpeg');
        $this->assertInstanceOf(JPEG::class, $image);
    }

    public function testInvalidFile()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Unrecognised file name');

        Image::fromFile(__FILE__);
    }
}
