<?php

namespace Galactus\Service\Rss;

class Channel
{

    public $title;
    public $atom_link;
    public $link;
    public $description;
    public $lastBuildDate;
    public $language;
    public $generator;

    public function __construct(array $data)
    {
        foreach (get_object_vars($this) as $name => $value) {
            $this->$name = $data[$name];
        }
    }

}