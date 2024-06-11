<?php

namespace Ycs77\ImageMetadata;

use Ycs77\ImageMetadata\Metadata\Aggregate;
use Ycs77\ImageMetadata\Metadata\Exif;
use Ycs77\ImageMetadata\Metadata\Iptc;
use Ycs77\ImageMetadata\Metadata\UnsupportedException;
use Ycs77\ImageMetadata\Metadata\Xmp;

interface ImageInterface
{
    /**
     * @param $filename
     * @return bool
     */
    public function save($filename = null);

    /**
     * @return string
     */
    public function getBytes();

    /**
     * @param $filename
     */
    public function setFilename($filename);

    /**
     * @return Xmp
     *
     * @throws UnsupportedException
     */
    public function getXmp();

    /**
     * @return Exif
     *
     * @throws UnsupportedException
     */
    public function getExif();

    /**
     * @return Iptc
     *
     * @throws UnsupportedException
     */
    public function getIptc();

    /**
     * @return Aggregate
     */
    public function getAggregate();

    public static function fromFile($filename);
}
