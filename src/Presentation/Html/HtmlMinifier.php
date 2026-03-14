<?php

declare(strict_types=1);

namespace App\Presentation\Html;

final class HtmlMinifier
{
    public function minify(string $html): string
    {
        // sloučí zalomené atributy uvnitř tagů
        do {
            $newHtml = preg_replace(
                '/<([a-z0-9]+)([^>]*?)\s*[\r\n]+([^>]*)>/isu',
                '<$1$2 $3>',
                $html
            );

            $changed = $newHtml !== $html;
            $html = $newHtml;

        } while ($changed);

        $replace = [
            '/\>[^\S ]+/s' => '>',
            '/[^\S ]+\</s' => '<',
            '/([\t ])+/s' => ' ',
            '/^([\t ])+/m' => '',
            '/([\t ])+$/m' => '',
            '~//[a-zA-Z0-9 ]+$~m' => '',
            '/[\r\n]+([\t ]?[\r\n]+)+/s' => "\n",
            '/\>[\r\n\t ]+\</s' => '><',
            '/}[\r\n\t ]+/s' => '}',
            '/}[\r\n\t ]+,[\r\n\t ]+/s' => '},',
            '/\)[\r\n\t ]?{[\r\n\t ]+/s' => '){',
            '/,[\r\n\t ]?{[\r\n\t ]+/s' => ',{',
            '/\),[\r\n\t ]+/s' => '),',
        ];

        $html = preg_replace(array_keys($replace), array_values($replace), $html);

        $remove = [
            '</option>',
            '</li>',
            '</dt>',
            '</dd>',
            '</tr>',
            '</th>',
            '</td>',
        ];

        return str_ireplace($remove, '', $html);
    }
}
