<?php

declare(strict_types=1);

namespace App\Presentation\Html;

final class HtmlMinifier
{
    public function minify(string $html): string
    {
        do {

            $newHtml = $this->preg(
                '/<([a-z0-9]+)([^>]*?)\s*[\r\n]+([^>]*)>/iu',
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

        $html = $this->preg(
            array_keys($replace),
            array_values($replace),
            $html
        );

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

    /**
     * Safe wrapper for preg_replace that never returns null.
     *
     * @param string|array<int,string> $pattern
     * @param string|array<int,string> $replace
     */
    private function preg(string|array $pattern, string|array $replace, string $subject): string
    {
        $result = preg_replace($pattern, $replace, $subject);

        if ($result === null) {
            return $subject;
        }

        return $result;
    }
}
