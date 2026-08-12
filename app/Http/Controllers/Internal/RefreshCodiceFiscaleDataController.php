<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

class RefreshCodiceFiscaleDataController extends Controller
{
    public function __invoke(): Response
    {
        $token = config('codicefiscale.update_token');

        if (! is_string($token) || $token === '') {
            abort(503, 'The codice fiscale update token is not configured.');
        }

        if (! hash_equals($token, (string) request()->header('X-Codicefiscale-Update-Token'))) {
            abort(403);
        }

        $lock = Cache::lock('codice-fiscale:update-places', 900);

        if (! $lock->get()) {
            return response()->json(['message' => 'A birthplace data refresh is already running.'], 409);
        }

        try {
            $exitCode = Artisan::call('codice-fiscale:update-places');

            if ($exitCode !== 0) {
                Log::error('Codice fiscale birthplace data refresh failed.', ['exit_code' => $exitCode]);

                return response()->json(['message' => 'Birthplace data refresh failed.'], 500);
            }

            Log::info('Codice fiscale birthplace data refreshed.');

            return response()->noContent();
        } catch (Throwable $exception) {
            Log::error('Codice fiscale birthplace data refresh threw an exception.', ['exception' => $exception]);

            return response()->json(['message' => 'Birthplace data refresh failed.'], 500);
        } finally {
            $lock->release();
        }
    }
}
