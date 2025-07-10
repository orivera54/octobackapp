<?php

namespace App\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class PlaceholderMutation
{
    /**
     * Return a value for the field.
     *
     * @param  null  $_
     * @param  array<string, mixed>  $args
     * @param  GraphQLContext|null  $context
     * @param  ResolveInfo  $resolveInfo
     * @return mixed
     */
    public function echo($_, array $args, GraphQLContext $context = null, ResolveInfo $resolveInfo)
    {
        return $args["text"] ?? "No text provided.";
    }
}
