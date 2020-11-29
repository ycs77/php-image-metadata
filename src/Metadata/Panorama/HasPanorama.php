<?php

namespace Ycs77\ImageMetadata\Metadata\Panorama;

use Closure;

trait HasPanorama
{
    protected $GPanoNS = 'http://ns.google.com/photos/1.0/panorama/';

    protected function initializePanorama()
    {
        $this->namespaces['GPano'] = $this->GPanoNS;
    }

    public function getPanorama()
    {
        return $this->getRDFDescription($this->GPanoNS);
    }

    public function setPanorama(Closure $callback)
    {
        $description = $this->getOrCreateRDFDescription($this->GPanoNS);
        $description->setAttribute('rdf:about', '');

        $GPano = $callback(new GPano);

        foreach ($GPano as $key => $value) {
            $description->setAttribute("GPano:$key", $value);
        }

        return $this;
    }
}
