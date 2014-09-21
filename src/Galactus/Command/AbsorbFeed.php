<?php

namespace Galactus\Command;

use GuzzleHttp\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbsorbFeed extends Command
{
    protected $name;
    protected $uri;
    protected $charset;

    const CHARSET_ISO = 0;
    const CHARSET_UTF8 = 1;

    protected function configure()
    {
        $this
            ->setName('galactus:absorb')
            ->setDescription(sprintf('Absorb %s rss feed', $this->name));
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $guzzle = new Client();
        $response = $guzzle->get($this->uri);
        $xml = $response->xml();

        return $xml;
    }

    protected function extract($string)
    {
        if (self::CHARSET_UTF8 === $this->charset) {
            return $string;
        }

        return utf8_encode($string);
    }
}