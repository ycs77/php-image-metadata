<?php

namespace Ycs77\ImageMetadata\Tests\Format;

use PHPUnit\Framework\TestCase;
use Ycs77\ImageMetadata\Format\PNG\Chunk;

class ChunkTest extends TestCase
{
    public function testGetters()
    {
        $chunk = new Chunk('iTXt', 'data');

        $this->assertEquals('iTXt', $chunk->getType());
        $this->assertEquals('data', $chunk->getData());
    }

    public function testGetLength()
    {
        $chunk = new Chunk('iTXt', 'data');

        $this->assertEquals(4, $chunk->getLength());
    }

    public function testGetCRC()
    {
        $chunk = new Chunk('iTXt', 'data');

        $this->assertEquals('1d2449b7', bin2hex($chunk->getCrc()));
    }

    public function testGetChunk()
    {
        $chunk = new Chunk('iTXt', 'data');

        $this->assertEquals('0000000469545874646174611d2449b7', bin2hex($chunk->getChunk()));
    }

    public function testSetData()
    {
        $chunk = new Chunk('iTXt', 'data');
        $chunk->setData('newdata');

        $this->assertEquals('newdata', $chunk->getData());
    }
}
