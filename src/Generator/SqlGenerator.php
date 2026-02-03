<?php

namespace CrawlTool\Generator;

class SqlGenerator
{
    public function generateInsert(string $table, array $data): string
    {
        $columns = array_keys($data);
        $values = array_map(function ($value) {
            if (is_null($value)) return 'NULL';
            if (is_numeric($value)) return $value;
            // Basic escaping
            return "'" . addslashes((string)$value) . "'";
        }, array_values($data));

        $colString = implode('`, `', $columns);
        $valString = implode(', ', $values);

        return "INSERT INTO `$table` (`$colString`) VALUES ($valString);";
    }

    public function saveToFile(string $filePath, string $sql): void
    {
        $dir = dirname($filePath);
        if (!is_dir($dir)) mkdir($dir, 0777, true);
        file_put_contents($filePath, $sql . PHP_EOL, FILE_APPEND);
    }
}
