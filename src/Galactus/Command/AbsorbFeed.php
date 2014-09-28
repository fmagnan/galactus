<?php

namespace Galactus\Command;

use Galactus\Persistence\PDO\QueryBuilder;
use GuzzleHttp\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AbsorbFeed extends Command
{
    protected $feedRepository;
    protected $postRepository;

    public function __construct($db)
    {
        parent::__construct();
        $this->feedRepository = new QueryBuilder($db, 'feeds', 'id');
        $this->postRepository = new QueryBuilder($db, 'posts', 'id');
    }

    protected function configure()
    {
        $this
            ->setName('galactus:absorb')
            ->setDescription('Absorb rss feed')
            ->addArgument('feed', InputArgument::OPTIONAL, 'Enter id of feed you want to absorb')
            ->addOption('truncate', null, InputOption::VALUE_NONE, 'If set, posts table will be truncated');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('truncate')) {
            $this->postRepository->truncate();
        }
        $feedId = $input->getArgument('feed');
        if ($feedId) {
            $feed = $this->feedRepository->findByPk($feedId);
            $this->absorbFeed($output, $feed);
        } else {
            $feeds = $this->feedRepository->findActiveFeeds();
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

        foreach ($xml->channel->item as $post) {
            $output->writeln('adding new post: ' . $post->title);

            $date = new \DateTime($post->pubDate, new \DateTimeZone('UTC'));
            $creationDate = $date->format('Y-m-d h:i:s');

            $remoteIdContainerField = property_exists($post, 'guid') ? 'guid' : 'link';
            $remoteId = $this->extractRemoteIdFrom($post->{$remoteIdContainerField});

            $description = $this->filterText($post->description);
            $content = $this->filterText($post->children('content', true)->encoded);

            $this->postRepository->add(
                [
                    'feedId' => $feed['id'],
                    'remoteId' => $remoteId,
                    'title' => $post->title,
                    'creationDate' => $creationDate,
                    'description' => $description,
                    'content' => $content,
                    'url' => $post->link
                ],
                true
            );
        }
    }

    protected function filterText($string)
    {
        $string = html_entity_decode($string);

        return $string;
    }

    protected function extractRemoteIdFrom($link)
    {
        $queryParts = parse_url($link);
        if (!isset($queryParts['query'])) {
            return 0;
        }
        $params = explode('=', $queryParts['query']);
        $remoteId = $params[1];

        return $remoteId;
    }

}