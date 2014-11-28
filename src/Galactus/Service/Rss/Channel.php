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

    public function __construct(array $data, $prefix = '')
    {
        foreach (get_object_vars($this) as $name => $value) {
            $key = $prefix . $name;
            if (isset($data[$key])) {
                $this->$key = $data[$key];
            }
            if (isset($data[$name])) {
                $this->$name = $data[$name];
            }

        }
    }

}