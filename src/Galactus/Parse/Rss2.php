<?php

namespace Galactus\Parse;

class Rss2 extends Atom
{

    public function extractItem($item)
    {
        $date = new \DateTime($item->pubDate, new \DateTimeZone('UTC'));
        $creationDate = $date->format('Y-m-d h:i:s');

        $content = $this->filterText($item->children('content', true)->encoded);
        if ('' == $content) {
            $content = $this->filterText($item->description);
        }

        return [
            'feedId' => $this->feedId,
            'remoteId' => md5($item->link),
            'title' => $item->title,
            'creationDate' => $creationDate,
            'content' => $content,
            'url' => $item->link
        ];

    }

    public function current()
    {
        return $this->extractItem($this->xml->channel->item[$this->position]);
    }

    public function valid()
    {
        return isset($this->xml->channel->item[$this->position]);
    }

}