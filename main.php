<?php

/**
 * Abstract class for parsers.
 */
abstract class Parser
{
    /**
     * @var string|false
     */
    protected string|bool $content;

    /**
     * Parser constructor
     * @param string $url
     */
    public function __construct(string $url)
    {
        $this->content = file_get_contents($url);
    }

    /**
     * Parse content
     * @return array
     */
    abstract public function parse(): array;
}

/**
 * Class representing an HTML tag.
 */
class HTMLTag
{
    /**
     * @var string
     */
    private string $name;
    /**
     * @var int
     */
    private int $count = 0;

    /**
     * HTMLTag constructor
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * Return tag name
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Return count of tags
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * Increment count of tags
     * @return void
     */
    public function addTag(): void
    {
        $this->count++;
    }
}

/**
 * Class for counting HTML tags in a given content.
 */
class HTMLTagsCounter extends Parser
{
    /**
     * @var array
     */
    private array $tags = [];

    /**
     * HTMLTagsCounter constructor
     * @param string $url
     */
    public function __construct(string $url)
    {
        parent::__construct($url);
    }

    /**
     * @inheritDoc
     * @return array
     */
    public function parse(): array
    {
        if (!is_string($this->content)) {
            return [];
        }
        preg_match_all('~<(/?)([a-z]+)(?:\s*|[^>]*\s*)>~i', $this->content, $matches);

        foreach ($matches[2] as $index => $tagName) {
            $tagName = strtolower($tagName);
            $isClosingTag = $matches[1][$index] === '/';

            if (!isset($this->tags[$tagName])) {
                $this->tags[$tagName] = new HTMLTag($tagName);
            }

            if (!$isClosingTag) {
                $this->tags[$tagName]->addTag();
            }
        }

        return $this->tags;
    }
}

/**
 * Class for formatting the output of tags count.
 */
class OutputFormatter
{
    /**
     * Return formatted information about tags
     * @param array $tags
     * @return string
     */
    public static function formatTagsCount(array $tags): string
    {
        $output = "Tags and their count on page:\n";

        /** @var HTMLTag $tag */
        foreach ($tags as $tag) {
            $output .= $tag->getName() . ": " . $tag->getCount() . "\n";
        }

        return $output;
    }
}

$url = 'https://example.com'; // Input url;
$parser = new HTMLTagsCounter($url);
$tags = $parser->parse();
echo OutputFormatter::formatTagsCount($tags);

// Output result
/*
Tags and their count on page:
html: 1
head: 1
title: 1
meta: 1
body: 1
div: 1
h: 1
br: 2
a: 1
*/

?>
