<?php

namespace Galactus\Command;

use Galactus\Persistence\PDO\QueryBuilder;
use PicoFeed\Config;
use PicoFeed\Logging;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use PicoFeed\Reader;

class AbsorbFeed extends Command
{
    protected $feedRepository;
    protected $postRepository;
    protected $settingsRepository;

    public function __construct(\PDO $db)
    {
        parent::__construct();
        $this->feedRepository = new QueryBuilder($db, 'feeds');
        $this->postRepository = new QueryBuilder($db, 'posts');
        $this->settingsRepository = new QueryBuilder($db, 'settings', 'name');
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
            if (false === ($feed = $this->feedRepository->findByPk($feedId))) {
                $output->writeln('<error>feed id n°' . $feedId . ' does not exist.</error>');
                return;
            }
            $this->absorb($output, $feedId, $feed['url']);
        } else {
            $feeds = $this->feedRepository->findActiveFeeds();
            foreach ($feeds as $feed) {
                $this->absorb($output, $feed['id'], $feed['url']);
            }
        }
        $this->settingsRepository->flushBuildDate();
    }

    protected function writeErrors(OutputInterface $output)
    {
        foreach (Logging::getMessages() as $message) {
            $output->writeln('<error>' . $message . '</error>');
        }
    }

    protected function formatDateForMySQL($date)
    {
        return date('Y-m-d H:i:s', $date);
    }

    protected function absorb(OutputInterface $output, $id, $url)
    {
        $config = new Config();
        $config->setClientUserAgent('Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:11.0) Gecko/20100101 Firefox/11.0');

        $reader = new Reader($config);
        $reader->download($url);

        $parser = $reader->getParser();

        if ($parser === false) {
            //$this->feedRepository->disableFeed($id);
            return $this->writeErrors($output);
        }

        $feed = $parser->execute();
        if ($feed === false) {
            //$this->feedRepository->disableFeed($id);
            return $this->writeErrors($output);
        }

        $data = [
            'lang' => $feed->getLanguage(),
            'title' => $feed->getTitle(),
            'feedUri' => $feed->getUrl(),
            'lastUpdate' => $this->formatDateForMySQL($feed->getDate()),
        ];
        $this->feedRepository->updateByPk($data, $id);


        foreach ($feed->items as $item) {
            $url = $item->getUrl();
            $output->writeln('+ ' . $item->title);
            $fullContent = file_get_contents($url);
            $tidy = tidy_parse_string($fullContent, array(), 'UTF8');
            $tidy->cleanRepair();
            $html = $tidy->value;

            $readability = new \Readability($html, $url);
            $result = $readability->init();
            if ($result) {
                $content = $readability->getContent()->innerHTML;
                $tidy = tidy_parse_string($content, array('indent' => true, 'show-body-only' => true), 'UTF8');
                $tidy->cleanRepair();
                $content = $tidy->value;
            } else {
                $output->writeln('unable to get full content');
                $content = $item->getContent();
            }
            $data = [
                'feedId' => $id,
                'remoteId' => $item->getId(),
                'title' => $item->getTitle(),
                'url' => $url,
                'pubDate' => $this->formatDateForMySQL($item->getDate()),
                'content' => $content,
                'author' => $item->getAuthor(),
                /*'category' => $item->getCategory(),
                'comments' => $item->getComments()*/
            ];

            $this->postRepository->add($data, true);
        }
    }

}