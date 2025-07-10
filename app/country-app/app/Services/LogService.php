<?php

namespace App\Services;

use App\Models\LogEntry;
use Carbon\Carbon; // For handling timestamps

class LogService
{
    /**
     * Creates a new log entry.
     *
     * @param string $username
     * @param int $numCountriesReturned
     * @param array $countriesDetails // Expecting an array of country data
     * @return LogEntry
     */
    public function createLog(string $username, int $numCountriesReturned, array $countriesDetails): LogEntry
    {
        return LogEntry::create([
            "username" => $username,
            "request_timestamp" => Carbon::now(),
            "num_countries_returned" => $numCountriesReturned,
            "countries_details" => json_encode($countriesDetails), // Store details as a JSON string
        ]);
    }
}
