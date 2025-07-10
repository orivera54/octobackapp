<?php

namespace App\GraphQL\Queries;

use App\Services\CountryService;
use App\Services\LogService; // To log the request
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class CountryQuery
{
    protected CountryService $countryService;
    protected LogService $logService;

    public function __construct(CountryService $countryService, LogService $logService)
    {
        $this->countryService = $countryService;
        $this->logService = $logService;
    }

    /**
     * Return a value for the field.
     *
     * @param  null  $_
     * @param  array<string, mixed>  $args
     * @param  GraphQLContext|null  $context
     * @param  ResolveInfo  $resolveInfo
     * @return mixed
     */
    public function topByDensity($_, array $args, GraphQLContext $context = null, ResolveInfo $resolveInfo)
    {
        $count = $args["count"];
        $countries = $this->countryService->getCountriesByDensity($count);

        // Log the request
        // Assuming a default username for now, or it could come from context if auth is implemented
        $username = $context && $context->user() ? $context->user()->name : "anonymous";

        // Prepare details for logging. We need to be careful not to log excessively large data.
        // For now, lets log names and densities, or just a summary.
        $loggedCountriesDetails = $countries->map(fn($country) => [
            "name" => $country["nameCommon"],
            "density" => $country["populationDensity"]
        ])->toArray();

        $this->logService->createLog(
            $username,
            $countries->count(),
            $loggedCountriesDetails
        );

        return $countries;
    }
}
