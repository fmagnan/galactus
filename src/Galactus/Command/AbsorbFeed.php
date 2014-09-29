<?php

namespace Galactus\Command;

use Galactus\Domain\Feed;
use Galactus\Parse\Atom;
use Galactus\Parse\Rss;
use Galactus\Parse\Rss2;
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
        $options = [
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:11.0) Gecko/20100101 Firefox/11.0'
            ]
        ];
        $guzzle = new Client();
        $url = $feed['url'];
        $response = $guzzle->get($url, $options);
        $output->writeln('fetching ' . $url);
        $type = (int)$feed['type'];
        $xml = $response->xml();

        if (Feed::TYPE_ATOM === $type) {
            $parser = new Atom($xml);
        } elseif (Feed::TYPE_RSS === $type) {
            $parser = new Rss($xml);
        } else {
            $parser = new Rss2($xml);
        }

        foreach ($parser->extractPost($feed['id']) as $data) {
            $output->writeln('+ ' . $data['title']);
            $this->postRepository->add($data, true);
        }
    }

}