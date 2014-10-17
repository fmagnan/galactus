<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../inc/settings.inc.php';

use Galactus\Persistence\PDO\QueryBuilder;

function rss_formatDateToRFC2822($date)
{
    return date('r', strtotime($date));
}

function rss_shorten($text, $length)
{
    $pos = strpos($text, ' ', $length);
    return substr($text, 0, $pos);
}

$postRepository = new QueryBuilder($connector, 'posts');
$posts = $postRepository->last();

$settingsRepository = new QueryBuilder($connector, 'settings', 'name');
$settings = $settingsRepository->all();

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
    <title>' . $settings['title'] . '</title>
    <atom:link href="' . $settings['atom_link'] . '" rel="self" type="application/rss+xml" />
    <link>' . $settings['link'] . '</link>
    <description>' . $settings['description'] . '</description>
    <lastBuildDate>' . rss_formatDateToRFC2822($settings['lastBuildDate']) . '</lastBuildDate>
    <language>' . $settings['language'] . '</language>
    <generator>' . $settings['generator'] . '</generator>';

$postMask = <<<HEREDOC_NAME

        <item>
            <title>%s</title>
            <link>%s</link>
            <pubDate>%s</pubDate>
            <dc:creator><![CDATA[%s]]></dc:creator>
            <guid isPermaLink="false">%s</guid>
            <description><![CDATA[%s]]></description>
        </item>
HEREDOC_NAME;

foreach ($posts as $post) {
    $postContent = sprintf(
        $postMask,
        $post['title'],
        $post['url'],
        rss_formatDateToRFC2822($post['pubDate']),
        $post['author'],
        $post['url'],
        rss_shorten($post['content'], 250) . ' ...'
    );
    echo $postContent;
}
echo PHP_EOL . '</channel>
</rss>';