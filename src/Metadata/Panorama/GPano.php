<?php

namespace Ycs77\ImageMetadata\Metadata\Panorama;

use ArrayIterator;
use IteratorAggregate;

/**
 * @see https://developers.google.com/streetview/spherical-metadata
 */
class GPano implements IteratorAggregate
{
    protected $attributes = [];

    public function usePanoramaViewer(bool $usePanoramaViewer)
    {
        return $this->setAttribute('UsePanoramaViewer', $usePanoramaViewer);
    }

    public function captureSoftware(string $captureSoftware)
    {
        return $this->setAttribute('CaptureSoftware', $captureSoftware);
    }

    public function stitchingSoftware(string $stitchingSoftware)
    {
        return $this->setAttribute('StitchingSoftware', $stitchingSoftware);
    }

    public function projectionType(string $projectionType = 'equirectangular')
    {
        return $this->setAttribute('ProjectionType', $projectionType);
    }

    public function poseHeadingDegrees(int $poseHeadingDegrees)
    {
        return $this->setAttribute('PoseHeadingDegrees', $poseHeadingDegrees);
    }

    public function posePitchDegrees(int $posePitchDegrees)
    {
        return $this->setAttribute('PosePitchDegrees', $posePitchDegrees);
    }

    public function poseRollDegrees(int $poseRollDegrees)
    {
        return $this->setAttribute('PoseRollDegrees', $poseRollDegrees);
    }

    public function initialViewHeadingDegrees(int $initialViewHeadingDegrees)
    {
        return $this->setAttribute('InitialViewHeadingDegrees', $initialViewHeadingDegrees);
    }

    public function initialViewPitchDegrees(int $initialViewPitchDegrees)
    {
        return $this->setAttribute('InitialViewPitchDegrees', $initialViewPitchDegrees);
    }

    public function initialViewRollDegrees(int $initialViewRollDegrees)
    {
        return $this->setAttribute('InitialViewRollDegrees', $initialViewRollDegrees);
    }

    public function initialHorizontalFOVDegrees(int $initialHorizontalFOVDegrees)
    {
        return $this->setAttribute('InitialHorizontalFOVDegrees', $initialHorizontalFOVDegrees);
    }

    public function firstPhotoDate(string $firstPhotoDate)
    {
        return $this->setAttribute('FirstPhotoDate', $firstPhotoDate);
    }

    public function lastPhotoDate(string $lastPhotoDate)
    {
        return $this->setAttribute('LastPhotoDate', $lastPhotoDate);
    }

    public function sourcePhotosCount(int $sourcePhotosCount)
    {
        return $this->setAttribute('SourcePhotosCount', $sourcePhotosCount);
    }

    public function exposureLockUsed(bool $exposureLockUsed)
    {
        return $this->setAttribute('ExposureLockUsed', $exposureLockUsed);
    }

    public function croppedAreaImageWidthPixels(int $croppedAreaImageWidthPixels)
    {
        return $this->setAttribute('CroppedAreaImageWidthPixels', $croppedAreaImageWidthPixels);
    }

    public function croppedAreaImageHeightPixels(int $croppedAreaImageHeightPixels)
    {
        return $this->setAttribute('CroppedAreaImageHeightPixels', $croppedAreaImageHeightPixels);
    }

    public function fullPanoWidthPixels(int $fullPanoWidthPixels)
    {
        return $this->setAttribute('FullPanoWidthPixels', $fullPanoWidthPixels);
    }

    public function fullPanoHeightPixels(int $fullPanoHeightPixels)
    {
        return $this->setAttribute('FullPanoHeightPixels', $fullPanoHeightPixels);
    }

    public function croppedAreaLeftPixels(int $croppedAreaLeftPixels)
    {
        return $this->setAttribute('CroppedAreaLeftPixels', $croppedAreaLeftPixels);
    }

    public function croppedAreaTopPixels(int $croppedAreaTopPixels)
    {
        return $this->setAttribute('croppedAreaTopPixels', $croppedAreaTopPixels);
    }

    public function initialCameraDolly(int $initialCameraDolly)
    {
        return $this->setAttribute('InitialCameraDolly', $initialCameraDolly);
    }

    protected function setAttribute($attribute, $value)
    {
        $this->attributes[$attribute] = $value;

        return $this;
    }

    protected function getAttribute($attribute)
    {
        return $this->attributes[$attribute];
    }

    public function get()
    {
        return $this->attributes;
    }

    public function getIterator() {
        return new ArrayIterator($this->attributes);
    }
}
