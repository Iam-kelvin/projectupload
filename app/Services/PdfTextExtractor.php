<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Smalot\PdfParser\Parser;

class PdfTextExtractor
{
    public function extract(UploadedFile|string $file): string
    {
        $path = $file instanceof UploadedFile ? $file->getRealPath() : $file;

        if (! $path || ! is_file($path)) {
            return '';
        }

        $text = $this->extractWithPdftotext($path);

        if ($text === '') {
            $text = $this->extractWithPdfParser($path);
        }

        if ($text === '') {
            $text = $this->extractFallback($path);
        }

        return $this->clean($text);
    }

    private function extractWithPdftotext(string $path): string
    {
        $binary = trim((string) config('services.pdftotext_path', 'pdftotext'));
        $command = $this->isWindows()
            ? 'where '.escapeshellarg($binary).' >NUL 2>NUL'
            : 'command -v '.escapeshellarg($binary).' >/dev/null 2>&1';

        exec($command, $output, $exitCode);

        if ($exitCode !== 0) {
            return '';
        }

        $extractCommand = escapeshellarg($binary).' -layout '.escapeshellarg($path).' -';
        $text = shell_exec($extractCommand);

        return is_string($text) ? $text : '';
    }

    private function extractWithPdfParser(string $path): string
    {
        if (! class_exists(Parser::class)) {
            return '';
        }

        try {
            return (new Parser())->parseFile($path)->getText();
        } catch (\Throwable) {
            return '';
        }
    }

    private function extractFallback(string $path): string
    {
        $contents = file_get_contents($path);

        if ($contents === false) {
            return '';
        }

        preg_match_all('/\((?:\\\\.|[^\\\\()])*\)\s*Tj/s', $contents, $simpleText);
        preg_match_all('/\[(.*?)\]\s*TJ/s', $contents, $arrayText);

        $chunks = [];

        foreach ($simpleText[0] as $match) {
            if (preg_match('/\(((?:\\\\.|[^\\\\()])*)\)\s*Tj/s', $match, $textMatch)) {
                $chunks[] = $this->decodePdfString($textMatch[1]);
            }
        }

        foreach ($arrayText[1] as $array) {
            preg_match_all('/\(((?:\\\\.|[^\\\\()])*)\)/s', $array, $strings);

            foreach ($strings[1] as $string) {
                $chunks[] = $this->decodePdfString($string);
            }
        }

        return implode(' ', $chunks);
    }

    private function decodePdfString(string $value): string
    {
        return strtr($value, [
            '\\(' => '(',
            '\\)' => ')',
            '\\\\' => '\\',
            '\\n' => "\n",
            '\\r' => "\r",
            '\\t' => "\t",
        ]);
    }

    private function clean(string $text): string
    {
        $text = $this->forceUtf8($text);
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        $text = preg_replace('/(?<=[a-z0-9])(?=[A-Z])/u', ' ', $text) ?? $text;
        $text = preg_replace('/[^\P{C}\t\r\n]+/u', ' ', $text) ?? $text;
        $text = preg_replace('/\x{FFFD}+/u', ' ', $text) ?? $text;
        $text = preg_replace('/[ \t]+$/m', '', $text) ?? $text;
        $text = preg_replace("/\n{4,}/", "\n\n\n", $text) ?? $text;

        return trim($text);
    }

    private function forceUtf8(string $text): string
    {
        if ($text === '') {
            return '';
        }

        // Some PDF extractors return UTF-8 encoded surrogate glyphs. They are not valid
        // Unicode scalar values, and MySQL rejects them even on utf8mb4 columns.
        $text = preg_replace('/\xED[\xA0-\xBF][\x80-\xBF]/', ' ', $text) ?? $text;

        if (mb_check_encoding($text, 'UTF-8')) {
            return $text;
        }

        $encoding = mb_detect_encoding($text, ['UTF-8', 'Windows-1252', 'ISO-8859-1'], true);

        if ($encoding && $encoding !== 'UTF-8') {
            return mb_convert_encoding($text, 'UTF-8', $encoding);
        }

        return mb_scrub($text, 'UTF-8');
    }

    private function isWindows(): bool
    {
        return str_starts_with(PHP_OS_FAMILY, 'Windows');
    }
}
