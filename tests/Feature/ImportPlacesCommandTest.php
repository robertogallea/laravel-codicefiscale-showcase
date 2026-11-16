<?php

use Illuminate\Support\Facades\DB;
use Robertogallea\CodiceFiscale\Data\RetiredBirthPlaces;
use Robertogallea\CodiceFiscale\Laravel\BirthPlaces\Models\ForeignCountry;
use Robertogallea\CodiceFiscale\Laravel\BirthPlaces\Models\Municipality;
use Robertogallea\CodiceFiscale\Laravel\CodiceFiscaleServiceProvider;

beforeEach(function () {
    // The birthplace database lives in its own SQLite file, outside
    // RefreshDatabase's reach. Re-boot the package provider against an
    // in-memory database so the import never touches storage/.
    config()->set('codicefiscale.database.path', ':memory:');
    DB::purge('codicefiscale');
    (new CodiceFiscaleServiceProvider($this->app))->boot();

    $this->municipalitiesPath = tempnam(sys_get_temp_dir(), 'comuni');
    file_put_contents($this->municipalitiesPath, implode("\n", [
        '"ID","DATAISTITUZIONE","DATACESSAZIONE","CODISTAT","CODCATASTALE","DENOMINAZIONE_IT","DENOMTRASLITTERATA","ALTRADENOMINAZIONE","ALTRADENOMTRASLITTERATA","ID_PROVINCIA","IDPROVINCIAISTAT","IDREGIONE","IDPREFETTURA","STATO","SIGLAPROVINCIA","FONTE","DATAULTIMOAGG","COD_DENOM"',
        '12560,"1866-11-19","1924-11-13","028001","A001","ABANO","ABANO","","",28,"028","05","","C","PD","","2016-06-17","028500"',
        '1,"1924-11-14","9999-12-31","028001","A001","ABANO TERME","ABANO TERME","","",28,"028","05","PD","A","PD","","2016-06-17",""',
    ])."\n");

    // ISTAT report 128: C056 is a comune ANPR lacks; A001 is one ANPR
    // carries, so ANPR's eras must win over ISTAT's.
    $this->suppressedMunicipalitiesPath = tempnam(sys_get_temp_dir(), 'soppressi');
    file_put_contents($this->suppressedMunicipalitiesPath, json_encode(['resultset' => [
        ['SIGLA_UTS' => 'VI', 'PRO_COM_T' => '024027', 'COMUNE' => 'Castegnero', 'COD_CATASTO' => 'C056', 'DATA_INIZIO_AMMINISTRATIVA' => '2026-02-21T00:00:00Z'],
        ['SIGLA_UTS' => 'PD', 'PRO_COM_T' => '028001', 'COMUNE' => 'Abano Bagni', 'COD_CATASTO' => 'A001', 'DATA_INIZIO_AMMINISTRATIVA' => '1924-11-14T00:00:00Z'],
    ]]));

    $this->countriesPath = tempnam(sys_get_temp_dir(), 'stati');
    $zip = new ZipArchive;
    $zip->open($this->countriesPath, ZipArchive::OVERWRITE);
    $zip->addFromString('xl/worksheets/sheet1.xml', <<<'XML'
        <worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetData>
        <row r="1"><c r="A1" t="str"><v>CODAT</v></c><c r="B1" t="str"><v>DENOMINAZIONE</v></c><c r="C1" t="str"><v>CODISO3166_1_ALPHA3</v></c><c r="D1" t="str"><v>DATAINIZIOVALIDITA</v></c><c r="E1" t="str"><v>DATAFINEVALIDITA</v></c></row>
        <row r="2"><c r="A2" t="str"><v>Z404</v></c><c r="B2" t="str"><v>STATI UNITI D'AMERICA</v></c><c r="C2" t="str"><v>USA</v></c><c r="D2" t="str"><v>01/01/1900 00:00:00</v></c><c r="E2" t="str"><v>31/12/9999 00:00:00</v></c></row>
        <row r="3"><c r="A3" t="str"><v>Z501</v></c><c r="B3" t="str"><v>ARUBA</v></c><c r="C3" t="str"><v>ABW</v></c><c r="D3" t="str"><v>01/01/1900 00:00:00</v></c><c r="E3" t="str"><v>31/12/9999 00:00:00</v></c></row>
        </sheetData></worksheet>
        XML);
    $zip->close();

    // ISTAT report 469: MAECI must win Z404 (a current country), and
    // ISTAT's historical row must win Z501, which MAECI reassigned.
    $this->historicalCountriesPath = tempnam(sys_get_temp_dir(), 'storico');
    file_put_contents($this->historicalCountriesPath, json_encode(['resultset' => [
        ['TIPO_PAESE' => 'Stato', 'DENOMINAZIONE_IT' => 'Stati Uniti', 'COD_AT' => 'Z404', 'ISO_3166_ALPHA3' => 'USA'],
        ['TIPO_PAESE' => 'Territorio storico', 'DENOMINAZIONE_IT' => 'Antille olandesi', 'COD_AT' => "Z501\u{00A0}", 'ISO_3166_ALPHA3' => 'ANT'],
    ]]));
});

afterEach(function () {
    @unlink($this->municipalitiesPath);
    @unlink($this->suppressedMunicipalitiesPath);
    @unlink($this->countriesPath);
    @unlink($this->historicalCountriesPath);
});

function importPlacesArguments(object $test, array $overrides = []): array
{
    return [
        'municipalities' => $test->municipalitiesPath,
        'suppressed-municipalities' => $test->suppressedMunicipalitiesPath,
        'countries' => $test->countriesPath,
        'historical-countries' => $test->historicalCountriesPath,
        ...$overrides,
    ];
}

it('imports every birthplace source from local files', function () {
    $this->artisan('codice-fiscale:import-places', importPlacesArguments($this))
        ->expectsOutput('Imported 2 municipality era-records from ANPR.')
        ->expectsOutput('Imported 1 suppressed comuni ANPR lacks from ISTAT.')
        ->expectsOutput('Wrote '.count(RetiredBirthPlaces::domestic()).' retired domestic codes no source carries.')
        ->expectsOutput('Imported 2 foreign country records from MAECI.')
        ->expectsOutput('Imported 2 foreign country records from ISTAT.')
        ->expectsOutput('Wrote '.count(RetiredBirthPlaces::foreign()).' retired foreign codes no source carries.')
        ->assertSuccessful();

    expect(Municipality::query()->where('code', 'A001')->pluck('name')->sort()->values()->all())
        ->toBe(['ABANO', 'ABANO TERME']);
    expect(Municipality::query()->where('code', 'C056')->value('name'))->toBe('Castegnero');
    expect(Municipality::query()->where('code', 'B930')->exists())->toBeTrue();

    expect(ForeignCountry::query()->where('code', 'Z404')->value('name'))->toBe("STATI UNITI D'AMERICA");
    expect(ForeignCountry::query()->where('code', 'Z501')->value('country_code'))->toBe('ANT');
    expect(ForeignCountry::query()->where('code', 'Z136')->exists())->toBeTrue();
});

it('fails without importing anything when a source file is missing', function (string $source) {
    $this->artisan('codice-fiscale:import-places', importPlacesArguments($this, [$source => '/does/not/exist']))
        ->expectsOutput('Cannot read "/does/not/exist".')
        ->assertFailed();

    expect(Municipality::query()->count())->toBe(0);
    expect(ForeignCountry::query()->count())->toBe(0);
})->with(['municipalities', 'suppressed-municipalities', 'countries', 'historical-countries']);
