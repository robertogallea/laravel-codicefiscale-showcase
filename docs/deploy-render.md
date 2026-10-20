# Deploy su Render

Il file `render.yaml` crea il web service gratuito. Prima del primo deploy:

1. In Render, crea un Blueprint dal repository.
2. Aggiungi un Secret File denominato `composer_auth`. Il suo contenuto deve essere un `auth.json` Composer con l'email del tuo account Flux e la sua license key:

   ```json
   {
     "http-basic": {
       "composer.fluxui.dev": {
         "username": "la-tua-email-flux@example.com",
         "password": "la-tua-flux-license-key"
       }
     }
   }
   ```

   Recupera entrambi i valori dal dashboard Flux. Il Dockerfile monta questo file solo durante `composer install`, senza copiarlo nell'immagine.
3. Nelle **Settings** del servizio, copia il **Deploy Hook** e salvalo in GitHub Actions come secret `RENDER_DEPLOY_HOOK_URL`.

Il WAF di ANPR blocca le richieste provenienti dalla rete di Render, quindi la build non scarica i dati da ANPR. Il workflow `.github/workflows/update-codicefiscale-data.yml`, ogni lunedì alle 03:00 UTC (o manualmente dalla scheda Actions), scarica da ANPR il CSV dei comuni e la tabella degli stati esteri, li pubblica come asset della release `anpr-sources` e avvia un deploy tramite il Deploy Hook. La build scarica gli asset dalla release e li importa con `php artisan codice-fiscale:import-places`. Il repository deve essere pubblico perché la build possa scaricare gli asset senza autenticazione; prima del primo deploy esegui il workflow una volta, così la release esiste già.

Per verificare la build in locale, passa il file di autenticazione Composer come secret BuildKit:

```bash
DOCKER_BUILDKIT=1 docker build \
  --secret id=composer_auth,src="$HOME/.config/composer/auth.json" \
  --tag laravel-codicefiscale-showcase:local .
```

Il piano gratuito ha filesystem effimero: il database SQLite resta disponibile fino al successivo deploy, restart o sleep dell'istanza. Il dataset dei luoghi di nascita fa parte dell'immagine, quindi sopravvive ai restart e viene aggiornato a ogni build, insieme a tre profili demo deterministici che non richiedono Faker in produzione.
