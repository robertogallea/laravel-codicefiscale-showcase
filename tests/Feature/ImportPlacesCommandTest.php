<?php

use Illuminate\Support\Facades\DB;
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

    $this->countriesPath = tempnam(sys_get_temp_dir(), 'stati');
    $zip = new ZipArchive;
    $zip->open($this->countriesPath, ZipArchive::OVERWRITE);
    $zip->addFromString('xl/worksheets/sheet1.xml', <<<'XML'
        <worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetData>
        <row r="1"><c r="A1" t="str"><v>CODAT</v></c><c r="B1" t="str"><v>DENOMINAZIONE</v></c><c r="C1" t="str"><v>CODISO3166_1_ALPHA3</v></c><c r="D1" t="str"><v>DATAINIZIOVALIDITA</v></c><c r="E1" t="str"><v>DATAFINEVALIDITA</v></c></row>
        <row r="2"><c r="A2" t="str"><v>Z404</v></c><c r="B2" t="str"><v>STATI UNITI D'AMERICA</v></c><c r="C2" t="str"><v>USA</v></c><c r="D2" t="str"><v>01/01/1900 00:00:00</v></c><c r="E2" t="str"><v>31/12/9999 00:00:00</v></c></row>
        </sheetData></worksheet>
        XML);
    $zip->close();
});

afterEach(function () {
    @unlink($this->municipalitiesPath);
    @unlink($this->countriesPath);
});

it('imports the municipalities CSV and countries xlsx from local files', function () {
    $this->artisan('codice-fiscale:import-places', [
        'municipalities' => $this->municipalitiesPath,
        'countries' => $this->countriesPath,
    ])
        ->expectsOutput('Imported 2 municipality era-records.')
        ->expectsOutput('Imported 1 foreign country records.')
        ->assertSuccessful();

    expect(Municipality::query()->where('code', 'A001')->pluck('name')->sort()->values()->all())
        ->toBe(['ABANO', 'ABANO TERME']);
    expect(ForeignCountry::query()->where('code', 'Z404')->value('country_code'))->toBe('USA');
});

it('fails without importing anything when a source file is missing', function () {
    $this->artisan('codice-fiscale:import-places', [
        'municipalities' => $this->municipalitiesPath,
        'countries' => '/does/not/exist.xlsx',
    ])
        ->expectsOutput('Cannot read "/does/not/exist.xlsx".')
        ->assertFailed();

    expect(Municipality::query()->count())->toBe(0);
});
