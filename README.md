
![alt text](https://github.com/OpenAccess-Italia/ComplianceGuard-App/blob/main/OAI.png)

# Disclaimer
This software is provided ​“AS IS”. Developers make no other warranties, express or implied.

#

# SORGENTI E COMPILATI APPLICATIVO 

*       installed_ps.openaccessitalia.org - instanza dell'applicativo
ready to use (file ".env" già esistente e directories "vendor" e
"node_modules" già "compilate")
*       ps.openaccessitalia.org-main - instanza dell'applicativo da
"compilare" tramite il software "composer" eseguendo il comando "composer
install" nella directory principale (file ".env" da copiare da
".env.example")
*       ps.openaccessitalia.org.sql - dump mysql del database da utilizzare
contenente già utente base admin per accesso iniziale (vedi sotto per la
password del db).


# VM e Container

L'appliance è configurata su una macchina virtuale Debian 12 che comprende
due container lxc Debian 12:
- bgp
- famp

La VM host concerta tutte le automazioni con script in /opt/src.
Il container bgp ospita il demone openbgpd. Il container famp ospita la vera
e propria APP. Anche per entrambi i container le automazioni sono sempre in
/opt/src.
Tutti gli script sono disponibili qui nel repository, cartella scripts.


# Configurazione per l'esecuzione:



Versione PHP da utilizzare è la 7.4, eventuali upgrade a php8 o successive richiedono
L'upgrade dell'intero framework Laravel https://laravel.com/

Il web server deve essere configurato con la DocumentRoot che deve essere la
directory "public" dell'applicativo.

Per la connessione al DB devono essere valorizzati i campi con il suffisso
"DB_" del file ".env" presente nella directory principale dell'applicativo.

Una volta deciso l'IP della VM si consiglia di aggiornare il campo "APP_URL"
nel file ".env" con il relativo IP o hostname in modo da evitare problemi di
raggiungibilità delle risorse pubbliche dell'app.

Per l'esecuzione dei cron è necessario mettere a crontab il seguente
comando:


"*  *  *  *  * cd {{app_directory}} && php artisan schedule:run >> /dev/null 2>&1"


dove {{app_directory}} deve essere il path della directory base dell'app.




Utilizzo iniziale dell'applicativo:



Una volta che l'app è "up & running" si può già effettuare il login con le
seguenti credenziali:


*       Username: admin
*       Password: openaccessitalia


L'utilizzo di questo utente deve essere necessario solo all'utente finale
per un primo accesso per poi generare un altro utente admin e disabilitare
quello base, come riportato nella home quando lo si utilizza ("You should
not use this user. You should create a new user with admin policy and
disable this one.").

NOTA BENE: quando si crea un nuovo utente le credenziali vengono fornite
solamente a quest'ultimo tramite l'invio di una mail all'indirizzo inserito,
è quindi necessario che prima di creare un utente vengano impostati o dalla
webgui "Admin->Settings->Edit->General->SMTP" i dati del server smtp di
preferenza oppure impostare già prima del deploy all'utente finale un smtp
pubblico direttamente nel file ".env" compilando i campo con il suffisso
"MAIL_".


L'applicativo parte con tutti i moduli disattivati sul file ".env"
attivabile o da file o da webgui nei settings.


I vari cron vengono eseguiti solo se il modulo è attivo e i campi dei
settings sono formalmente corretti, eventuali errori saranno visualizzabili
sempre nell'action log.



Files di settings:



Nella directory "storage/settings" dell'app verranno generati vari file di
setting utili per la configurazione della macchina stessa o dei nodi ad
essa collegata, di seguito la lista e le relative info:



*       network.csv - creato/aggiornato alla modifica dei settings da webgui
se i dati sono formalmente corretti - contiene i dati della parte di network
della macchina da configurare (ip,subnet mask,gateway)

*       bgp.csv - creato/aggiornato alla modifica dei settings da webgui se
i dati sono formalmente corretti - contiene i dati della parte di bgp della
macchina da configurare

*       ipsec_conf.add - creato/aggiornato alla modifica dei settings da
webgui se i dati sono formalmente corretti se il modulo PS è attivo -
contiene la parte di configurazione di strongswan da appendere a
"/etc/ipsec.conf"

*       ipsec_secrets.add - creato/aggiornato alla modifica dei settings da
webgui se i dati sono formalmente corretti se il modulo PS è attivo -
contiene la parte di configurazione di strongswan da appendere a
"/etc/ipsec.secrets"

*       iptables.add - creato/aggiornato alla modifica dei settings da webgui
se i dati sono formalmente corretti se il modulo PS è attivo - contiete il
comando da eseguire su iptables per eseguire la source-nat per raggiungere
il server API PS tramite la VPN (NOTA BENE: per far funzionare la souce-nat
è necessario prima attivare la funzione di sysctl di ip forward, impostando
il valore "net.ipv4.ip_forward" a 1 sul file "/etc/sysctl.conf")

*       hosts.add - creato/aggiornato alla modifica dei settings da webgui
se i dati sono formalmente corretti se il modulo PS è attivo - contiene la
riga da aggiungere al file "/etc/hosts" per la raggiungibilità del server
API PS tramite la VPN



Files di contenuto:

Nella directory "storage/downloads" dell'app verranno generati ogni 10
minuti i file "ipv4.txt" e "ipv6.txt" contenenti gli addresses da bannare
recuperati dalle varie liste di ban a seconda dei mod

Script

Gli script per l'automazione dei processi di raccordo tra la VM e i container
sono nel path /opt/src


# VM GIA' PRONTA ALL'USO

Di seguito il Link per la VM in formato OVA con già tutto pronto e configurato 
basterà importarla sul vostro sistema o convertirla a vostro piacimento

Si consiglia sempre comunque di leggere e seguire la sezione 
"Configurazione per l'esecuzione" sopra descritta


* https://drive.google.com/drive/folders/1Plo1aq8PP1rv1X8hUYuEQlmBp6l6VXLu?usp=sharing


IP di Default 10.255.255.1/24

GW di Default 10.255.255.254

Accesso HTTP in porta TCP 55080

Accesso SSH in porta TCP 55022

Utenti Attivi:

Terminale/SSH:

*       user: oaicg
*       pass: openaccessitalia

eseguire "su -" per accesso root con la password di root

Terminale:

*       user: root
*       pass: openaccessitalia

Si consiglia di cambiare le password una volta effettuato il primo accesso

Le credenziali di accesso al DB nel container lamp:

*       user: root
*       pass: OpenaccessItalia@2024


Prima configurazione:

Accedere all'indirizzo http://10.255.255.1:55080

*       User: admin
*       Password openaccessitalia

Seguire le istruzioni internamente per cambio password, creazione utenti admin o semplici viewer
E' possibile cambiare ip alla macchina sia da GUI che da SSH

Per configurare la parte Piracy Shield, inserire i dati come comunicati da AGCOM
nella sezione Admin-Settings-Edit Tab PiracyShield

Il sistema si autoconfigura e lancia in automatico tutti i moduli, per vedere se è
tutto funzionante basterà andare nella sezione Admin-Test alla Tab PiracyShield premere su Run Test, 
dovrà dare Success su tutte le sezioni.

Ora dobbiamo configurare la parte BGP e DNS (ovviamente è richiesta una sessione bgp verso un router 
di backbone dell'operatore e accesso in ssh verso i server dns dell'operatore)

Per configurare il neighbor del BGP bisogna andare nella sezione Admin-Settings-Edit Tab General
Nella tab BGP, Valorizzare i campi BGP Router IP e ASN con i dati corretti del router che farà
sessione con openbgp all'interno della VM. Gli altri dati, per ora, non sono necessari.

Le reti da inibire (per ora IPv4 /32 e IPv6 /128) vengono annunciate via BGP con i seguenti attributi:
- LOCAL_PREF 120
- COMMUNITY NO_EXPORT
- COMMUNITY BLACKHOLE

Dopo ogni modifica dei parametri BGP, il container lxc bgp viene riavviato.

Per configurare la parte DNS basterà andare analogamente nella tab Primary DNS / Secondary DNS
e compilare i campi richiesti per l'accesso alla macchina, il sistema creerà la zona all'interno
del server DNS inserito e aggiornerà in automatico la lista.

Per poter consentire la scrittura nel file named.conf.block e l'applicazione delle regole al DNS
sarà necessario inserire nel file named.conf la seguente stringa

*      include "/etc/bind/named.conf.block";

e creare il file named.conf.block con il comando:

*      touch /etc/bind/named.conf.block

Con la configurazione del DNS e l'abilitazione anche del modulo CNCPO e ADM, il sistema inserirà
la lista aggiornata di tutti i siti web inibiti dalle autorità alle 8 del mattino.

Per la blacklist consob fare riferimento a: https://github.com/mphilosopher/censura/blob/master/

Per l'autenticazione SSH con chiave pubblica occorre generare una chiave con i seguenti comandi:

*     lxc-attach lamp
*     cd /var/www/html/storage/settings/
*     ssh-keygen -m PEM -t rsa -f <key-name> -C 'commento'
*     chown 100033:100033 <key-name>*
*     ssh-copy-id -i <key-name> <username>@<dns>

Vengono generati i due file delle chiavi `<key-name>` (privata) e `<key-name>.pub` (pubblica).
Successivamente è possibile configurare la chiave nel campo "SSH private key" con il valore `/var/www/html/storage/settings/<key_name>`

# UPDATE DELL'APPLICATIVO

Per aggiornare la propria versione installata dovete semplicemente loggarvi in ssh dentro la vostra macchina con 
privilegi di root con il comando 
*        su -
fare update delle variabili dentro il file .env modificandolo con il vostro editor preferito e copiare le nuove modifiche presenti nel file .env.example (Vi ricordiamo che dentor il file .env ci sono i vostri parametri personali di VPN, DNS e BGP pertanto dovete fare con cautela le modifiche). Dopo aver aggiornato il file .env possiamo procedere.

Vi consigliamo di fare un backup prima di qualsiasi update in modo da poter tornare indietro lanciando questi comandi:

*       mkdir /var/lib/lxc/lamp/rootfs/var/www/htmlBCK
*       cp -a /var/lib/lxc/lamp/rootfs/var/www/html/. /var/lib/lxc/lamp/rootfs/var/www/htmlBCK/


Procediamo all'update dal lanciando i seguenti comandi:

*       mkdir /var/lib/lxc/lamp/rootfs/var/www/git
*       cd /var/lib/lxc/lamp/rootfs/var/www/git
*       git clone https://github.com/OpenAccess-Italia/ComplianceGuard
*       cp -a /var/lib/lxc/lamp/rootfs/var/www/git/ComplianceGuard/ps.openaccessitalia.org-main/. /var/lib/lxc/lamp/rootfs/var/www/html/
*       chown -R 100033:100033 /var/lib/lxc/lamp/rootfs/var/www/html
*       rm -r /var/lib/lxc/lamp/rootfs/var/www/git




Il Team di OpenAccess Italia
