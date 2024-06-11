<?php

namespace Ycs77\ImageMetadata\Tests\Metadata;

use PHPUnit\Framework\TestCase;
use Ycs77\ImageMetadata\Metadata\Panorama\GPano;
use Ycs77\ImageMetadata\Metadata\Xmp;

class PanoramaXmpTest extends TestCase
{
    public function testSetPanoramaXmlMetadata()
    {
        $xmp = new Xmp;

        $xmp->setPanorama(function (GPano $gPano) {
            return $gPano
                ->projectionType()
                ->usePanoramaViewer(true)
                ->poseHeadingDegrees(0)
                ->croppedAreaImageWidthPixels(8192)
                ->croppedAreaImageHeightPixels(4096)
                ->fullPanoWidthPixels(8192)
                ->fullPanoHeightPixels(4096)
                ->croppedAreaLeftPixels(0)
                ->croppedAreaTopPixels(0)
                ->stitchingSoftware('Your App Name');
        });

        $this->assertStringContainsString(implode('', [
            '<rdf:Description xmlns:GPano="http://ns.google.com/photos/1.0/panorama/" rdf:about="">',
            '<GPano:ProjectionType>equirectangular</GPano:ProjectionType>',
            '<GPano:UsePanoramaViewer>True</GPano:UsePanoramaViewer>',
            '<GPano:PoseHeadingDegrees>0</GPano:PoseHeadingDegrees>',
            '<GPano:CroppedAreaImageWidthPixels>8192</GPano:CroppedAreaImageWidthPixels>',
            '<GPano:CroppedAreaImageHeightPixels>4096</GPano:CroppedAreaImageHeightPixels>',
            '<GPano:FullPanoWidthPixels>8192</GPano:FullPanoWidthPixels>',
            '<GPano:FullPanoHeightPixels>4096</GPano:FullPanoHeightPixels>',
            '<GPano:CroppedAreaLeftPixels>0</GPano:CroppedAreaLeftPixels>',
            '<GPano:croppedAreaTopPixels>0</GPano:croppedAreaTopPixels>',
            '<GPano:StitchingSoftware>Your App Name</GPano:StitchingSoftware>',
            '</rdf:Description>',
        ]), $xmp->getString());
    }
}
