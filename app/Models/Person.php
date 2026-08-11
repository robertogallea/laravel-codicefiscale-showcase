<?php

namespace App\Models;

use Database\Factories\PersonFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Robertogallea\CodiceFiscale\Laravel\Casts\CodiceFiscaleCast;

/**
 * The "Person (record)" per CONTEXT.md - this showcase's own Eloquent
 * model, distinct from the package's Data\Person value object.
 */
#[Fillable(['first_name', 'last_name', 'codice_fiscale'])]
class Person extends Model
{
    /** @use HasFactory<PersonFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'codice_fiscale' => CodiceFiscaleCast::class,
        ];
    }
}
