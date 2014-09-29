<?php

namespace Galactus\Parse;

class Atom
{

    protected $xml;

    public function __construct(\SimpleXMLElement $xml)
    {
        $this->xml = $xml;
    }

    public function extractPost($feedId)
    {
        //todo or not
    }

    protected function filterText($string)
    {
        $string = html_entity_decode($string);

        return $string;
    }

}