<?php

namespace App\Listeners;

use App\Events\GpgKeyUpdated;
use App\Http\Controllers\Admin\ActionLogController;
use App\SettingKeys;
use Illuminate\Support\Facades\Process;
use Settings;

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
        $certificate = Settings::get(SettingKeys::CNCPO_GPG_PRIVATE_KEY);
        $password = Settings::get(SettingKeys::CNCPO_GPG_PRIVATE_KEY_PASSWORD);

        $file = storage_path('app/tmp/private_key.gpg');

        $res = Process::run(['which', 'gpg']);

        if (!$res->successful()) {
            ActionLogController::log(0, 'system', 'GPG is not installed.');
            $this->disableCncpo();

            return;
        }
        $gpgPath = trim($res->output());
        file_put_contents($file, $certificate);

        $result = Process::run([
            $gpgPath,
            '--batch',
            '--pinentry-mode', 'loopback',
            '--passphrase', $password,
            '--import', $file,
            $file
        ]);
        unlink($file);

        if (!$result->successful()) {
            ActionLogController::log(0, 'system', 'GPG key update failed: '.$result->output().PHP_EOL.$result->errorOutput());
            $this->disableCncpo();

            return;
        }

        ActionLogController::log(0, 'system', 'GPG key update success');
    }

    protected function disableCncpo()
    {
        Settings::set(SettingKeys::CNCPO_ENABLED, '0');
    }
}
