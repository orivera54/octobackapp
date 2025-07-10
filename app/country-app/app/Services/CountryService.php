<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Collection;

class CountryService
{
    protected string $apiUrl = "https://restcountries.com/v3.1/all?fields=name,area,population";

    /**
     * Fetches countries from the API, calculates population density,
     * and sorts them by density in descending order.
     *
     * @param int $count The number of countries to return.
     * @return Collection A collection of countries with calculated density.
     */
    public function getCountriesByDensity(int $count): Collection
    {
        $response = Http::get($this->apiUrl);

        if ($response->failed()) {
            // In a real app, log error: Log::error("Failed to fetch countries from API", ["status" => $response->status(), "body" => $response->body()]);
            return collect([]);
        }

        $countries = $response->json();
        if (empty($countries)) {
            return collect([]);
        }

        return collect($countries)
            ->map(function ($country) {
                $area = $country["area"] ?? 0;
                $population = $country["population"] ?? 0;
                $populationDensity = ($area > 0 && $population > 0) ? $population / $area : 0;

                return [
                    "nameCommon" => $country["name"]["common"] ?? "N/A",
                    "nameOfficial" => $country["name"]["official"] ?? "N/A",
                    "area" => (float) $area,
                    "population" => (int) $population,
                    "populationDensity" => (float) $populationDensity,
                ];
            })
            ->filter(fn($country) => $country["populationDensity"] > 0) // Ensure only countries with valid, positive density are included
            ->sortByDesc("populationDensity")
            ->take($count)
            ->values();
    }
}
