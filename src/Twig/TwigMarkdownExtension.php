<?php

namespace App\Twig;

use App\Util\MarkdownUtil;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TwigMarkdownExtension extends AbstractExtension
{
    public function __construct(private MarkdownUtil $util)
    {
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('excerpt', [$this->util, 'excerpt']),
            new TwigFilter('markdown', [$this->util, 'markdown'], ['is_safe' => ['html']]),
            new TwigFilter('markdown_excerpt', [$this->util, 'markdownExcerpt'], ['is_safe' => ['html']]),
            new TwigFilter('markdown_untrusted', [$this->util, 'markdownUntrusted'], ['is_safe' => ['html']]),
        ];
    }
}
