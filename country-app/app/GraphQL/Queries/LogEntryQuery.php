<?php

namespace App\GraphQL\Queries;

use App\Models\LogEntry;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class LogEntryQuery
{
    /**
     * Return all log entries.
     *
     * @param  null  $_
     * @param  array<string, mixed>  $args
     * @param  GraphQLContext|null  $context
     * @param  ResolveInfo  $resolveInfo
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all($_, array $args, GraphQLContext $context = null, ResolveInfo $resolveInfo)
    {
        return LogEntry::all();
    }
}
