# Deploy su Render

Il file `render.yaml` crea il web service gratuito. Prima del primo deploy:

1. In Render, crea un Blueprint dal repository.
2. Nella pagina **Environment** del servizio, imposta `CODICEFISCALE_UPDATE_TOKEN` con un valore casuale e lungo.
3. Aggiungi un Secret File denominato `composer_auth`. Il suo contenuto deve essere il tuo `auth.json` di Composer, contenente le credenziali per `composer.fluxui.dev`. Il Dockerfile lo monta solo durante `composer install`, senza copiarlo nell'immagine.
4. Dopo il deploy, salva in GitHub Actions i secret `RENDER_APP_URL` (l'URL pubblico Render) e `CODICEFISCALE_UPDATE_TOKEN` (lo stesso valore di Render).

Il workflow `.github/workflows/update-codicefiscale-data.yml` aggiorna i dati dei luoghi di nascita ogni lunedì alle 03:00 UTC e può essere avviato manualmente dalla scheda Actions.

Per verificare la build in locale, passa il file di autenticazione Composer come secret BuildKit:

```bash
DOCKER_BUILDKIT=1 docker build \
  --secret id=composer_auth,src="$HOME/.config/composer/auth.json" \
  --tag laravel-codicefiscale-showcase:local .
```

Il piano gratuito ha filesystem effimero: il database SQLite e il dataset aggiornato restano disponibili fino al successivo deploy, restart o sleep dell'istanza. L'immagine contiene comunque un dataset iniziale aggiornato durante ogni build.
