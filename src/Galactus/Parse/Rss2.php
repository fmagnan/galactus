<?php

namespace Galactus\Parse;

class Rss2 extends Atom
{

    public function extractPost($feedId)
    {
        foreach ($this->xml->channel->item as $post) {
            $date = new \DateTime($post->pubDate, new \DateTimeZone('UTC'));
            $creationDate = $date->format('Y-m-d h:i:s');

            $content = $this->filterText($post->children('content', true)->encoded);
            if ('' == $content) {
                $content = $this->filterText($post->description);
            }

            yield [
                'feedId' => $feedId,
                'remoteId' => md5($post->link),
                'title' => $post->title,
                'creationDate' => $creationDate,
                'content' => $content,
                'url' => $post->link
            ];
        }
    }

}