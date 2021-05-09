<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class DurationHelperExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
            new TwigFilter('duration_helper', [$this, 'duration_helper']),
        ];
    }


    public function duration_helper(int $value): string
    {
        if($value<60)
            return "$value minutes";
        else{
            $h=intdiv($value,60);
            $value=$value%60;
            return "$h hours and $value minutes";
        }
    }
}
