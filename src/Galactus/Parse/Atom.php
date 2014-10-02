<?php

namespace Galactus\Parse;

class Atom implements \Iterator
{

    protected $xml;
    protected $feedId;
    protected $position;

    public function __construct(\SimpleXMLElement $xml, $feedId)
    {
        $this->xml = $xml;
        $this->feedId = $feedId;
        $this->position = 0;
    }

    public function extractItem($item)
    {
        $date = new \DateTime($item->published, new \DateTimeZone('UTC'));
        $creationDate = $date->format('Y-m-d h:i:s');

        echo $creationDate;

        $content = $this->filterText($item->content);
        $link = $item->link->attributes()->href;

        return [
            'feedId' => $this->feedId,
            'remoteId' => md5($link),
            'title' => $item->title,
            'creationDate' => $creationDate,
            'content' => $content,
            'url' => $link
        ];
    }

    protected function filterText($string)
    {
        $string = html_entity_decode($string);

        return $string;
    }

    public function current()
    {
        return $this->extractItem($this->xml->entry[$this->position]);
    }

    public function valid()
    {
        return isset($this->xml->entry[$this->position]);
    }

    public function next()
    {
        $this->position++;
    }

    public function key()
    {
        //@todo
    }

    public function rewind()
    {
        //@todo
    }
}