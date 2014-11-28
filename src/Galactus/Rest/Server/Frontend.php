<?php

namespace Galactus\Rest\Server;

use Galactus\Persistence\PDO\QueryBuilder;
use Galactus\Service\Rss\Channel;
use Galactus\Service\Rss\Export;

class Frontend
{

    const MAX_LIMIT_FOR_DATABASE_QUERIES = 50;

    protected $connector;

    public function __construct(\PDO $db)
    {
        $this->connector = $db;
    }

    public function feeds()
    {
        $feedRepository = new QueryBuilder($this->connector, 'feeds');
        $code = isset($_GET['code']) ? $_GET['code'] : '';
        $feeds = $feedRepository->findActiveFeeds($code);

        return $feeds;
    }

    public function posts()
    {
        $postRepository = new QueryBuilder($this->connector, 'posts');

        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : self::MAX_LIMIT_FOR_DATABASE_QUERIES;
        $limit = min($limit, self::MAX_LIMIT_FOR_DATABASE_QUERIES);
        $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

        $conditions = [];
        if (isset($_GET['feedId'])) {
            $conditions['feedId'] = (int)$_GET['feedId'];
        }
        if (isset($_GET['code'])) {
            $conditions['code'] = $_GET['code'];
        }

        $posts = $postRepository->findActivePosts($conditions, $limit, $offset);

        return $posts;
    }

    public function rss()
    {
        if (!isset($_GET['code'])) {
            header('location: /');
            exit;
        }

        $code = $_GET['code'];
        $conditions = ['code' => $code];
        $prefix = $code . '.';

        $postRepository = new QueryBuilder($this->connector, 'posts');
        $posts = $postRepository->findActivePosts($conditions, 20);

        $settingsRepository = new QueryBuilder($this->connector, 'settings', 'name');
        $settings = $settingsRepository->allWhichBeginWith($prefix);

        $channel = new Channel($settings, $prefix);

        $rss = new Export($channel, $posts);

        die($rss->output());
    }

    public function addFeed($planetCode)
    {
        if (!isset($_POST['url'])) {
            header('location: /');
            exit;
        }
        $feedUrl = $_POST['url'];
        $feedRepository = new QueryBuilder($this->connector, 'feeds');
        $result = $feedRepository->proposeNewFeed(['code' => $planetCode, 'url' => $feedUrl], true);

        return $result;
    }

}