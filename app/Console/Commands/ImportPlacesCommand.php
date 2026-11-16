<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Robertogallea\CodiceFiscale\Laravel\BirthPlaces\Import\ForeignCountryXlsxImporter;
use Robertogallea\CodiceFiscale\Laravel\BirthPlaces\Import\IstatForeignCountryJsonImporter;
use Robertogallea\CodiceFiscale\Laravel\BirthPlaces\Import\IstatSuppressedMunicipalityJsonImporter;
use Robertogallea\CodiceFiscale\Laravel\BirthPlaces\Import\MunicipalityCsvImporter;
use Robertogallea\CodiceFiscale\Laravel\BirthPlaces\Import\RetiredForeignCountryImporter;
use Robertogallea\CodiceFiscale\Laravel\BirthPlaces\Import\RetiredMunicipalityImporter;

/**
 * Imports already-downloaded birthplace sources into the birthplace
 * database - the same import `codice-fiscale:update-places` runs, minus
 * the downloads.
 *
 * ANPR's WAF rejects requests from Render's network, so the Docker build
 * can't run `codice-fiscale:update-places` (which downloads from ANPR
 * itself). The weekly GitHub workflow mirrors every source to the
 * `anpr-sources` release instead, and the build downloads them from
 * there and imports them with this command.
 *
 * The import order mirrors the package's UpdatePlacesCommand, because it
 * decides which source wins a code (package ADR-0012, ADR-0014,
 * ADR-0015) - keep the two in step when upgrading the package.
 */
#[Signature('codice-fiscale:import-places
    {municipalities : Path to the ANPR comuni archive CSV}
    {suppressed-municipalities : Path to the ISTAT suppressed-comuni report JSON}
    {countries : Path to the MAECI stati-esteri xlsx table}
    {historical-countries : Path to the ISTAT historical foreign-countries report JSON}')]
#[Description('Import the ANPR, ISTAT and MAECI birthplace sources from local files')]
class ImportPlacesCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(
        MunicipalityCsvImporter $municipalityImporter,
        IstatSuppressedMunicipalityJsonImporter $istatMunicipalityImporter,
        RetiredMunicipalityImporter $retiredMunicipalityImporter,
        ForeignCountryXlsxImporter $countryImporter,
        IstatForeignCountryJsonImporter $istatCountryImporter,
        RetiredForeignCountryImporter $retiredCountryImporter,
    ): int {
        $paths = [
            'municipalities' => (string) $this->argument('municipalities'),
            'suppressed-municipalities' => (string) $this->argument('suppressed-municipalities'),
            'countries' => (string) $this->argument('countries'),
            'historical-countries' => (string) $this->argument('historical-countries'),
        ];

        foreach ($paths as $path) {
            if (! is_file($path) || ! is_readable($path)) {
                $this->error("Cannot read \"{$path}\".");

                return self::FAILURE;
            }
        }

        $csv = (string) file_get_contents($paths['municipalities']);
        $suppressedJson = (string) file_get_contents($paths['suppressed-municipalities']);

        // ANPR wins a code outright, then ISTAT's suppressed comuni, then
        // the retired list: each later writer skips the codes an earlier
        // one carries.
        $count = $municipalityImporter->import($csv);
        $anprEras = $municipalityImporter->eraStarts($csv);
        $istatCount = $istatMunicipalityImporter->import($suppressedJson, $anprEras);
        $retiredCount = $retiredMunicipalityImporter->import(
            $anprEras + $istatMunicipalityImporter->eraStarts($suppressedJson),
        );

        $this->info("Imported {$count} municipality era-records from ANPR.");
        $this->info("Imported {$istatCount} suppressed comuni ANPR lacks from ISTAT.");
        $this->info("Wrote {$retiredCount} retired domestic codes no source carries.");

        $xlsx = (string) file_get_contents($paths['countries']);
        $historicalJson = (string) file_get_contents($paths['historical-countries']);

        // Plain upserts on a shared key, so the last writer wins: every
        // source beats the retired list, MAECI beats ISTAT's current
        // rows, and ISTAT's historical rows beat MAECI.
        $retiredCount = $retiredCountryImporter->import();
        $istatCount = $istatCountryImporter->importCurrent($historicalJson);
        $count = $countryImporter->import($xlsx);
        $istatCount += $istatCountryImporter->importHistorical($historicalJson);

        $this->info("Imported {$count} foreign country records from MAECI.");
        $this->info("Imported {$istatCount} foreign country records from ISTAT.");
        $this->info("Wrote {$retiredCount} retired foreign codes no source carries.");

        return self::SUCCESS;
    }
}
