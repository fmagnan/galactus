<?php

namespace Galactus\Command;

use Galactus\Persistence\PDO\Feed;
use Galactus\Persistence\PDO\Post;
use GuzzleHttp\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AbsorbFeed extends Command
{
    protected $db;

    public function __construct($db)
    {
        parent::__construct();
        $this->db = $db;
    }

    protected function configure()
    {
        $this
            ->setName('galactus:absorb')
            ->setDescription('Absorb rss feed')
            ->addArgument('feed', InputArgument::OPTIONAL, 'Enter id of feed you want to absorb');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $feedId = $input->getArgument('feed');
        $feedRepository = new Feed($this->db);
        if ($feedId) {
            $feed = $feedRepository->findByPk($feedId);
            $this->absorbFeed($output, $feed);
        } else {
            $feeds = $feedRepository->findBy('isEnabled', 1);
            foreach ($feeds as $feed) {
                $this->absorbFeed($output, $feed);
            }
        }
    }

    protected function absorbFeed(OutputInterface $output, array $feed)
    {
        $guzzle = new Client();
        $response = $guzzle->get($feed['url']);
        $xml = $response->xml();

        $output->writeln('fetching ' . $feed['url']);

        $postRepository = new Post($this->db);

        foreach ($xml->channel->item as $post) {
            $output->writeln('adding new post: ' . $post->title);
            $queryParts = parse_url($post->link);
            $params = explode('=', $queryParts['query']);
            $remoteId = $params[1];

            $postRepository->add(
                [
                    'blogId' => $feed['id'],
                    'remoteId' => $remoteId,
                    'title' => $post->title,
                    'creationDate' => $post->pubDate,
                    'description' => $post->description,
                    'content' => $post->children('content', true)->encoded,
                    'url' => $post->link
                ],
                true
            );
        }
    }

}