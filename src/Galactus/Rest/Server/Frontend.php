<?php

namespace Galactus\Rest\Server;

use Galactus\Persistence\PDO\Feed;
use Galactus\Persistence\PDO\Post;

class Frontend
{

    protected $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function feeds()
    {
        $feedRepository = new Feed($this->db);
        $feeds = $feedRepository->findAllActiveFeeds();

        return $feeds;
    }

    public function posts()
    {
        $postRepository = new Post($this->db);
        $posts = $postRepository->all();

        return $posts;
    }

}