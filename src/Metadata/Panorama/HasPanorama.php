<?php

namespace Ycs77\ImageMetadata\Metadata\Panorama;

use Closure;
use DOMElement;

/**
 * @property \DomDocument $dom
 * @property \DOMXPath $xpath
 */
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
        $description->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:GPano', $this->GPanoNS);
        $description->setAttribute('rdf:about', '');

        $GPano = $callback(new GPano);

        foreach ($GPano as $key => $value) {
            // $description->setAttribute("GPano:$key", $value);

            if ($element = $this->xpath->query("GPano:$key", $description)->item(0)) {
                $element->nodeValue = $value;
                continue;
            }

            if ($element = $this->dom->createElement("GPano:$key", $value)) {
                $description->appendChild($element);
            }
        }

        return $this;
    }
}
