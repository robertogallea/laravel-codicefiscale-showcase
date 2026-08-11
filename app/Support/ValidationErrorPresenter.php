<?php

namespace App\Support;

use Robertogallea\CodiceFiscale\Enums\ValidationError;

/**
 * Maps the domain Validator's ValidationError cases to friendly,
 * translated messages. Shared by every page that runs the domain
 * Validator directly - currently the hero tool's Decode mode and the
 * Repository-swap Explore page - since Validator::validate() never
 * accepts a Person and reports per-reason failures rather than one
 * generic message, so this is the one place that turns those reasons
 * into copy, instead of each page inventing its own mapping.
 */
final class ValidationErrorPresenter
{
    /** @param  list<ValidationError>  $errors */
    public static function describe(array $errors): string
    {
        $messages = array_map(
            fn (ValidationError $error) => __('messages.decode.errors.'.$error->value),
            $errors,
        );

        return implode(' ', $messages);
    }
}
