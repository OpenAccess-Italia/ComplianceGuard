@if(now()->hour > 11)
Buonasera,
@else
Buongiorno,
@endif
con la presente si segnala che in data {{ now()->format('d/m/Y') }} e' avvenuta ricezione e applicazione della lista dei siti da inibire per il CNCPO avente progressivo {{ $listaProg }} e identificativo {{ $listaId }}.
Il messaggio e' stato generato automaticamente, pertanto vi preghiamo di segnalare qualsiasi eventuale problema o incorrettezza del presente riscontro.

Cordiali Saluti,
{{ \Settings::get(\App\SettingKeys::CNCPO_REPLY_SIGNATURE)  }}
