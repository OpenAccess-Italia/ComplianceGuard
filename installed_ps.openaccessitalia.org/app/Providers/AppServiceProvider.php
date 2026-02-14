<?php

namespace App\Providers;

use App\SettingKeys;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Builder;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        Model::automaticallyEagerLoadRelationships();
        Builder::defaultStringLength(191);

        config([
            'imap.accounts.cncpo.host' => settings(SettingKeys::CNCPO_PEC_IMAP_HOST, 'localhost'),
            'imap.accounts.cncpo.port' => settings(SettingKeys::CNCPO_PEC_IMAP_PORT, 993),
            'imap.accounts.cncpo.username' => settings(SettingKeys::CNCPO_PEC_IMAP_USERNAME),
            'imap.accounts.cncpo.password' => settings(SettingKeys::CNCPO_PEC_IMAP_PASSWORD, ''),
            'mail.mailers.smtp.host' => settings( SettingKeys::MAIL_HOST),
            'mail.mailers.smtp.port' => settings( SettingKeys::MAIL_PORT),
            'mail.mailers.smtp.encryption' => settings( SettingKeys::MAIL_ENCRYPTION),
            'mail.mailers.smtp.username' => settings( SettingKeys::MAIL_USERNAME),
            'mail.mailers.smtp.password' => settings( SettingKeys::MAIL_PASSWORD),
            'mail.mailers.cncpo_pec.host' => settings( SettingKeys::CNCPO_PEC_SMTP_HOST),
            'mail.mailers.cncpo_pec.port' => settings( SettingKeys::CNCPO_PEC_SMTP_PORT, 465),
            'mail.mailers.cncpo_pec.encryption' => 'ssl',
            'mail.mailers.cncpo_pec.username' => settings( SettingKeys::CNCPO_PEC_SMTP_USERNAME),
            'mail.mailers.cncpo_pec.password' => settings( SettingKeys::CNCPO_PEC_SMTP_PASSWORD),
            'mail.from.address' => settings( SettingKeys::MAIL_FROM_ADDRESS),
            'mail.from.name' => settings( SettingKeys::MAIL_FROM_NAME),
        ]);

    }
}
