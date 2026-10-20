<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Robertogallea\CodiceFiscale\Laravel\BirthPlaces\Import\ForeignCountryXlsxImporter;
use Robertogallea\CodiceFiscale\Laravel\BirthPlaces\Import\MunicipalityCsvImporter;

/**
 * Imports already-downloaded ANPR sources into the birthplace database.
 *
 * ANPR's WAF rejects requests from Render's network, so the Docker build
 * can't run `codice-fiscale:update-places` (which downloads from ANPR
 * itself). The weekly GitHub workflow mirrors the sources to the
 * `anpr-sources` release instead, and the build downloads them from
 * there and imports them with this command.
 */
#[Signature('codice-fiscale:import-places
    {municipalities : Path to the ANPR comuni archive CSV}
    {countries : Path to the stati-esteri xlsx table}')]
#[Description('Import the ANPR comuni archive and stati-esteri table from local files')]
class ImportPlacesCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(MunicipalityCsvImporter $municipalityImporter, ForeignCountryXlsxImporter $countryImporter): int
    {
        $municipalitiesPath = (string) $this->argument('municipalities');
        $countriesPath = (string) $this->argument('countries');

        foreach ([$municipalitiesPath, $countriesPath] as $path) {
            if (! is_file($path) || ! is_readable($path)) {
                $this->error("Cannot read \"{$path}\".");

                return self::FAILURE;
            }
        }

        $count = $municipalityImporter->import((string) file_get_contents($municipalitiesPath));
        $this->info("Imported {$count} municipality era-records.");

        $count = $countryImporter->import((string) file_get_contents($countriesPath));
        $this->info("Imported {$count} foreign country records.");

        return self::SUCCESS;
    }
}
