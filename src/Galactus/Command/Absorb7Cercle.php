<?php

namespace Galactus\Command;

use Galactus\Persistence\PDO\Connector;
use Galactus\Persistence\PDO\Post;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Absorb7Cercle extends AbsorbFeed
{
    public function __construct()
    {
        $this->name = '7emecercle.com';
        $this->uri = 'http://www.7emecercle.com/website/?feed=rss2';
        $this->charset = self::CHARSET_UTF8;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $xml = parent::execute($input, $output);

        $output->writeln('fetching ' . $this->uri);

        $db = new Connector('localhost', 'galactus', 'password', 'galactus');
        $output->writeln('opening database connection');
        $postRepository = new Post($db);

        foreach ($xml->channel->item as $post) {
            $output->writeln('adding new post: ' . $post->title);
            $queryParts = parse_url($post->link);
            $params = explode('=', $queryParts['query']);
            $remoteId = $params[1];

            $postRepository->add([
                'blogId' => 1,
                'remoteId' => $remoteId,
                'title' => $this->extract($post->title),
                'creationDate' => $post->pubDate,
                'content' => $this->extract($post->children('content',true)->encoded),
                'url' => $post->link
            ], true);
        }


    }
}