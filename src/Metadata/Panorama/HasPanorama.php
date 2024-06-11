<?php

namespace Ycs77\ImageMetadata\Metadata\Panorama;

use Closure;
use DOMElement;

trait HasPanorama
{
    protected string $GPanoNS = 'http://ns.google.com/photos/1.0/panorama/';

    protected function initializePanorama(): void
    {
        $this->namespaces['GPano'] = $this->GPanoNS;
    }

    public function getPanorama(): ?DOMElement
    {
        return $this->getRDFDescription($this->GPanoNS);
    }

    public function setPanorama(Closure $callback)
    {
        /** @var \DOMElement */
        $description = $this->getOrCreateRDFDescription($this->GPanoNS);
        $description->setAttribute('rdf:about', '');

        $GPano = $callback(new GPano);

        foreach ($GPano as $key => $value) {
            $description->setAttribute("GPano:$key", $value);
        }

        return $this;
    }
}
