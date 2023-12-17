<?php

namespace quasiuna\paintai;

class Analytics
{
    public static function logApiUsage($model, $promptCharCount, $responseCharCount, $tokenUsage)
    {
        $sql = "INSERT INTO api_log (model, prompt_length, response_length, token_usage) VALUES (?, ?, ?, ?)";
        return DB::query($sql, [$model, $promptCharCount, $responseCharCount, $tokenUsage]);
    }

    // Display or process the statistics as needed
    public static function getStats($interval)
    {
        $filter = "FROM api_log WHERE timestamp >= datetime('now', ?)";
        $sql = "SELECT SUM(prompt_length) AS total_prompt_length, SUM(response_length) AS total_response_length, SUM(token_usage) AS total_tokens $filter";
        $q = DB::query($sql, [$interval]);
        $stats = $q->fetch(\PDO::FETCH_ASSOC);

        $sql = "SELECT COUNT(*) AS c $filter";
        $count = DB::query($sql, [$interval])->fetch(\PDO::FETCH_COLUMN);

        return [
            'count' => $count,
            'stats' => $stats,
        ];
    }

    public static function allStats()
    {
        $dayStats = static::getStats('-1 day');
        $weekStats = static::getStats('-7 days');
        $monthStats = static::getStats('-1 month');

        dd('1 day', "\n", $dayStats, '7 days', "\n", $weekStats, '1 month', "\n", $monthStats);
    }
}
