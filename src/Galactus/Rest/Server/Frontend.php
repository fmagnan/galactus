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
        $postRepository = new QueryBuilder($this->connector, 'posts');
        $posts = $postRepository->last();

        $settingsRepository = new QueryBuilder($this->connector, 'settings', 'name');
        $settings = $settingsRepository->all();

        $channel = new Channel($settings);
        $rss = new Export($channel, $posts);

        die($rss->output());
    }

}