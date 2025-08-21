<div>
    @if(now()->hour > 11)
    Buonasera,
    @else
    Buongiorno,
    @endif
    <br>con la presente si segnala che in data {{ now()->format('d/m/Y') }} e' avvenuta ricezione e applicazione della
        lista dei siti da inibire per il CNCPO avente progressivo <b>{{ $listaProg }}</b> e identificativo <b>{{ $listaId }}</b>.<br>
    Il messaggio e' stato generato automaticamente, pertanto vi preghiamo di segnalare qualsiasi eventuale problema
        o incorrettezza del presente riscontro.<br>
    <br>
    Cordiali Saluti.<br>
    {{ env('CNCPO_REPLY_SIGNATURE')  }}
</div>
