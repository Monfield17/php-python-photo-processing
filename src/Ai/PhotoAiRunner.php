<?php

namespace App\Ai;

class PhotoAiRunner
{
    private const PYTHON_SCRIPT = __DIR__ . '/../../scripts/analyze.py';

    public static function analyze(string $imagePath): array
    {
        $cmd = 'python3 ' . escapeshellarg(self::PYTHON_SCRIPT) . ' ' . escapeshellarg($imagePath);

        $output = [];
        $exit = 0;

        exec($cmd, $output, $exit);

        if ($exit !== 0) {
            throw new \RuntimeException("Python script failed (exit $exit)");
        }

        $json = implode("\n", $output);
        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException("Invalid JSON output");
        }

        return $data;
    }
}
