<?php

namespace App\Twig;

use App\Util\EmailUtil;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TwigEmailExtension extends AbstractExtension
{
    public function __construct(private EmailUtil $util)
    {
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('markdown_email', [$this->util, 'markdownEmail'], ['needs_context' => true, 'is_safe' => ['html']]),
            new TwigFilter('text_email', [$this->util, 'formatText'])
        ];
    }
}
