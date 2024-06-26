<?php

namespace Ycs77\ImageMetadata;

use Ycs77\ImageMetadata\Format\JPEG;
use Ycs77\ImageMetadata\Format\PNG;
use Ycs77\ImageMetadata\Format\WebP;
use Ycs77\ImageMetadata\Metadata\Aggregate;
use Ycs77\ImageMetadata\Metadata\UnsupportedException;

abstract class Image implements ImageInterface
{
    /**
     * @var string
     */
    protected $filename;

    /**
     * @param  string  $filename
     * @return $this
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * @return Aggregate
     */
    public function getAggregate()
    {
        try {
            $xmp = $this->getXmp();
        } catch (UnsupportedException $e) {
            $xmp = null;
        }

        try {
            $exif = $this->getExif();
        } catch (UnsupportedException $e) {
            $exif = null;
        }

        try {
            $iptc = $this->getIptc();
        } catch (UnsupportedException $e) {
            $iptc = null;
        }

        return new Aggregate($xmp, $iptc, $exif);
    }

    /**
     * {@inheritdoc}
     */
    public function save($filename = null)
    {
        $filename = $filename ?: $this->filename;

        if (! $filename) {
            throw new \Exception('Must provide a filename');
        }

        file_put_contents($filename, $this->getBytes());
    }

    /**
     * @param  string  $fileName
     * @return ImageInterface
     *
     * @throws \Exception
     *
     * @todo add more sophisticated checks by inspecting file
     */
    public static function fromFile($fileName)
    {
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        switch ($ext) {
            case 'jpg':
            case 'jpeg':
                return Format\JPEG::fromFile($fileName);
                break;
            case 'png':
                return Format\PNG::fromFile($fileName);
                break;
            case 'webp':
                return Format\WebP::fromFile($fileName);
                break;
            case 'psd':
                return Format\PSD::fromFile($fileName);
                break;
        }

        throw new \Exception('Unrecognised file name');
    }

    /**
     * @return JPEG|WebP|PNG|false
     */
    public static function fromString($string)
    {
        $len = strlen($string);

        // try JPEG
        if ($len >= 2) {
            if (substr($string, 0, 2) === JPEG::SOI) {
                return JPEG::fromString($string);
            }
        }

        // try WebP
        if ($len >= 4) {
            if (substr($string, 0, 4) === 'RIFF' && substr($string, 8, 4) === 'WEBP') {
                return WebP::fromString($string);
            }
        }

        // try PNG
        if ($len >= 8) {
            if (substr($string, 0, 8) === PNG::SIGNATURE) {
                return PNG::fromString($string);
            }
        }

        return false;
    }
}
