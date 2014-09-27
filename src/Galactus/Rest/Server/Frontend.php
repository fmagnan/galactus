<?php

namespace Galactus\Rest\Server;

use Galactus\Persistence\PDO\Feed;
use Galactus\Persistence\PDO\Post;

class Frontend
{

    const MAX_LIMIT_FOR_DATABASE_QUERIES = 50;

    protected $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function feeds()
    {
        $feedRepository = new Feed($this->db);
        $feeds = $feedRepository->findActives();

        return $feeds;
    }

    public function posts()
    {
        $postRepository = new Post($this->db);

        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : self::MAX_LIMIT_FOR_DATABASE_QUERIES;
        $limit = min($limit, self::MAX_LIMIT_FOR_DATABASE_QUERIES);
        $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

        $posts = $postRepository->findActives($limit, $offset);

        return $posts;
    }

}