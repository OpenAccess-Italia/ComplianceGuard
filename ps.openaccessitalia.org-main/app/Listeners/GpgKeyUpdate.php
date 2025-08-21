<?php

namespace App\Listeners;

use App\Events\GpgKeyUpdated;
use App\Http\Controllers\Admin\ActionLogController;

class GpgKeyUpdate
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(GpgKeyUpdated $event): void
    {
        $certificate = env('CNCPO_GPG_PRIVATE_KEY');
        $password = env('CNCPO_GPG_PRIVATE_KEY_PASSWORD');
        $file = storage_path('app/tmp/private_key.gpg');

        $gpgPath = preg_replace("/\r\n|\r|\n/", '', shell_exec('which gpg'));
        if (preg_match('/not found/', $gpgPath)) {
            ActionLogController::log(0, 'system', 'GPG is not installed.');
            $this->disableCncpo();

            return;
        }

        file_put_contents($file, $certificate);
        exec("$gpgPath --batch --pinentry-mode loopback --passphrase \"$password\" --import \"$file\" 2>&1", $retArr, $retVal);
        unlink($file);

        if ($retVal !== 0) {
            ActionLogController::log(0, 'system', 'GPG key update failed: '.implode("\n", $retArr));
            $this->disableCncpo();

            return;
        }

        ActionLogController::log(0, 'system', 'GPG key update success');
    }

    protected function disableCncpo()
    {
        $path = base_path('.env');
        file_put_contents($path, str_replace('CNCPO_ENABLED="1"', 'CNCPO_ENABLED="0"', file_get_contents($path)));
        $_ENV['CNCPO_ENABLED'] = '0';
    }
}
