<div>
    @if(now()->hour > 11)
    Buonasera,
    @else
    Buongiorno,
    @endif
    <br>con la presente si segnala che in data {{ $data }} e' avvenuta ricezione e applicazione della
        lista dei siti da inibire per il CNCPO avente identificativo <b>{{ $id }}</b>.<br>
    Il messaggio e' stato generato automaticamente, pertanto vi preghiamo di segnalare qualsiasi eventuale problema
        o incorrettezza del presente riscontro.<br>
    <br>
    Cordiali Saluti.<br>
    {{ \Settings::get(\App\SettingKeys::CNCPO_REPLY_SIGNATURE)  }}
</div>
