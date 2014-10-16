<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../inc/settings.inc.php';

$postRepository = new \Galactus\Persistence\PDO\QueryBuilder($connector, 'posts', 'id');
$posts = $postRepository->last();

$foo = '';

header('Content-Type: application/xml; charset=utf-8');

echo '<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0"
     xmlns:content="http://purl.org/rss/1.0/modules/content/"
     xmlns:wfw="http://wellformedweb.org/CommentAPI/"
     xmlns:dc="http://purl.org/dc/elements/1.1/"
     xmlns:atom="http://www.w3.org/2005/Atom"
     xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
     xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
>

<channel>
    <title>' . $foo . '</title>
    <atom:link href="' . $foo . '" rel="self" type="application/rss+xml" />
    <link>' . $foo . '</link>
    <description>' . $foo . '</description>
    <lastBuildDate>' . $foo . '</lastBuildDate>
    <language>' . $foo . '</language>
    <generator>' . $foo . '</generator>';

foreach ($posts as $post) {
    echo '<item>
    <title>' . $post['title'] . '</title>
    <link>' .$post['title'].'</link>
    <comments>' .$post['title'].'</comments>
    <pubDate>' .$post['title']/*<?php echo mysql2date('D, d M Y H:i:s +0000', get_post_time('Y-m-d H:i:s', true), false); ?>*/.'</pubDate>
    <dc:creator><![CDATA[' . $post['title'] . ']]></dc:creator>

    <guid isPermaLink="false">' . $post['title'] . '</guid>
        <description><![CDATA[' . $post['title'] . ']]></description>
            <content:encoded><![CDATA[' . $post['title'] . ']]></content:encoded>

    <wfw:commentRss>' . $post['title'] . '</wfw:commentRss>
    <slash:comments>' . $post['title'] . '</slash:comments>
    ' ./*<?php rss_enclosure(); ?>*/
        '


</item>';
}
echo '</channel>
</rss>';