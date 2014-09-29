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
        foreach ($this->xml->entry as $post) {
            $date = new \DateTime($post->published, new \DateTimeZone('UTC'));
            $creationDate = $date->format('Y-m-d h:i:s');

            $content = $this->filterText($post->content);
            $link = $post->link->attributes()->href;

            yield [
                'feedId' => $feedId,
                'remoteId' => md5($link),
                'title' => $post->title,
                'creationDate' => $creationDate,
                'content' => $content,
                'url' => $link
            ];
        }
    }

    protected function filterText($string)
    {
        $string = html_entity_decode($string);

        return $string;
    }

}