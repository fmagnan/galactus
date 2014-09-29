<?php

namespace Galactus\Parse;

class Rss extends Atom
{


    public function extractPost($feedId)
    {
        foreach ($this->xml->channel->item as $post) {
            $date = new \DateTime($post->pubDate, new \DateTimeZone('UTC'));
            $creationDate = $date->format('Y-m-d h:i:s');

            $description = $this->filterText($post->description);
            $content = $this->filterText($post->children('content', true)->encoded);

            yield [
                'feedId' => $feedId,
                'remoteId' => md5($post->link),
                'title' => $post->title,
                'creationDate' => $creationDate,
                'description' => $description,
                'content' => $content,
                'url' => $post->link
            ];
        }
    }


}