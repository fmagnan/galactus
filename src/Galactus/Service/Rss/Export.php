<?php

namespace Galactus\Service\Rss;

class Export
{

    protected $channel;
    protected $items;

    public function __construct(Channel $channel, array $items)
    {
        $this->channel = $channel;
        $this->items = $items;
    }

    function formatDateToRFC2822($date)
    {
        return date('r', strtotime($date));
    }

    function shorten($text, $length)
    {
        $pos = strpos($text, ' ', $length);
        return substr($text, 0, $pos);
    }

    public function output()
    {
        header('Content-Type: application/xml; charset=utf-8');

        $output = '<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0"
     xmlns:content="http://purl.org/rss/1.0/modules/content/"
     xmlns:wfw="http://wellformedweb.org/CommentAPI/"
     xmlns:dc="http://purl.org/dc/elements/1.1/"
     xmlns:atom="http://www.w3.org/2005/Atom"
     xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
     xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
>

<channel>
    <title>' . $this->channel->title . '</title>
    <atom:link href="' . $this->channel->atom_link . '" rel="self" type="application/rss+xml" />
    <link>' . $this->channel->link . '</link>
    <description>' . $this->channel->description . '</description>
    <lastBuildDate>' . $this->formatDateToRFC2822($this->channel->lastBuildDate) . '</lastBuildDate>
    <language>' . $this->channel->language . '</language>
    <generator>' . $this->channel->generator . '</generator>';

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

        foreach ($this->items as $post) {
            $postContent = sprintf(
                $postMask,
                $post['title'],
                $post['url'],
                $this->formatDateToRFC2822($post['pubDate']),
                $post['author'],
                $post['url'],
                $this->shorten($post['content'], 250) . ' ...'
            );
            $output .= $postContent;
        }
        $output .= PHP_EOL . '</channel>
</rss>';

        return $output;
    }

}