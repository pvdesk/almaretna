<?php
declare(strict_types=1);
defined('ABSPATH') || exit;

/**
 * Dati contenuto per le pagine destinazione Sicilia.
 * Lingua rilevata via alm_get_lang().
 */

function alm_destinazioni_list(): array {
    $lang = alm_get_lang();
    $data = [
        'it' => [
            ['slug' => 'taormina',         'icon' => '🏛️', 'title' => 'Taormina',          'subtitle' => 'La perla della Sicilia orientale',       'distance' => '25 min'],
            ['slug' => 'etna',             'icon' => '🌋', 'title' => 'Etna',               'subtitle' => 'Il vulcano più alto d\'Europa',            'distance' => '20 min'],
            ['slug' => 'catania',          'icon' => '🐘', 'title' => 'Catania',             'subtitle' => 'La città dell\'elefante',                 'distance' => '30 min'],
            ['slug' => 'siracusa',         'icon' => '🏺', 'title' => 'Siracusa',            'subtitle' => 'Duemila anni di storia sul mare',         'distance' => '~90 min'],
            ['slug' => 'messina',          'icon' => '⚓', 'title' => 'Messina',             'subtitle' => 'Porta della Sicilia sullo Stretto',       'distance' => '~50 min'],
            ['slug' => 'mare-di-sicilia',  'icon' => '🌊', 'title' => 'Il mare di Sicilia',  'subtitle' => 'Fondachello, Torre Archirafi e le coste ioniche', 'distance' => '10 min'],
        ],
        'en' => [
            ['slug' => 'taormina',         'icon' => '🏛️', 'title' => 'Taormina',          'subtitle' => 'The pearl of eastern Sicily',             'distance' => '25 min'],
            ['slug' => 'etna',             'icon' => '🌋', 'title' => 'Etna',               'subtitle' => 'The highest volcano in Europe',           'distance' => '20 min'],
            ['slug' => 'catania',          'icon' => '🐘', 'title' => 'Catania',             'subtitle' => 'The city of the elephant',                'distance' => '30 min'],
            ['slug' => 'siracusa',         'icon' => '🏺', 'title' => 'Syracuse',            'subtitle' => 'Two thousand years of history by the sea','distance' => '~90 min'],
            ['slug' => 'messina',          'icon' => '⚓', 'title' => 'Messina',             'subtitle' => 'Gateway to Sicily across the Strait',     'distance' => '~50 min'],
            ['slug' => 'mare-di-sicilia',  'icon' => '🌊', 'title' => 'The Sicilian sea',    'subtitle' => 'Fondachello, Torre Archirafi and the Ionian coast', 'distance' => '10 min'],
        ],
        'de' => [
            ['slug' => 'taormina',         'icon' => '🏛️', 'title' => 'Taormina',          'subtitle' => 'Die Perle Ostsiziliens',                  'distance' => '25 Min.'],
            ['slug' => 'etna',             'icon' => '🌋', 'title' => 'Ätna',               'subtitle' => 'Der höchste Vulkan Europas',              'distance' => '20 Min.'],
            ['slug' => 'catania',          'icon' => '🐘', 'title' => 'Catania',             'subtitle' => 'Die Stadt des Elefanten',                 'distance' => '30 Min.'],
            ['slug' => 'siracusa',         'icon' => '🏺', 'title' => 'Syrakus',             'subtitle' => 'Zweitausend Jahre Geschichte am Meer',    'distance' => '~90 Min.'],
            ['slug' => 'messina',          'icon' => '⚓', 'title' => 'Messina',             'subtitle' => 'Tor zur Meerenge von Messina',            'distance' => '~50 Min.'],
            ['slug' => 'mare-di-sicilia',  'icon' => '🌊', 'title' => 'Das Sizilianische Meer', 'subtitle' => 'Fondachello, Torre Archirafi und die Ionische Küste', 'distance' => '10 Min.'],
        ],
        'fr' => [
            ['slug' => 'taormina',         'icon' => '🏛️', 'title' => 'Taormine',          'subtitle' => 'La perle de la Sicile orientale',         'distance' => '25 min'],
            ['slug' => 'etna',             'icon' => '🌋', 'title' => 'Etna',               'subtitle' => 'Le plus haut volcan d\'Europe',           'distance' => '20 min'],
            ['slug' => 'catania',          'icon' => '🐘', 'title' => 'Catane',              'subtitle' => 'La ville de l\'éléphant',                 'distance' => '30 min'],
            ['slug' => 'siracusa',         'icon' => '🏺', 'title' => 'Syracuse',            'subtitle' => 'Deux mille ans d\'histoire en mer',       'distance' => '~90 min'],
            ['slug' => 'messina',          'icon' => '⚓', 'title' => 'Messine',             'subtitle' => 'Porte de la Sicile sur le Détroit',       'distance' => '~50 min'],
            ['slug' => 'mare-di-sicilia',  'icon' => '🌊', 'title' => 'La mer de Sicile',    'subtitle' => 'Fondachello, Torre Archirafi et la côte ionienne', 'distance' => '10 min'],
        ],
        'es' => [
            ['slug' => 'taormina',         'icon' => '🏛️', 'title' => 'Taormina',          'subtitle' => 'La perla de Sicilia oriental',            'distance' => '25 min'],
            ['slug' => 'etna',             'icon' => '🌋', 'title' => 'Etna',               'subtitle' => 'El volcán más alto de Europa',            'distance' => '20 min'],
            ['slug' => 'catania',          'icon' => '🐘', 'title' => 'Catania',             'subtitle' => 'La ciudad del elefante',                  'distance' => '30 min'],
            ['slug' => 'siracusa',         'icon' => '🏺', 'title' => 'Siracusa',            'subtitle' => 'Dos mil años de historia junto al mar',   'distance' => '~90 min'],
            ['slug' => 'messina',          'icon' => '⚓', 'title' => 'Mesina',              'subtitle' => 'Puerta de Sicilia en el Estrecho',        'distance' => '~50 min'],
            ['slug' => 'mare-di-sicilia',  'icon' => '🌊', 'title' => 'El mar de Sicilia',   'subtitle' => 'Fondachello, Torre Archirafi y la costa jónica', 'distance' => '10 min'],
        ],
    ];
    return $data[$lang] ?? $data['it'];
}

function alm_destinazione_data(string $slug): array {
    $lang = alm_get_lang();

    $all = [

        /* ──────────── TAORMINA ──────────── */
        'taormina' => [
            'it' => [
                'title'    => 'Taormina',
                'tagline'  => 'La perla della Sicilia orientale',
                'intro'    => "A soli 25 minuti da Almaretna, Taormina è una delle mete più celebri del Mediterraneo. Arroccata su un promontorio con vista sul mare e sull'Etna, offre un mix irresistibile di storia millenaria, bellezze naturali e vita mondana.",
                'attractions' => [
                    ['Teatro Antico', "Il teatro greco-romano meglio conservato della Sicilia, con una vista mozzafiato sull'Etna e sul mare. Costruito nel III secolo a.C., ospita ancora oggi concerti e spettacoli estivi."],
                    ['Isola Bella', "Una piccola isola collegata alla terraferma da un sottile istmo sabbioso. Le sue acque cristalline sono tra le spiagge più fotografate di Sicilia."],
                    ['Castelmola', "Il borgo medievale sopra Taormina offre una vista panoramica a 360° tra le più belle della Sicilia orientale. Raggiungibile a piedi o in auto."],
                    ['Corso Umberto', "Il cuore della città: boutique, caffè storici, chiese barocche e piazze vivaci. Il luogo ideale per assaporare il ritmo della vita siciliana."],
                    ['Giardini Naxos', "Appena sotto Taormina, la baia offre una lunga spiaggia sabbiosa e un porto turistico vivace, ideale per chi ama il mare e la movida serale."],
                ],
                'summer'   => "In estate Taormina è al suo massimo splendore: il mare, i festival (tra cui il celebre Taormina Arte), le terrazze con vista e la vita notturna la rendono una destinazione da sogno.",
                'winter'   => "In inverno Taormina si trasforma in una meta romantica e autentica. Meno folla, prezzi più bassi, clima mite e nessuna coda ai monumenti. Nelle giornate limpide la vista sull'Etna innevato è spettacolare.",
                'distance' => '25 min da Almaretna',
                'dist_note'=> 'Strada panoramica costiera. Consigliamo la mattina presto o il tardo pomeriggio in estate.',
                'sections' => [
                    [
                        'title' => 'Tremila anni di storia',
                        'text'  => "Taormina non è nata con i Greci, anche se è con loro che raggiunse la sua prima grande fioritura. La città di Naxos, la più antica colonia greca di Sicilia, fu fondata nel 734 a.C. e sorgeva proprio ai piedi dell'attuale Taormina. Dopo la sua distruzione, i superstiti risalirono il promontorio e diedero vita a Tauromenium. Nei secoli successivi passò ai Romani, ai Bizantini, agli Arabi e ai Normanni: ogni civiltà ha lasciato il proprio segno in pietre, archi, sapori e tradizioni. Passeggiare per il centro storico significa attraversare secoli di storia mediterranea in poche centinaia di metri.",
                    ],
                    [
                        'title' => 'Sapori e tradizioni da non perdere',
                        'text'  => "La cucina taorminese è un inno alla Sicilia orientale: granite di mandorla con brioscia calda per colazione, pesce spada alla messinese, arancini croccanti e dolci di mandorle. Il mercato coperto al Palazzo dei Duchi di Santo Stefano ospita prodotti artigianali e gastronomici locali. Lungo il Corso Umberto si trovano alcune delle migliori pasticcerie della costa ionica, dove assaggiare i cannoli freschi e i famosi dolci al pistacchio. Il vino Etna DOC — prodotto proprio sulle pendici del vulcano che si vede dalla terrazza — è ormai riconosciuto tra i migliori d'Italia.",
                    ],
                    [
                        'title' => 'Consigli per la visita',
                        'text'  => "Taormina si visita idealmente di mattina presto (prima delle 9) o nel tardo pomeriggio. D'estate le sue strade si riempiono di turisti: prenotare il Teatro Antico online è indispensabile. Almaretna si trova a 25 minuti di guida, su una strada panoramica che costeggia il mare. Se preferisci evitare l'auto, in estate corre un servizio di navetta dalla stazione di Taormina-Giardini. Per il parcheggio, il garage Lumbi o il parcheggio sul lungomare di Mazzarò sono le soluzioni più comode.",
                    ],
                ],
            ],
            'en' => [
                'title'    => 'Taormina',
                'tagline'  => 'The pearl of eastern Sicily',
                'intro'    => "Just 25 minutes from Almaretna, Taormina is one of the most celebrated destinations in the Mediterranean. Perched on a headland overlooking the sea and Etna, it offers an irresistible mix of ancient history, natural beauty and cosmopolitan life.",
                'attractions' => [
                    ['Ancient Theatre', "The best-preserved Greco-Roman theatre in Sicily, with a breathtaking view of Etna and the sea. Built in the 3rd century BC, it still hosts summer concerts and shows."],
                    ['Isola Bella', "A tiny island connected to the mainland by a thin sandy isthmus. Its crystal-clear waters are among the most photographed beaches in Sicily."],
                    ['Castelmola', "The medieval village above Taormina offers a 360° panoramic view, one of the finest in eastern Sicily. Reachable on foot or by car."],
                    ['Corso Umberto', "The heart of the city: boutiques, historic cafés, baroque churches and lively piazzas. The perfect place to savour the rhythm of Sicilian life."],
                    ['Giardini Naxos', "Just below Taormina, the bay offers a long sandy beach and a busy marina, ideal for those who love the sea and evening entertainment."],
                ],
                'summer'   => "In summer Taormina is at its most glorious: the sea, festivals (including the renowned Taormina Arte), terraces with views and nightlife make it a dream destination.",
                'winter'   => "In winter Taormina becomes a romantic and authentic destination. Fewer crowds, lower prices, mild climate and no queuing at monuments. On clear days, the view of snow-capped Etna is spectacular.",
                'distance' => '25 min from Almaretna',
                'dist_note'=> 'Scenic coastal road. We recommend early morning or late afternoon in summer.',
            ],
            'de' => [
                'title'    => 'Taormina',
                'tagline'  => 'Die Perle Ostsiziliens',
                'intro'    => "Nur 25 Minuten von Almaretna entfernt, ist Taormina eines der bekanntesten Reiseziele des Mittelmeers. Auf einem Vorgebirge mit Blick auf das Meer und den Ätna gelegen, bietet es eine unwiderstehliche Mischung aus jahrtausendealter Geschichte, Naturschönheiten und mondänem Leben.",
                'attractions' => [
                    ['Antikes Theater', "Das am besten erhaltene griechisch-römische Theater Siziliens, mit einem atemberaubenden Blick auf den Ätna und das Meer. Im 3. Jh. v. Chr. erbaut, beherbergt es noch heute Sommerkonzerte und Aufführungen."],
                    ['Isola Bella', "Eine kleine Insel, die durch einen schmalen Sandstreifen mit dem Festland verbunden ist. Ihre kristallklaren Gewässer gehören zu den meistfotografierten Stränden Siziliens."],
                    ['Castelmola', "Das mittelalterliche Dorf oberhalb von Taormina bietet einen 360°-Panoramablick, einer der schönsten in Ostsizilien. Zu Fuß oder mit dem Auto erreichbar."],
                    ['Corso Umberto', "Das Herzstück der Stadt: Boutiquen, historische Cafés, Barockkirchen und belebte Piazzas. Der ideale Ort, um den Rhythmus des sizilianischen Lebens zu genießen."],
                    ['Giardini Naxos', "Direkt unterhalb von Taormina bietet die Bucht einen langen Sandstrand und einen lebhaften Yachthafen, ideal für Strandliebhaber und Abendunterhaltung."],
                ],
                'summer'   => "Im Sommer zeigt sich Taormina von seiner schönsten Seite: das Meer, Festivals (darunter das renommierte Taormina Arte), Terrassen mit Aussicht und Nachtleben machen es zum Traumziel.",
                'winter'   => "Im Winter verwandelt sich Taormina in ein romantisches und authentisches Reiseziel. Weniger Massen, niedrigere Preise, mildes Klima und keine Warteschlangen an den Sehenswürdigkeiten. An klaren Tagen ist der Blick auf den schneebedeckten Ätna spektakulär.",
                'distance' => '25 Min. von Almaretna',
                'dist_note'=> 'Malerische Küstenstraße. Im Sommer empfehlen wir früh morgens oder spätnachmittags.',
            ],
            'fr' => [
                'title'    => 'Taormine',
                'tagline'  => 'La perle de la Sicile orientale',
                'intro'    => "À seulement 25 minutes d'Almaretna, Taormine est l'une des destinations les plus célèbres de la Méditerranée. Perchée sur un promontoire avec vue sur la mer et l'Etna, elle offre un mélange irrésistible d'histoire millénaire, de beautés naturelles et de vie mondaine.",
                'attractions' => [
                    ['Théâtre Antique', "Le théâtre gréco-romain le mieux conservé de Sicile, avec une vue imprenable sur l'Etna et la mer. Construit au IIIe siècle av. J.-C., il accueille encore des concerts et spectacles estivaux."],
                    ['Isola Bella', "Une petite île reliée au continent par un fin cordon de sable. Ses eaux cristallines sont parmi les plages les plus photographiées de Sicile."],
                    ['Castelmola', "Le village médiéval au-dessus de Taormine offre une vue panoramique à 360°, parmi les plus belles de Sicile orientale. Accessible à pied ou en voiture."],
                    ['Corso Umberto', "Le cœur de la ville : boutiques, cafés historiques, églises baroques et places animées. L'endroit idéal pour savourer le rythme de la vie sicilienne."],
                    ['Giardini Naxos', "Juste en dessous de Taormine, la baie offre une longue plage de sable et un port de plaisance animé, idéal pour les amateurs de mer et de soirées."],
                ],
                'summer'   => "En été Taormine est dans toute sa splendeur : la mer, les festivals (dont le célèbre Taormina Arte), les terrasses avec vue et la vie nocturne en font une destination de rêve.",
                'winter'   => "En hiver Taormine se transforme en destination romantique et authentique. Moins de foule, prix plus bas, climat doux et pas de queue aux monuments. Par temps clair, la vue sur l'Etna enneigé est spectaculaire.",
                'distance' => '25 min d\'Almaretna',
                'dist_note'=> 'Route côtière panoramique. Nous recommandons tôt le matin ou en fin d\'après-midi en été.',
            ],
            'es' => [
                'title'    => 'Taormina',
                'tagline'  => 'La perla de Sicilia oriental',
                'intro'    => "A solo 25 minutos de Almaretna, Taormina es uno de los destinos más celebrados del Mediterráneo. Encaramada en un promontorio con vistas al mar y al Etna, ofrece una combinación irresistible de historia milenaria, bellezas naturales y vida cosmopolita.",
                'attractions' => [
                    ['Teatro Antiguo', "El teatro grecoromano mejor conservado de Sicilia, con vistas impresionantes al Etna y al mar. Construido en el siglo III a.C., todavía acoge conciertos y espectáculos de verano."],
                    ['Isola Bella', "Una pequeña isla unida a tierra firme por un delgado istmo de arena. Sus aguas cristalinas están entre las playas más fotografiadas de Sicilia."],
                    ['Castelmola', "El pueblo medieval sobre Taormina ofrece una vista panorámica de 360°, de las más bellas de Sicilia oriental. Accesible a pie o en coche."],
                    ['Corso Umberto', "El corazón de la ciudad: boutiques, cafés históricos, iglesias barrocas y plazas animadas. El lugar ideal para saborear el ritmo de la vida siciliana."],
                    ['Giardini Naxos', "Justo debajo de Taormina, la bahía ofrece una larga playa de arena y un puerto deportivo animado, ideal para los amantes del mar y el ocio nocturno."],
                ],
                'summer'   => "En verano Taormina está en su máximo esplendor: el mar, los festivales (incluido el célebre Taormina Arte), las terrazas con vistas y la vida nocturna la convierten en un destino de ensueño.",
                'winter'   => "En invierno Taormina se convierte en un destino romántico y auténtico. Menos multitudes, precios más bajos, clima suave y sin colas en los monumentos. En días despejados, la vista del Etna nevado es espectacular.",
                'distance' => '25 min desde Almaretna',
                'dist_note'=> 'Carretera panorámica costera. Recomendamos la mañana temprano o la tarde en verano.',
            ],
        ],

        /* ──────────── ETNA ──────────── */
        'etna' => [
            'it' => [
                'title'    => 'Etna',
                'tagline'  => 'Il vulcano più alto d\'Europa',
                'intro'    => "A soli 20 minuti da Almaretna si apre l'accesso al versante nord dell'Etna. Il vulcano più attivo d'Europa, con i suoi 3.357 metri, è un'esperienza che non ha eguali al mondo — d'estate come d'inverno.",
                'attractions' => [
                    ['Estate sull\'Etna 🌞', "Da giugno a ottobre l'Etna si esplora a piedi, in jeep o con la funivia. I percorsi guidati portano fino ai bordi dei crateri sommitali, dove la terra fuma e il paesaggio è alieno. I Crateri Silvestri, raggiungibili in auto dal Rifugio Sapienza (Etna Sud), sono ideali per chi non vuole una camminata impegnativa."],
                    ['Inverno sull\'Etna ⛷️', "Da dicembre ad aprile l'Etna si ricopre di neve e diventa una stazione sciistica unica al mondo. A Piano Provenzana (Etna Nord, accessibile da Linguaglossa — vicino ad Almaretna) ci sono piste da sci, snowboard e ciaspolate con vista sul mare. Sciare con il vulcano sullo sfondo e il Mediterraneo all'orizzonte è un'esperienza irripetibile."],
                    ['Escursioni guidate', "Le guide vulcanologiche accompagnano i visitatori lungo percorsi studiati per ogni livello di preparazione fisica. Disponibili tutto l'anno, con attrezzatura, abbigliamento tecnico e tutto il necessario."],
                    ['Funivia dell\'Etna', "La funivia parte dal Rifugio Sapienza (Etna Sud, raggiungibile in auto) e porta a 2.500 metri di quota, da dove si prosegue con jeep 4×4 o a piedi con guida. Aperta da marzo a ottobre."],
                    ['Vini e cantine dell\'Etna', "Le pendici del vulcano producono alcuni dei vini più apprezzati d'Italia: il DOC Etna con uve Nerello Mascalese e Carricante cresciute su suoli lavici. Le cantine della zona offrono visite e degustazioni memorabili."],
                ],
                'summer'   => "In estate l'Etna è accessibile fino ai crateri sommitali. I trekking al tramonto, le escursioni in jeep e le colate laviche raffreddate di anni recenti sono esperienze indimenticabili.",
                'winter'   => "In inverno l'Etna offre sciistica, ciaspolate e paesaggi da cartolina. Piano Provenzana (Etna Nord) è la stazione sciistica più vicina ad Almaretna — meno di 30 minuti.",
                'distance' => '20 min (accesso Etna Nord – Piano Provenzana)',
                'dist_note'=> 'Via Linguaglossa. Accesso Etna Sud (Rifugio Sapienza) circa 45 min.',
                'sections' => [
                    [
                        'title' => 'Un vulcano vivo, non un museo',
                        'text'  => "L'Etna non è un vulcano da contemplare da lontano: è un sistema geologico attivo che cambia forma ogni anno. Le eruzioni del 2019, 2021 e 2022 hanno aperto nuovi crateri, modificato i panorami e depositato colate laviche fresche visibili a occhio nudo. Camminare su quel terreno scuro e rugoso, ancora caldo sotto la suola, è un'esperienza che rende concreta la forza della natura. Il contrasto tra il nero della lava e il verde dei boschi di betulla e leccio che crescono tra le colate è uno dei paesaggi più surreali e belli d'Italia.",
                    ],
                    [
                        'title' => 'Etna d\'estate: dai crateri al vino',
                        'text'  => "Da giugno a ottobre l'Etna si esplora con trekking guidati, escursioni in jeep 4×4 e la funivia che da Rifugio Sapienza sale fino a quota 2.500 metri. I percorsi per i Crateri Silvestri (facilissimi, adatti a famiglie) e quelli per i crateri sommitali (con guida vulcanologica obbligatoria) offrono esperienze completamente diverse. Nel tardo pomeriggio, le cantine della DOC Etna — Benanti, Passopisciaro, Cornelissen, Terre Nere — aprono le porte per degustazioni di Nerello Mascalese e Carricante, tra i vini più discussi d'Italia. Una giornata sull'Etna inizia sul vulcano e finisce con un bicchiere di rosso davanti al tramonto.",
                    ],
                    [
                        'title' => 'Come organizzare la visita da Almaretna',
                        'text'  => "Da Almaretna l'accesso più veloce è quello nord, via Linguaglossa, in circa 20 minuti fino a Piano Provenzana. Da qui partono escursioni guidate, noleggi di abbigliamento tecnico e, in inverno, gli impianti sciistici. Per l'accesso sud (funivia, Crateri Silvestri) il percorso è Nunziata → Zafferana Etnea → Rifugio Sapienza: circa 45-50 minuti. Si consiglia di prenotare le escursioni guidate con anticipo, specialmente in luglio e agosto. Gli abiti leggeri non bastano oltre i 2.000 metri, anche d'estate: una giacca a vento e scarpe chiuse sono indispensabili.",
                    ],
                ],
            ],
            'en' => [
                'title'    => 'Etna',
                'tagline'  => 'The highest volcano in Europe',
                'intro'    => "Just 20 minutes from Almaretna lies the northern access to Etna. Europe's most active volcano, at 3,357 metres, is an unparalleled experience — in summer as in winter.",
                'attractions' => [
                    ['Summer on Etna 🌞', "From June to October Etna can be explored on foot, by jeep or cable car. Guided tours lead to the rim of the summit craters, where the earth smokes and the landscape is otherworldly. The Silvestri Craters, reachable by car from Rifugio Sapienza (Etna Sud), are ideal for those seeking a gentler walk."],
                    ['Winter on Etna ⛷️', "From December to April Etna is blanketed in snow and becomes a one-of-a-kind ski resort. At Piano Provenzana (Etna Nord, accessible from Linguaglossa — near Almaretna) there are ski slopes, snowboarding and snowshoeing with sea views. Skiing with the volcano behind you and the Mediterranean on the horizon is a truly unique experience."],
                    ['Guided Excursions', "Volcanologist guides lead visitors along routes tailored to every fitness level. Available year-round, with equipment, technical clothing and everything you need provided."],
                    ['Etna Cable Car', "The cable car departs from Rifugio Sapienza (Etna Sud, reachable by car) and rises to 2,500 metres, from where you continue by 4×4 jeep or on foot with a guide. Open March to October."],
                    ['Etna wines and wineries', "The volcano's slopes produce some of Italy's most acclaimed wines: the Etna DOC with Nerello Mascalese and Carricante grapes grown in volcanic soils. Local wineries offer unforgettable tours and tastings."],
                ],
                'summer'   => "In summer Etna is accessible up to the summit craters. Sunset treks, jeep tours and recent lava flows are unforgettable experiences.",
                'winter'   => "In winter Etna offers skiing, snowshoeing and picture-postcard landscapes. Piano Provenzana (Etna Nord) is the closest ski resort to Almaretna — under 30 minutes.",
                'distance' => '20 min (Etna Nord – Piano Provenzana access)',
                'dist_note'=> 'Via Linguaglossa. Etna Sud access (Rifugio Sapienza) approx. 45 min.',
            ],
            'de' => [
                'title'    => 'Ätna',
                'tagline'  => 'Der höchste Vulkan Europas',
                'intro'    => "Nur 20 Minuten von Almaretna entfernt liegt der Nordzugang zum Ätna. Europas aktivster Vulkan mit seinen 3.357 Metern ist ein einzigartiges Erlebnis — im Sommer wie im Winter.",
                'attractions' => [
                    ['Sommer am Ätna 🌞', "Von Juni bis Oktober kann der Ätna zu Fuß, per Jeep oder Seilbahn erkundet werden. Geführte Touren führen bis an den Rand der Gipfelkrater, wo die Erde raucht und die Landschaft außerweltlich wirkt. Die Silvestri-Krater, per Auto vom Rifugio Sapienza (Ätna Süd) erreichbar, eignen sich für einen gemächlicheren Spaziergang."],
                    ['Winter am Ätna ⛷️', "Von Dezember bis April bedeckt Schnee den Ätna und verwandelt ihn in ein einzigartiges Skigebiet. An Piano Provenzana (Ätna Nord, von Linguaglossa erreichbar — nahe Almaretna) gibt es Skipisten, Snowboarden und Schneeschuhwandern mit Meerblick. Skifahren mit dem Vulkan im Rücken und dem Mittelmeer am Horizont ist ein unvergessliches Erlebnis."],
                    ['Geführte Ausflüge', "Vulkanologische Führer begleiten Besucher auf Routen, die jedem Fitnessniveau angepasst sind. Ganzjährig verfügbar, mit Ausrüstung und technischer Kleidung."],
                    ['Seilbahn des Ätna', "Die Seilbahn startet vom Rifugio Sapienza (Ätna Süd, per Auto erreichbar) und steigt auf 2.500 Meter, von wo aus man per Geländejeep oder zu Fuß mit Führer weiterfährt. Geöffnet März bis Oktober."],
                    ['Ätna-Weine und Weingüter', "Die Hänge des Vulkans produzieren einige der renommiertesten Weine Italiens: den Etna DOC mit Nerello Mascalese und Carricante auf vulkanischen Böden. Lokale Weingüter bieten unvergessliche Führungen und Verkostungen."],
                ],
                'summer'   => "Im Sommer ist der Ätna bis zu den Gipfelkratern zugänglich. Sonnenuntergangs-Trekking, Jeep-Touren und kürzliche Lavafelder sind unvergessliche Erlebnisse.",
                'winter'   => "Im Winter bietet der Ätna Skifahren, Schneeschuhwandern und Bilderbuchlandschaften. Piano Provenzana (Ätna Nord) ist das nächste Skigebiet zu Almaretna — unter 30 Minuten.",
                'distance' => '20 Min. (Ätna Nord – Piano Provenzana Zugang)',
                'dist_note'=> 'Über Linguaglossa. Ätna Süd (Rifugio Sapienza) ca. 45 Min.',
            ],
            'fr' => [
                'title'    => 'Etna',
                'tagline'  => 'Le plus haut volcan d\'Europe',
                'intro'    => "À seulement 20 minutes d'Almaretna se trouve l'accès nord à l'Etna. Le volcan actif le plus élevé d'Europe, avec ses 3 357 mètres, est une expérience sans égale — en été comme en hiver.",
                'attractions' => [
                    ['Été sur l\'Etna 🌞', "De juin à octobre, l'Etna se parcourt à pied, en jeep ou par téléphérique. Les visites guidées mènent jusqu'au bord des cratères sommitaux, où la terre fume et le paysage est lunaire. Les Cratères Silvestri, accessibles en voiture depuis le Rifugio Sapienza (Etna Sud), conviennent à ceux qui préfèrent une promenade plus douce."],
                    ['Hiver sur l\'Etna ⛷️', "De décembre à avril, l'Etna se couvre de neige et devient une station de ski unique au monde. À Piano Provenzana (Etna Nord, accessible depuis Linguaglossa — près d'Almaretna) on trouve des pistes de ski, du snowboard et des randonnées en raquettes avec vue sur la mer. Skier avec le volcan en toile de fond et la Méditerranée à l'horizon est une expérience inoubliable."],
                    ['Excursions guidées', "Des guides volcanologues accompagnent les visiteurs sur des itinéraires adaptés à tous les niveaux. Disponibles toute l'année, avec équipement et vêtements techniques fournis."],
                    ['Téléphérique de l\'Etna', "Le téléphérique part du Rifugio Sapienza (Etna Sud, accessible en voiture) et monte à 2 500 mètres d'altitude, d'où l'on continue en jeep 4×4 ou à pied avec un guide. Ouvert de mars à octobre."],
                    ['Vins et domaines de l\'Etna', "Les pentes du volcan produisent certains des vins les plus appréciés d'Italie : l'Etna DOC avec Nerello Mascalese et Carricante cultivés sur sols volcaniques. Les domaines locaux proposent des visites et dégustations inoubliables."],
                ],
                'summer'   => "En été l'Etna est accessible jusqu'aux cratères sommitaux. Les randonnées au coucher du soleil, les tours en jeep et les coulées de lave récentes sont des expériences inoubliables.",
                'winter'   => "En hiver l'Etna offre ski, raquettes et paysages de carte postale. Piano Provenzana (Etna Nord) est la station de ski la plus proche d'Almaretna — moins de 30 minutes.",
                'distance' => '20 min (accès Etna Nord – Piano Provenzana)',
                'dist_note'=> 'Via Linguaglossa. Accès Etna Sud (Rifugio Sapienza) environ 45 min.',
            ],
            'es' => [
                'title'    => 'Etna',
                'tagline'  => 'El volcán más alto de Europa',
                'intro'    => "A solo 20 minutos de Almaretna se encuentra el acceso norte al Etna. El volcán activo más alto de Europa, con sus 3.357 metros, es una experiencia sin igual en el mundo — en verano como en invierno.",
                'attractions' => [
                    ['Verano en el Etna 🌞', "De junio a octubre el Etna se puede explorar a pie, en jeep o por teleférico. Las excursiones guiadas llevan hasta el borde de los cráteres cimeros, donde la tierra humea y el paisaje es alienígena. Los Cráteres Silvestri, accesibles en coche desde el Rifugio Sapienza (Etna Sur), son ideales para una caminata más sencilla."],
                    ['Invierno en el Etna ⛷️', "De diciembre a abril el Etna se cubre de nieve y se convierte en una estación de esquí única en el mundo. En Piano Provenzana (Etna Norte, accesible desde Linguaglossa — cerca de Almaretna) hay pistas de esquí, snowboard y raquetas de nieve con vistas al mar. Esquiar con el volcán de fondo y el Mediterráneo en el horizonte es una experiencia irrepetible."],
                    ['Excursiones guiadas', "Guías vulcanológicos acompañan a los visitantes por rutas adaptadas a todos los niveles. Disponibles todo el año, con equipo y ropa técnica incluidos."],
                    ['Teleférico del Etna', "El teleférico parte del Rifugio Sapienza (Etna Sur, accesible en coche) y sube a 2.500 metros, desde donde se continúa en jeep 4×4 o a pie con guía. Abierto de marzo a octubre."],
                    ['Vinos y bodegas del Etna', "Las laderas del volcán producen algunos de los vinos más apreciados de Italia: el DOC Etna con uvas Nerello Mascalese y Carricante cultivadas en suelos volcánicos. Las bodegas de la zona ofrecen visitas y catas memorables."],
                ],
                'summer'   => "En verano el Etna es accesible hasta los cráteres cimeros. Los trekking al atardecer, los tours en jeep y las coladas de lava recientes son experiencias inolvidables.",
                'winter'   => "En invierno el Etna ofrece esquí, raquetas de nieve y paisajes de postal. Piano Provenzana (Etna Norte) es la estación de esquí más cercana a Almaretna — a menos de 30 minutos.",
                'distance' => '20 min (acceso Etna Norte – Piano Provenzana)',
                'dist_note'=> 'Por Linguaglossa. Acceso Etna Sur (Rifugio Sapienza) aprox. 45 min.',
            ],
        ],

        /* ──────────── CATANIA ──────────── */
        'catania' => [
            'it' => [
                'title'    => 'Catania',
                'tagline'  => 'La città dell\'elefante, ai piedi dell\'Etna',
                'intro'    => "A 30 minuti da Almaretna, Catania è una città vivace e autentica, costruita in pietra lavica scura, patrimonio UNESCO. Capoluogo della Sicilia orientale e hub di viaggio internazionale, nasconde un centro storico ricco di tesori barocchi e di vita.",
                'attractions' => [
                    ['Piazza del Duomo e Fontana dell\'Elefante', "Il simbolo di Catania: l'elefante di basalto con l'obelisco egizio al centro di una delle piazze barocche più belle d'Italia. Il Duomo dedicato a Sant'Agata chiude magnificamente la scena."],
                    ['Pescheria', "Il mercato del pesce di Catania è uno spettacolo unico per colori, profumi e voci. Uno dei mercati storici più famosi d'Italia, tra i vicoli del centro storico."],
                    ['Castello Ursino', "Fortezza normanna del XIII secolo, oggi sede del Museo Civico. Testimonianza imponente della storia medievale di Catania, circondata dai quartieri più pittoreschi della città."],
                    ['Via Etnea', "Il corso principale di Catania punta dritto verso il vulcano. Shopping, gelaterie, caffè storici e — sullo sfondo — l'imponente profilo dell'Etna."],
                    ['Teatro Massimo Bellini', "Uno dei teatri d'opera più belli d'Italia, dedicato al compositore catanese Vincenzo Bellini. Il foyer affrescato e la sala con 1.200 posti sono un capolavoro liberty."],
                    ['Festa di Sant\'Agata', "Ogni anno a febbraio, una delle processioni religiose più grandi d'Europa: oltre un milione di fedeli per tre giorni di devozione, cera e fuochi d'artificio intorno al fercolo d'argento."],
                ],
                'summer'   => "D'estate Catania è la base ideale per le escursioni sull'Etna e per le spiagge. La movida del centro storico, il mercato serale e i locali del quartiere Borgo offrono autentici sapori siciliani.",
                'winter'   => "In inverno Catania mantiene un clima mite e una vita cittadina piena d'energia. Il periodo natalizio e la Festa di Sant'Agata (febbraio) sono momenti di festa imperdibili, con l'intera città in movimento.",
                'distance' => '30 min da Almaretna',
                'dist_note'=> 'Autostrada A18. Aeroporto di Catania-Fontanarossa: 30 min — gateway per voli internazionali.',
                'sections' => [
                    [
                        'title' => 'Pietra lavica e barocco UNESCO',
                        'text'  => "Catania è costruita su se stessa: ogni terremoto, ogni eruzione — e ce ne sono stati molti — ha sepolto e poi rivelato strati di storia. Il centro storico che vediamo oggi è in gran parte quello ricostruito dopo il devastante terremoto del 1693 e l'eruzione del 1669. I palazzi barocchi in pietra lavica scura che si affacciano su Piazza del Duomo sono stati proclamati Patrimonio UNESCO nel 2002 insieme ad altre sette città siciliane del Val di Noto. Questa pietra nera è il DNA di Catania: la stessa materia che distrugge diventa la materia con cui si costruisce.",
                    ],
                    [
                        'title' => 'La Pescheria e la cucina di strada',
                        'text'  => "Il mercato del pesce di Catania — la Pescheria — è uno spettacolo che si tiene dal lunedì al sabato mattina, nascosto dietro la Fontana dell'Elefante, in pieno centro storico. I banchi del pesce fresco si affiancano a quelli di frutta, verdura, spezie e salumi in un'esplosione sensoriale che è rimasta invariata per secoli. Qui si comprendono le radici della cucina catanese: pasta alla norma, arancini alla carne, impanate di pesce, polpette di melanzane. Il street food del centro — in particolare quello di Via Plebiscito e Piazza Carlo Alberto — è tra i più ricchi e autentici della Sicilia.",
                    ],
                    [
                        'title' => 'Come visitare Catania da Almaretna',
                        'text'  => "Catania dista circa 30 minuti da Almaretna via A18. Per una giornata intera si consiglia di arrivare entro le 9 per godersi la Pescheria al massimo della sua attività. I parcheggi più comodi sono quelli interrati vicino a Piazza Stesicoro o al porto. In alternativa, la stazione ferroviaria di Giarre-Riposto (5 minuti da Almaretna) ha treni frequenti per Catania Centrale in circa 35 minuti — una comodità che evita lo stress del traffico e del parcheggio. L'aeroporto di Fontanarossa è a 30 minuti da Almaretna: utile per chi arriva o parte in giornata.",
                    ],
                ],
            ],
            'en' => [
                'title'    => 'Catania',
                'tagline'  => 'The city of the elephant, at the foot of Etna',
                'intro'    => "30 minutes from Almaretna, Catania is a vibrant and authentic city built from dark lava stone, a UNESCO heritage site. Eastern Sicily's main city and international travel hub, it hides a historic centre full of baroque treasures and city life.",
                'attractions' => [
                    ['Piazza del Duomo and Elephant Fountain', "Catania's symbol: the basalt elephant with its Egyptian obelisk at the centre of one of the finest baroque piazzas in Italy. The Cathedral of Sant'Agata completes the magnificent scene."],
                    ['Fish Market (Pescheria)', "Catania's fish market is a unique spectacle of colours, aromas and voices. One of the most famous historic markets in Italy, tucked into the lanes of the old city."],
                    ['Castello Ursino', "13th-century Norman fortress, now home to the Civic Museum. An imposing monument to Catania's medieval history, surrounded by the most picturesque quarters of the city."],
                    ['Via Etnea', "Catania's main boulevard points straight at the volcano. Shopping, gelaterias, historic cafés and — in the background — the imposing outline of Etna."],
                    ['Teatro Massimo Bellini', "One of the most beautiful opera houses in Italy, dedicated to Catanian composer Vincenzo Bellini. The frescoed foyer and 1,200-seat hall are an Art Nouveau masterpiece."],
                    ['Feast of Sant\'Agata', "Every February, one of Europe's largest religious processions: over a million devotees for three days of devotion, candlelight and fireworks around the silver fercolo."],
                ],
                'summer'   => "In summer Catania is the ideal base for Etna excursions and beaches. The old town nightlife, evening market and bars in the Borgo quarter offer authentic Sicilian flavours.",
                'winter'   => "In winter Catania keeps a mild climate and a full city life. The Christmas season and the Feast of Sant'Agata (February) are unmissable moments of celebration with the whole city in motion.",
                'distance' => '30 min from Almaretna',
                'dist_note'=> 'Motorway A18. Catania-Fontanarossa Airport: 30 min — gateway for international flights.',
            ],
            'de' => [
                'title'    => 'Catania',
                'tagline'  => 'Die Stadt des Elefanten, am Fuße des Ätna',
                'intro'    => "30 Minuten von Almaretna entfernt ist Catania eine lebhafte und authentische Stadt aus dunklem Lavagestein, UNESCO-Weltkulturerbe. Hauptstadt Ostsiziliens und internationaler Reise-Hub, birgt sie ein historisches Zentrum voller Barock-Schätze und Stadtleben.",
                'attractions' => [
                    ['Piazza del Duomo und Elefantenbrunnen', "Das Symbol Catanias: der Basaltelefant mit ägyptischem Obelisk im Mittelpunkt einer der schönsten Barockpiazzas Italiens. Der Sant'Agata-Dom schließt die Szene majestätisch ab."],
                    ['Fischmarkt (Pescheria)', "Der Fischmarkt Catanias ist ein einzigartiges Spektakel aus Farben, Düften und Stimmen. Einer der bekanntesten historischen Märkte Italiens, in den Gassen der Altstadt."],
                    ['Schloss Ursino', "Normannische Festung aus dem 13. Jahrhundert, heute Sitz des Stadtmuseums. Ein imposantes Zeugnis der mittelalterlichen Geschichte Catanias, umgeben von den malerischsten Vierteln der Stadt."],
                    ['Via Etnea', "Catanias Hauptstraße zeigt direkt auf den Vulkan. Shopping, Gelaterias, historische Cafés und — im Hintergrund — die imposante Silhouette des Ätna."],
                    ['Teatro Massimo Bellini', "Eines der schönsten Opernhäuser Italiens, dem Cataneser Komponisten Vincenzo Bellini gewidmet. Das bemalte Foyer und der 1.200-Plätze-Saal sind ein Jugendstil-Meisterwerk."],
                    ['Fest der Heiligen Agata', "Jedes Jahr im Februar: eine der größten religiösen Prozessionen Europas mit über einer Million Gläubiger für drei Tage Hingabe, Kerzenlicht und Feuerwerk."],
                ],
                'summer'   => "Im Sommer ist Catania die ideale Basis für Ätna-Ausflüge und Strände. Das Nachtleben der Altstadt und die Märkte bieten authentische sizilianische Aromen.",
                'winter'   => "Im Winter bleibt Catania mild und pulsierend. Die Weihnachtszeit und das Agata-Fest (Februar) sind unvergessliche Feste mit der ganzen Stadt in Bewegung.",
                'distance' => '30 Min. von Almaretna',
                'dist_note'=> 'Autobahn A18. Flughafen Catania: 30 Min. — Gateway für internationale Flüge.',
            ],
            'fr' => [
                'title'    => 'Catane',
                'tagline'  => 'La ville de l\'éléphant, au pied de l\'Etna',
                'intro'    => "À 30 minutes d'Almaretna, Catane est une ville vivante et authentique construite en pierre de lave sombre, classée au patrimoine UNESCO. Capitale de la Sicile orientale et hub de voyage international, elle cache un centre historique riche en trésors baroques.",
                'attractions' => [
                    ['Piazza del Duomo et Fontaine de l\'Éléphant', "Le symbole de Catane : l'éléphant de basalte avec l'obélisque égyptien au centre de l'une des plus belles places baroques d'Italie. La cathédrale de Sant'Agata complète magnifiquement la scène."],
                    ['Marché aux poissons (Pescheria)', "Le marché aux poissons de Catane est un spectacle unique de couleurs, d'arômes et de voix. L'un des marchés historiques les plus célèbres d'Italie, dans les ruelles du centre historique."],
                    ['Château Ursino', "Forteresse normande du XIIIe siècle, aujourd'hui Musée municipal. Imposant témoignage de l'histoire médiévale de Catane, entouré des quartiers les plus pittoresques."],
                    ['Via Etnea', "Le boulevard principal de Catane pointe droit vers le volcan. Shopping, glaciers, cafés historiques et — en arrière-plan — l'imposante silhouette de l'Etna."],
                    ['Teatro Massimo Bellini', "L'un des plus beaux opéras d'Italie, dédié au compositeur catanais Vincenzo Bellini. Le foyer fresqué et la salle de 1 200 places sont un chef-d'œuvre Art nouveau."],
                    ['Fête de Sainte Agathe', "Chaque année en février : l'une des plus grandes processions religieuses d'Europe avec plus d'un million de fidèles pendant trois jours de dévotion, de cierges et de feux d'artifice."],
                ],
                'summer'   => "En été Catane est la base idéale pour les excursions sur l'Etna et les plages. La vie nocturne du centre historique et les marchés offrent des saveurs siciliennes authentiques.",
                'winter'   => "En hiver Catane reste douce et animée. La période de Noël et la Fête de Sainte Agathe (février) sont des moments festifs inoubliables.",
                'distance' => '30 min d\'Almaretna',
                'dist_note'=> 'Autoroute A18. Aéroport de Catane : 30 min — hub pour vols internationaux.',
            ],
            'es' => [
                'title'    => 'Catania',
                'tagline'  => 'La ciudad del elefante, al pie del Etna',
                'intro'    => "A 30 minutos de Almaretna, Catania es una ciudad vibrante y auténtica construida en piedra de lava oscura, Patrimonio de la Humanidad UNESCO. Capital de Sicilia oriental y hub de viajes internacional, esconde un centro histórico lleno de tesoros barrocos.",
                'attractions' => [
                    ['Plaza del Duomo y Fuente del Elefante', "El símbolo de Catania: el elefante de basalto con el obelisco egipcio en el centro de una de las plazas barrocas más bellas de Italia. La Catedral de Sant'Agata cierra magníficamente la escena."],
                    ['Mercado del Pescado (Pescheria)', "El mercado de pescado de Catania es un espectáculo único de colores, aromas y voces. Uno de los mercados históricos más famosos de Italia, entre los callejones del casco antiguo."],
                    ['Castillo Ursino', "Fortaleza normanda del siglo XIII, hoy sede del Museo Municipal. Imponente testimonio de la historia medieval de Catania, rodeado de los barrios más pintorescos."],
                    ['Via Etnea', "El bulevar principal de Catania apunta directamente hacia el volcán. Tiendas, heladerías, cafés históricos y — al fondo — la imponente silueta del Etna."],
                    ['Teatro Massimo Bellini', "Uno de los teatros de ópera más bellos de Italia, dedicado al compositor cataniense Vincenzo Bellini. El foyer con frescos y la sala de 1.200 butacas son una obra maestra del modernismo."],
                    ['Fiesta de Santa Ágata', "Cada año en febrero: una de las procesiones religiosas más grandes de Europa con más de un millón de fieles durante tres días de devoción, velas y fuegos artificiales."],
                ],
                'summer'   => "En verano Catania es la base ideal para excursiones al Etna y playas. La vida nocturna del casco antiguo y los mercados ofrecen auténticos sabores sicilianos.",
                'winter'   => "En invierno Catania mantiene un clima suave y una vida ciudadana plena. Las fiestas navideñas y la Fiesta de Santa Ágata (febrero) son momentos festivos imperdibles.",
                'distance' => '30 min desde Almaretna',
                'dist_note'=> 'Autopista A18. Aeropuerto de Catania: 30 min — hub para vuelos internacionales.',
            ],
        ],

        /* ──────────── SIRACUSA ──────────── */
        'siracusa' => [
            'it' => [
                'title'    => 'Siracusa',
                'tagline'  => 'Duemila anni di storia sul mare',
                'intro'    => "A circa 90 minuti da Almaretna, Siracusa è forse la città più affascinante della Sicilia. Fondata dai Greci nel 734 a.C., fu per secoli una delle più potenti città del Mediterraneo. Oggi è Patrimonio UNESCO e meta di turismo culturale di alto livello.",
                'attractions' => [
                    ['Ortigia', "L'isola storica di Siracusa, collegata da ponti. Un labirinto di vicoli barocchi, piazze animate, chiese e palazzi che scendono direttamente sul mare. Il centro pulsante della città."],
                    ['Fontana di Aretusa', "Una delle fonti d'acqua dolce più celebri della storia: sorgente naturale a pochi metri dal mare, con papiri egizi spontanei e una leggenda mitologica millenaria."],
                    ['Teatro Greco', "Uno dei teatri greci più grandi del mondo antico, scavato nella roccia nel V sec. a.C. In estate ospita la stagione teatrale dell'INDA con tragedie greche classiche."],
                    ['Museo Archeologico Paolo Orsi', "Uno dei più importanti musei di antichità greche e romane al mondo. La collezione è straordinaria per quantità e rarità dei reperti."],
                    ['Noto', "A 40 km da Siracusa: il gioiello del barocco siciliano, ricostruita nel '700 dopo il terremoto del 1693. Patrimonio UNESCO e una delle città barocche più belle del mondo."],
                    ['Il mare di Siracusa', "Le spiagge del Plemmirio e la Riserva Naturale Oasi di Vendicari hanno acque tra le più cristalline d'Italia. Un paradiso per i sub e per chi ama il mare pulito e autentico."],
                ],
                'summer'   => "In estate Siracusa è al top: teatro greco, spiagge meravigliose, mercati serali in Ortigia, aperitivi con vista sul mare. Un'esperienza completa tra cultura e relax.",
                'winter'   => "Siracusa d'inverno è straordinariamente affascinante: clima mite (15-18°C), assenza di folle e una luce dorata sui palazzi barocchi. Un'atmosfera unica, lontana dall'overtourism.",
                'distance' => '~90 min da Almaretna',
                'dist_note'=> 'Consigliamo una giornata intera o anche due notti per vivere appieno Ortigia e dintorni.',
                'sections' => [
                    [
                        'title' => 'La città più antica della Sicilia',
                        'text'  => "Siracusa è stata fondata nel 734 a.C. da coloni corinzi che sbarcarono sull'isolotto di Ortigia e vi trovarono una sorgente d'acqua dolce — la mitica Aretusa — a pochi passi dal mare. Nel giro di due secoli divenne la città più potente della Magna Grecia, rivale di Atene e Cartagine. Il suo Teatro Greco, scavato nella roccia nel V secolo a.C., aveva una capienza di 15.000 spettatori. La Latomia del Paradiso — cava di pietra usata poi come prigione — ospita la grotta chiamata Orecchio di Dionisio, la cui acustica straordinaria ha alimentato leggende per secoli. Visitare Siracusa è fare un salto in una delle città più importanti del mondo antico.",
                    ],
                    [
                        'title' => 'Ortigia: un labirinto a misura d\'uomo',
                        'text'  => "L'isola di Ortigia è il cuore di Siracusa: un quadrilatero di vicoli barocchi dove ci si perde con piacere. La Cattedrale, costruita inglobando le colonne del Tempio di Atena del V secolo a.C., è uno degli edifici più straordinari di tutto il Mediterraneo — un palinsesto di architetture che racconta duemila anni di storia in un solo edificio. Tutt'intorno si aprono trattorie con vista mare, mercatini di ceramiche e spezie, bar sulla riva dove prendere un caffè di fronte alla baia. Ortigia è piccola — si attraversa a piedi in 20 minuti — ma densa e sorprendente, capace di stupire ad ogni angolo.",
                    ],
                    [
                        'title' => 'Consigli per godersi Siracusa al meglio',
                        'text'  => "La distanza da Almaretna (~90 minuti) rende Siracusa ideale per un'escursione di almeno una giornata intera, o meglio ancora una notte. Il periodo migliore è aprile-giugno e settembre-ottobre: meno caldo, meno affollata, luce dorata. In estate la stagione teatrale dell'INDA al Teatro Greco (maggio-luglio) è un'esperienza unica da non perdere — i biglietti vanno prenotati con settimane d'anticipo. A 40 km da Siracusa c'è Noto, la città barocca più bella del mondo: vale la deviazione. Le spiagge di Vendicari e Plemmirio, a pochi chilometri, sono tra le più belle d'Italia.",
                    ],
                ],
            ],
            'en' => [
                'title'    => 'Syracuse',
                'tagline'  => 'Two thousand years of history by the sea',
                'intro'    => "About 90 minutes from Almaretna, Syracuse is perhaps the most fascinating city in Sicily. Founded by the Greeks in 734 BC, it was for centuries one of the most powerful cities in the Mediterranean. Today it is a UNESCO World Heritage Site.",
                'attractions' => [
                    ['Ortigia', "Syracuse's historic island, connected by bridges. A labyrinth of baroque lanes, lively piazzas, churches and palaces descending directly to the sea. The beating heart of the city."],
                    ['Arethusa Fountain', "One of the most celebrated freshwater springs in history: a natural source just metres from the sea, with spontaneous Egyptian papyrus plants and an ancient mythological legend."],
                    ['Greek Theatre', "One of the largest Greek theatres of the ancient world, carved from rock in the 5th century BC. In summer it hosts the INDA classical theatre season with Greek tragedies."],
                    ['Archaeological Museum Paolo Orsi', "One of the world's most important Greek and Roman antiquities museums. The collection is extraordinary in quantity and rarity."],
                    ['Noto', "40 km from Syracuse: the jewel of Sicilian baroque, rebuilt in the 18th century after the 1693 earthquake. UNESCO heritage and one of the most beautiful baroque cities in the world."],
                    ['The sea of Syracuse', "The beaches at Plemmirio and the Vendicari Nature Reserve have some of the clearest water in Italy. A paradise for divers and lovers of unspoilt sea."],
                ],
                'summer'   => "In summer Syracuse is at its best: Greek theatre, wonderful beaches, evening markets in Ortigia, aperitifs with sea views. A complete experience of culture and relaxation.",
                'winter'   => "Syracuse in winter is extraordinarily charming: mild climate (15-18°C), no crowds and golden light on the baroque palaces. A unique atmosphere, far from overtourism.",
                'distance' => '~90 min from Almaretna',
                'dist_note'=> 'We recommend a full day or even two nights to fully experience Ortigia and surroundings.',
            ],
            'de' => [
                'title'    => 'Syrakus',
                'tagline'  => 'Zweitausend Jahre Geschichte am Meer',
                'intro'    => "Etwa 90 Minuten von Almaretna entfernt ist Syrakus vielleicht die faszinierendste Stadt Siziliens. Von den Griechen 734 v. Chr. gegründet, war sie jahrhundertelang eine der mächtigsten Städte des Mittelmeers. Heute UNESCO-Weltkulturerbe.",
                'attractions' => [
                    ['Ortigia', "Syrakus' historische Insel, durch Brücken verbunden. Ein Labyrinth barocker Gassen, lebhafter Piazzas, Kirchen und Paläste, die direkt auf das Meer hinabsteigen."],
                    ['Arethusa-Brunnen', "Eine der bekanntesten Süßwasserquellen der Geschichte: eine natürliche Quelle wenige Meter vom Meer entfernt, mit spontanem ägyptischen Papyrus und einer jahrtausendealten Legende."],
                    ['Griechisches Theater', "Eines der größten griechischen Theater der Antike, im 5. Jh. v. Chr. aus dem Fels gehauen. Im Sommer beherbergt es die INDA-Theatersaison mit klassischen Tragödien."],
                    ['Archäologisches Museum Paolo Orsi', "Eines der bedeutendsten Museen für griechische und römische Altertümer weltweit. Die Sammlung ist außergewöhnlich in Umfang und Seltenheit."],
                    ['Noto', "40 km von Syrakus: das Juwel des sizilianischen Barocks, nach dem Erdbeben von 1693 im 18. Jahrhundert neu aufgebaut. UNESCO-Welterbe und eine der schönsten Barockstädte der Welt."],
                    ['Das Meer von Syrakus', "Die Strände am Plemmirio und das Naturreservat Vendicari haben einige der klarsten Gewässer Italiens. Ein Paradies für Taucher und Liebhaber unberührter Meere."],
                ],
                'summer'   => "Im Sommer ist Syrakus auf dem Höhepunkt: Griechisches Theater, herrliche Strände, Abendmärkte in Ortigia, Aperitifs mit Meerblick. Kultur und Entspannung in einem.",
                'winter'   => "Syrakus im Winter ist außerordentlich reizvoll: mildes Klima (15-18°C), keine Massen und goldenes Licht auf den Barockpalästen. Eine einzigartige Atmosphäre, fern vom Overtourism.",
                'distance' => '~90 Min. von Almaretna',
                'dist_note'=> 'Wir empfehlen einen ganzen Tag oder sogar zwei Nächte, um Ortigia und Umgebung vollständig zu erleben.',
            ],
            'fr' => [
                'title'    => 'Syracuse',
                'tagline'  => 'Deux mille ans d\'histoire en mer',
                'intro'    => "À environ 90 minutes d'Almaretna, Syracuse est peut-être la ville la plus fascinante de Sicile. Fondée par les Grecs en 734 av. J.-C., elle fut pendant des siècles l'une des villes les plus puissantes de la Méditerranée. Aujourd'hui classée au patrimoine UNESCO.",
                'attractions' => [
                    ['Ortygie', "L'île historique de Syracuse, reliée par des ponts. Un labyrinthe de ruelles baroques, de places animées, d'églises et de palais qui descendent directement sur la mer."],
                    ['Fontaine d\'Aréthuse', "L'une des sources d'eau douce les plus célèbres de l'histoire : une source naturelle à quelques mètres de la mer, avec des papyrus égyptiens spontanés et une légende mythologique millénaire."],
                    ['Théâtre Grec', "L'un des plus grands théâtres grecs du monde antique, creusé dans le roc au Ve siècle av. J.-C. En été il accueille la saison théâtrale de l'INDA avec des tragédies grecques classiques."],
                    ['Musée Archéologique Paolo Orsi', "L'un des plus importants musées d'antiquités grecques et romaines au monde. La collection est extraordinaire par sa quantité et sa rareté."],
                    ['Noto', "À 40 km de Syracuse : le joyau du baroque sicilien, rebâtie au XVIIIe siècle après le tremblement de terre de 1693. Patrimoine UNESCO et l'une des plus belles villes baroques du monde."],
                    ['La mer de Syracuse', "Les plages du Plemmirio et la Réserve Naturelle de Vendicari ont parmi les eaux les plus cristallines d'Italie. Un paradis pour les plongeurs et les amateurs de mer vierge."],
                ],
                'summer'   => "En été Syracuse est au sommet : théâtre grec, merveilleuses plages, marchés du soir à Ortygie, apéritifs avec vue sur la mer. Culture et détente réunies.",
                'winter'   => "Syracuse en hiver est extraordinairement séduisante : climat doux (15-18°C), absence de foules et lumière dorée sur les palais baroques. Une atmosphère unique, loin du surtourisme.",
                'distance' => '~90 min d\'Almaretna',
                'dist_note'=> 'Nous recommandons une journée entière ou même deux nuits pour profiter pleinement d\'Ortygie.',
            ],
            'es' => [
                'title'    => 'Siracusa',
                'tagline'  => 'Dos mil años de historia junto al mar',
                'intro'    => "A unos 90 minutos de Almaretna, Siracusa es quizás la ciudad más fascinante de Sicilia. Fundada por los griegos en el 734 a.C., fue durante siglos una de las ciudades más poderosas del Mediterráneo. Hoy es Patrimonio de la Humanidad UNESCO.",
                'attractions' => [
                    ['Ortigia', "La isla histórica de Siracusa, unida por puentes. Un laberinto de callejones barrocos, plazas animadas, iglesias y palacios que descienden directamente al mar."],
                    ['Fuente de Aretusa', "Una de las fuentes de agua dulce más célebres de la historia: manantial natural a pocos metros del mar, con papiros egipcios espontáneos y una leyenda mitológica milenaria."],
                    ['Teatro Griego', "Uno de los teatros griegos más grandes del mundo antiguo, excavado en la roca en el siglo V a.C. En verano acoge la temporada teatral del INDA con tragedias griegas clásicas."],
                    ['Museo Arqueológico Paolo Orsi', "Uno de los museos de antigüedades griegas y romanas más importantes del mundo. La colección es extraordinaria en cantidad y rareza."],
                    ['Noto', "A 40 km de Siracusa: la joya del barroco siciliano, reconstruida en el siglo XVIII tras el terremoto de 1693. Patrimonio UNESCO y una de las ciudades barrocas más bellas del mundo."],
                    ['El mar de Siracusa', "Las playas de Plemmirio y la Reserva Natural de Vendicari tienen algunas de las aguas más cristalinas de Italia. Un paraíso para submarinistas y amantes del mar virgen."],
                ],
                'summer'   => "En verano Siracusa está en su mejor momento: teatro griego, playas maravillosas, mercados nocturnos en Ortigia, aperitivos con vistas al mar. Cultura y relax en uno.",
                'winter'   => "Siracusa en invierno es extraordinariamente encantadora: clima suave (15-18°C), sin multitudes y luz dorada sobre los palacios barrocos. Una atmósfera única, lejos del turismo masivo.",
                'distance' => '~90 min desde Almaretna',
                'dist_note'=> 'Recomendamos un día completo o incluso dos noches para disfrutar plenamente de Ortigia.',
            ],
        ],

        /* ──────────── MESSINA ──────────── */
        'messina' => [
            'it' => [
                'title'    => 'Messina',
                'tagline'  => 'Porta della Sicilia sullo Stretto',
                'intro'    => "A circa 50 minuti da Almaretna verso nord, Messina è la città che guarda la Calabria attraverso lo Stretto più famoso d'Italia. Ricca di storia, tradizioni e una posizione geografica unica tra i Peloritani e il Tirreno.",
                'attractions' => [
                    ['Orologio Astronomico del Duomo', "Uno degli orologi meccanici più grandi e complessi al mondo. Ogni giorno a mezzogiorno le figure animate si mettono in moto per uno spettacolo meccanico unico."],
                    ['Duomo di Messina', "Cattedrale normanna ricostruita dopo il terremoto del 1908. Il mosaico della Madonna della Lettera e il campanile con l'orologio astronomico sono i punti di forza."],
                    ['Santuario di Montalto', "Il santuario più venerato di Messina, su un colle sopra la città. Dal piazzale si abbraccia con lo sguardo lo Stretto, la Calabria e i Monti Peloritani."],
                    ['Ganzirri', "Il piccolo borgo lacustre a nord di Messina, famoso per le cozze e i mitili allevati nelle lagune. I ristoranti sul lungolago sono la sosta gastronomica perfetta."],
                    ['I Giganti di Messina', "Mata e Grifone, i giganti della tradizione messinese, sono il simbolo folkloristico più amato della città. Le loro leggende e processioni fanno parte dell'identità culturale di Messina."],
                    ['Stretto di Messina', "Lo storico braccio di mare tra Sicilia e Calabria: correnti potenti, miti, il Faro, la punta dello Stivale. Uno degli scenari più carichi di storia e suggestione del Mediterraneo."],
                ],
                'summer'   => "In estate lo Stretto è animatissimo: traghetti, vento fresco, escursioni in barca e la gita in Calabria in meno di 30 minuti. Le cozze fresche di Ganzirri sono un classico estivo.",
                'winter'   => "In inverno Messina ha un fascino particolare: le tradizioni natalizie siciliane, i presepi viventi e un clima comunque mite la rendono una meta piacevole tutto l'anno.",
                'distance' => '~50 min da Almaretna',
                'dist_note'=> 'Autostrada A18 nord. In estate consigliamo di partire la mattina presto.',
                'sections' => [
                    [
                        'title' => 'La città che rinasce sempre',
                        'text'  => "Messina ha una storia straziante e straordinaria: il terremoto del 28 dicembre 1908 — uno dei più distruttivi mai registrati in Europa — rase al suolo l'intera città in 37 secondi, uccidendo tra 75.000 e 200.000 persone. Quello che vediamo oggi è una città interamente ricostruita nel Novecento, con una pianta razionale e ampi viali. Eppure Messina ha mantenuto la sua anima: il Duomo normanno ricostruito, l'Orologio Astronomico più grande al mondo, le tradizioni dei Giganti Mata e Grifone che ogni anno sfilano per le strade. Una città resiliente, che ha trasformato la tragedia in identità.",
                    ],
                    [
                        'title' => 'Lo Stretto, le Eolie e la Calabria',
                        'text'  => "La posizione di Messina è unica: si trova sul punto più stretto del Mediterraneo occidentale, dove Sicilia e Calabria distano appena 3 km. Da Messina i traghetti per Villa San Giovanni partono ogni 20 minuti — una gita in Calabria è questione di un'ora. Dal porto si organizzano anche escursioni alle Isole Eolie (Lipari, Vulcano, Stromboli) in giornata: un'esperienza indimenticabile con il vulcano di Stromboli attivo che di notte illumina il mare. Lo Stretto è anche un fenomeno naturale: le correnti, il vortice di Cariddi (il mitico Scilla e Cariddi di Omero), gli avvistamenti di delfini e capodogli.",
                    ],
                    [
                        'title' => 'Ganzirri e i sapori dello Stretto',
                        'text'  => "A nord di Messina, il piccolo borgo lacustre di Ganzirri è il posto dove i messinesi vanno a mangiare le cozze. Le lagune di Ganzirri e di Faro producono mitili allevati in acque pulitissime: le trattorie sul lungolaguna le servono crude, gratinate, con pasta o in zuppe fumanti. È qui che si capisce la cucina dello Stretto — più semplice e marinara di quella palermitana, con il pesce azzurro protagonista. Il tramonto sul Faro di Messina, con la Calabria illuminata dall'altra parte, è uno di quei paesaggi che restano impressi.",
                    ],
                ],
            ],
            'en' => [
                'title'    => 'Messina',
                'tagline'  => 'Gateway to Sicily across the Strait',
                'intro'    => "About 50 minutes north of Almaretna, Messina is the city that looks across to Calabria over Italy's most famous strait. Rich in history, traditions and a unique geographical position between the Peloritani mountains and the Tyrrhenian Sea.",
                'attractions' => [
                    ['Cathedral Astronomical Clock', "One of the largest and most complex mechanical clocks in the world. Every day at noon, animated figures come to life in a unique mechanical show."],
                    ['Messina Cathedral', "Norman cathedral rebuilt after the 1908 earthquake. The mosaic of the Madonna della Lettera and the bell tower with the astronomical clock are the highlights."],
                    ['Sanctuary of Montalto', "Messina's most revered sanctuary, on a hill above the city. From the square, the view embraces the Strait, Calabria and the Peloritani mountains."],
                    ['Ganzirri', "The small lakeside village north of Messina, famous for mussels and shellfish farmed in the lagoons. The restaurants along the waterfront are the perfect gastronomic stop."],
                    ['The Giants of Messina', "Mata and Grifone, Messina's traditional giants, are the city's most beloved folkloric symbols. Their legends and processions are central to Messina's cultural identity."],
                    ['Strait of Messina', "The historic stretch of sea between Sicily and Calabria: powerful currents, myths, the lighthouse, the tip of the Boot. One of the most historically charged and evocative seascapes in the Mediterranean."],
                ],
                'summer'   => "In summer the Strait is buzzing: ferries, fresh breezes, boat trips and a day in Calabria in under 30 minutes. Freshly farmed mussels from Ganzirri are a classic summer treat.",
                'winter'   => "In winter Messina has a particular charm: Sicilian Christmas traditions, nativity scenes and a consistently mild climate make it a pleasant destination year-round.",
                'distance' => '~50 min from Almaretna',
                'dist_note'=> 'Motorway A18 north. In summer we recommend an early morning start.',
            ],
            'de' => [
                'title'    => 'Messina',
                'tagline'  => 'Tor zur Meerenge von Messina',
                'intro'    => "Etwa 50 Minuten nördlich von Almaretna blickt Messina über die bekannteste Meerenge Italiens nach Kalabrien. Reich an Geschichte, Traditionen und einer einzigartigen geographischen Lage zwischen den Peloritanischen Bergen und dem Tyrrhenischen Meer.",
                'attractions' => [
                    ['Astronomische Uhr des Doms', "Eine der größten und komplexesten Uhren der Welt. Jeden Tag um Mittag erwachen Figuren zum Leben in einer einzigartigen mechanischen Show."],
                    ['Dom von Messina', "Normannische Kathedrale, nach dem Erdbeben von 1908 wieder aufgebaut. Das Mosaik der Madonna della Lettera und der Glockenturm mit der astronomischen Uhr sind die Highlights."],
                    ['Heiligtum von Montalto', "Das verehrteste Heiligtum Messinas auf einem Hügel über der Stadt. Vom Platz aus umfasst der Blick die Meerenge, Kalabrien und die Peloritanischen Berge."],
                    ['Ganzirri', "Das kleine Seeufer-Dorf nördlich von Messina, berühmt für Muscheln aus den Lagunen. Die Restaurants am Ufer sind der perfekte gastronomische Halt."],
                    ['Die Riesen von Messina', "Mata und Grifone, die traditionellen Riesen Messinas, sind das beliebteste Volkssymbol der Stadt. Ihre Legenden und Prozessionen gehören zur kulturellen Identität."],
                    ['Meerenge von Messina', "Das historische Seegebiet zwischen Sizilien und Kalabrien: starke Strömungen, Mythen, der Leuchtturm. Eine der geschichtsreichsten Meereslandschaften des Mittelmeers."],
                ],
                'summer'   => "Im Sommer herrscht an der Meerenge reges Treiben: Fähren, frische Brise, Bootstouren und ein Tagesausflug nach Kalabrien in unter 30 Minuten. Frische Muscheln aus Ganzirri sind ein Sommerklassiker.",
                'winter'   => "Im Winter hat Messina einen besonderen Charme: sizilianische Weihnachtstraditionen und ein stets mildes Klima machen es das ganze Jahr zu einem angenehmen Ausflugsziel.",
                'distance' => '~50 Min. von Almaretna',
                'dist_note'=> 'Autobahn A18 Nord. Im Sommer empfehlen wir eine frühe Abfahrt.',
            ],
            'fr' => [
                'title'    => 'Messine',
                'tagline'  => 'Porte de la Sicile sur le Détroit',
                'intro'    => "À environ 50 minutes au nord d'Almaretna, Messine regarde la Calabre à travers le détroit le plus célèbre d'Italie. Riche d'histoire, de traditions et d'une position géographique unique entre les monts Péloritains et la mer Tyrrhénienne.",
                'attractions' => [
                    ['Horloge astronomique de la cathédrale', "L'une des horloges mécaniques les plus grandes et complexes du monde. Chaque jour à midi, des figurines animées s'animent dans un spectacle mécanique unique."],
                    ['Cathédrale de Messine', "Cathédrale normande reconstruite après le tremblement de terre de 1908. La mosaïque de la Madonna della Lettera et le campanile avec l'horloge astronomique en sont les points forts."],
                    ['Sanctuaire de Montalto', "Le sanctuaire le plus vénéré de Messine, sur une colline au-dessus de la ville. Depuis la place, le regard embrasse le Détroit, la Calabre et les monts Péloritains."],
                    ['Ganzirri', "Le petit village lacustre au nord de Messine, célèbre pour les moules et les coquillages des lagunes. Les restaurants au bord de l'eau sont l'étape gastronomique idéale."],
                    ['Les Géants de Messine', "Mata et Grifone, les géants traditionnels de Messine, sont le symbole folklorique le plus aimé de la ville. Leurs légendes et processions font partie de l'identité culturelle."],
                    ['Détroit de Messine', "Le bras de mer historique entre la Sicile et la Calabre : courants puissants, mythes, le phare. L'un des paysages maritimes les plus chargés d'histoire de la Méditerranée."],
                ],
                'summer'   => "En été le Détroit est très animé : ferrys, brise fraîche, excursions en bateau et une journée en Calabre en moins de 30 minutes. Les moules fraîches de Ganzirri sont un classique estival.",
                'winter'   => "En hiver Messine a un charme particulier : traditions de Noël siciliennes et un climat toujours doux en font une destination agréable toute l'année.",
                'distance' => '~50 min d\'Almaretna',
                'dist_note'=> 'Autoroute A18 nord. En été, nous recommandons un départ tôt le matin.',
            ],
            'es' => [
                'title'    => 'Mesina',
                'tagline'  => 'Puerta de Sicilia en el Estrecho',
                'intro'    => "A unos 50 minutos al norte de Almaretna, Mesina mira hacia Calabria a través del estrecho más famoso de Italia. Rica en historia, tradiciones y una posición geográfica única entre los montes Peloritanos y el mar Tirreno.",
                'attractions' => [
                    ['Reloj Astronómico de la Catedral', "Uno de los relojes mecánicos más grandes y complejos del mundo. Cada día a mediodía, figuras animadas cobran vida en un espectáculo mecánico único."],
                    ['Catedral de Mesina', "Catedral normanda reconstruida tras el terremoto de 1908. El mosaico de la Madonna della Lettera y el campanario con el reloj astronómico son los puntos fuertes."],
                    ['Santuario de Montalto', "El santuario más venerado de Mesina, en una colina sobre la ciudad. Desde la plaza, la vista abarca el Estrecho, Calabria y los montes Peloritanos."],
                    ['Ganzirri', "El pequeño pueblo lacustre al norte de Mesina, famoso por los mejillones y mariscos de las lagunas. Los restaurantes junto al lago son la parada gastronómica perfecta."],
                    ['Los Gigantes de Mesina', "Mata y Grifone, los gigantes tradicionales de Mesina, son el símbolo folclórico más querido de la ciudad. Sus leyendas y procesiones forman parte de la identidad cultural."],
                    ['Estrecho de Mesina', "El histórico brazo de mar entre Sicilia y Calabria: fuertes corrientes, mitos, el faro. Uno de los paisajes marinos más cargados de historia del Mediterráneo."],
                ],
                'summer'   => "En verano el Estrecho está muy animado: ferris, brisa fresca, excursiones en barco y un día en Calabria en menos de 30 minutos. Los mejillones frescos de Ganzirri son un clásico veraniego.",
                'winter'   => "En invierno Mesina tiene un encanto especial: tradiciones navideñas sicilianas y un clima siempre suave la convierten en un destino agradable todo el año.",
                'distance' => '~50 min desde Almaretna',
                'dist_note'=> 'Autopista A18 norte. En verano recomendamos salir temprano por la mañana.',
            ],
        ],

        /* ──────────── MARE DI SICILIA ──────────── */
        'mare-di-sicilia' => [
            'it' => [
                'title'    => 'Il mare di Sicilia',
                'tagline'  => 'La costa ionica ai tuoi piedi',
                'intro'    => "Almaretna è a soli 10 minuti dal mare. Non un mare qualsiasi: la costa ionica della Sicilia orientale offre acque cristalline, borghi di pescatori autentici e spiagge di ogni tipo — dalla sabbia fine della riviera di Mascali alle calette di Taormina.",
                'attractions' => [
                    ['Fondachello — Mascali', "La spiaggia di casa di Almaretna: a 10 minuti in auto, un borgo marinaro autentico con stabilimenti balneari, ristoranti di pesce fresco e un'atmosfera familiare. Il lungomare di Fondachello al tramonto è uno spettacolo da non perdere."],
                    ['Torre Archirafi', "Il caratteristico borgo marinaro del Comune di Riposto: il lungomare storico, i moli dei pescatori e i locali sul mare. Un luogo genuino e poco turistico, dove gustare pesce fresco e vivere la vera atmosfera della costa ionica siciliana."],
                    ['Giardini Naxos', "La baia più frequentata sotto Taormina: sabbia fine, acque blu, un lungomare ricco di ristoranti e stabilimenti. A pochi passi dalla magia di Taormina, è la spiaggia perfetta per chi vuole mare e comodità."],
                    ['Isola Bella — Taormina', "La piccola isola naturale protetta collegata da un istmo sabbioso. Acque da cartolina, snorkeling eccezionale e lo scenario unico con Taormina sopra e l'Etna sullo sfondo."],
                    ['Le spiagge di Siracusa e Vendicari', "Più a sud, la Riserva Naturale Oasi di Vendicari e le spiagge del Plemmirio: fondali bassi, sabbia chiara e acque turchesi tra le più belle d'Italia — quasi caraibiche."],
                ],
                'summer'   => "D'estate la costa ionica è al massimo: il mare è caldo e limpido, i borghi si animano, i mercati serali sul lungomare sono uno spettacolo. Da Almaretna puoi raggiungere una spiaggia diversa ogni giorno.",
                'winter'   => "Anche in inverno il mare di Sicilia ha il suo fascino: le passeggiate sul lungomare deserto, i ristoranti di pesce frequentati dai locali, la luce bassa che dipinge il mare di oro. Non per nuotare, ma per ammirare.",
                'distance' => '10 min (Fondachello)',
                'dist_note'=> 'Torre Archirafi 15 min · Giardini Naxos 20 min · Taormina/Isola Bella 30 min.',
                'sections' => [
                    [
                        'title' => 'Un mare ionico, non un mare qualunque',
                        'text'  => "Il mare che bagna Almaretna è il Mar Ionio: acque profonde, temperature che salgono a 28°C in agosto, fondali rocciosi alternati a sabbia fine e una trasparenza che in certi giorni permette di vedere il fondo a 10 metri di profondità. La corrente circolare dello Ionio mantiene le acque pulite e ossigenate, creando condizioni ideali per lo snorkeling e le immersioni. La costa ionica siciliana non ha le acque piatte del Tirreno: c'è il profumo del largo, le onde che arrivano dalla Grecia, una luce che in certi pomeriggi di settembre trasforma il mare in un quadro d'oro.",
                    ],
                    [
                        'title' => 'Da Fondachello a Isola Bella: le spiagge vicine',
                        'text'  => "Fondachello — a 10 minuti da Almaretna — è la spiaggia di tutti i giorni: stabilimenti balneari con ombrelloni, lettini, docce e bar, ma anche tratti liberi con sabbia grigia vulcanica. Torre Archirafi (15 min), Praiola di Riposto (20 min) e Giardini Naxos (20 min) sono le tappe di una piccola vacanza balneare che non richiede ore di auto. Salendo verso Taormina, l'Isola Bella (30 min) offre l'esperienza marina più fotogenica della costa: acque smeraldo, il profilo dell'Etna sullo sfondo, il piccolo istmo che all'alba è ancora deserto.",
                    ],
                    [
                        'title' => 'Quando e come vivere il mare ionioco',
                        'text'  => "La stagione balneare sul Mar Ionio va da maggio a ottobre. Giugno e settembre sono i mesi perfetti: il mare è già caldo (24-26°C), le spiagge non sono affollate come ad agosto e le giornate lunghe permettono di combinare mare e visite culturali. Per chi vuole il massimo del relax e del silenzio, le prime ore del mattino sulle spiagge libere di Fondachello o Torre Archirafi sono un'esperienza di pace assoluta. I migliori ristoranti di pesce della costa sono a Torre Archirafi e Riposto: pesce di giornata, crudo misto e zuppa di cozze preparati come li facevano i nonni.",
                    ],
                ],
            ],
            'en' => [
                'title'    => 'The Sicilian sea',
                'tagline'  => 'The Ionian coast at your feet',
                'intro'    => "Almaretna is just 10 minutes from the sea. Not just any sea: the Ionian coast of eastern Sicily offers crystal-clear waters, authentic fishing villages and beaches of every kind — from the fine sand of the Mascali riviera to the coves of Taormina.",
                'attractions' => [
                    ['Fondachello — Mascali', "Almaretna's home beach: 10 minutes by car, an authentic seaside village with beach clubs, fresh fish restaurants and a family atmosphere. The Fondachello seafront promenade at sunset is a must-see."],
                    ['Torre Archirafi', "The characteristic fishing village of the municipality of Riposto: the historic seafront, fishermen's piers and seaside bars. A genuine, low-key spot to enjoy fresh fish and the true atmosphere of the Sicilian Ionian coast."],
                    ['Giardini Naxos', "The most popular bay below Taormina: fine sand, blue waters, a seafront lined with restaurants and beach clubs. Steps from the magic of Taormina, perfect for those who want plenty of sea and convenience."],
                    ['Isola Bella — Taormina', "The small protected natural island connected by a sandy isthmus. Picture-postcard waters, exceptional snorkelling and the unique setting with Taormina above and Etna behind."],
                    ['Syracuse beaches and Vendicari', "Further south, the Vendicari Nature Reserve and Plemmirio beaches: shallow crystal water, white sand — some of the clearest sea in Italy, almost Caribbean in feel."],
                ],
                'summer'   => "In summer the Ionian coast is at its best: warm and clear sea, lively villages, evening seafront markets. From Almaretna you can reach a different beach every day of your stay.",
                'winter'   => "In winter the Sicilian sea has its own magic: deserted seafront promenades, fish restaurants filled with locals, low golden light over the water. Not for swimming, but for wonder.",
                'distance' => '10 min (Fondachello)',
                'dist_note'=> 'Torre Archirafi 15 min · Giardini Naxos 20 min · Taormina/Isola Bella 30 min.',
            ],
            'de' => [
                'title'    => 'Das Sizilianische Meer',
                'tagline'  => 'Die Ionische Küste zu Ihren Füßen',
                'intro'    => "Almaretna ist nur 10 Minuten vom Meer entfernt. Nicht irgendein Meer: die Ionische Küste Ostsiziliens bietet kristallklares Wasser, authentische Fischerdörfer und Strände jeder Art.",
                'attractions' => [
                    ['Fondachello — Mascali', "Almaretnas Hausstrand: 10 Minuten mit dem Auto, ein authentisches Fischerdorf mit Strandclubs, frischen Fischrestaurants und familiärer Atmosphäre. Die Uferpromenade von Fondachello bei Sonnenuntergang ist ein Muss."],
                    ['Torre Archirafi', "Das charakteristische Fischerdorf der Gemeinde Riposto: die historische Uferpromenade, Fischerstege und Lokale am Meer. Ein genuiner, wenig touristischer Ort für frischen Fisch und echte ionische Atmosphäre."],
                    ['Giardini Naxos', "Die beliebteste Bucht unterhalb von Taormina: Feinsand, blaues Wasser, eine Uferpromenade voller Restaurants und Strandclubs."],
                    ['Isola Bella — Taormina', "Die kleine geschützte Naturinsel, verbunden durch einen Sandstreifen. Bilderbuch-Wasser, ausgezeichnetes Schnorcheln und das einzigartige Panorama mit Taormina oben und dem Ätna im Hintergrund."],
                    ['Strände von Syrakus und Vendicari', "Weiter südlich: das Naturreservat Vendicari und die Strände des Plemmirio. Flaches, kristallklares Wasser und weißer Sand — zu den schönsten Meeresstellen Italiens."],
                ],
                'summer'   => "Im Sommer ist die ionische Küste auf dem Höhepunkt: warmes, klares Meer, belebte Dörfer, Abendmärkte am Ufer. Von Almaretna aus erreichen Sie täglich einen anderen Strand.",
                'winter'   => "Auch im Winter hat das sizilianische Meer seinen Reiz: verlassene Uferspaziergänge, Fischrestaurants mit Einheimischen, goldenes Licht über dem Wasser.",
                'distance' => '10 Min. (Fondachello)',
                'dist_note'=> 'Torre Archirafi 15 Min. · Giardini Naxos 20 Min. · Taormina/Isola Bella 30 Min.',
            ],
            'fr' => [
                'title'    => 'La mer de Sicile',
                'tagline'  => 'La côte ionienne à vos pieds',
                'intro'    => "Almaretna est à seulement 10 minutes de la mer. Pas n'importe quelle mer : la côte ionienne de la Sicile orientale offre des eaux cristallines, des villages de pêcheurs authentiques et des plages de toutes sortes.",
                'attractions' => [
                    ['Fondachello — Mascali', "La plage de référence d'Almaretna : à 10 minutes en voiture, un village marin authentique avec clubs de plage, restaurants de poisson frais et atmosphère familiale. La promenade de Fondachello au coucher du soleil est un spectacle à ne pas manquer."],
                    ['Torre Archirafi', "Le village de pêcheurs caractéristique de la commune de Riposto : la promenade historique, les jetées des pêcheurs et les bars en bord de mer. Un endroit authentique et peu touristique pour savourer le poisson frais."],
                    ['Giardini Naxos', "La baie la plus fréquentée sous Taormine : sable fin, eaux bleues, promenade riche en restaurants et clubs de plage. À deux pas de la magie de Taormine."],
                    ['Isola Bella — Taormine', "La petite île naturelle protégée reliée par un cordon de sable. Eaux de carte postale, snorkeling exceptionnel et cadre unique avec Taormine au-dessus et l'Etna en fond."],
                    ['Plages de Syracuse et Vendicari', "Plus au sud : la Réserve Naturelle de Vendicari et les plages de Plemmirio. Eaux cristallines et sable blanc — parmi les plus belles d'Italie."],
                ],
                'summer'   => "En été la côte ionienne est au sommet : mer chaude et limpide, villages animés, marchés nocturnes sur les promenades. Depuis Almaretna, une plage différente chaque jour.",
                'winter'   => "En hiver la mer de Sicile a son propre charme : promenades desertes, restaurants de poisson fréquentés par les locaux, lumière dorée sur l'eau.",
                'distance' => '10 min (Fondachello)',
                'dist_note'=> 'Torre Archirafi 15 min · Giardini Naxos 20 min · Taormine/Isola Bella 30 min.',
            ],
            'es' => [
                'title'    => 'El mar de Sicilia',
                'tagline'  => 'La costa jónica a tus pies',
                'intro'    => "Almaretna está a solo 10 minutos del mar. No cualquier mar: la costa jónica de Sicilia oriental ofrece aguas cristalinas, auténticos pueblos de pescadores y playas de todo tipo.",
                'attractions' => [
                    ['Fondachello — Mascali', "La playa de referencia de Almaretna: a 10 minutos en coche, un pueblo marinero auténtico con chiringuitos, restaurantes de pescado fresco y ambiente familiar. El paseo marítimo de Fondachello al atardecer es un espectáculo imperdible."],
                    ['Torre Archirafi', "El característico pueblo pesquero del municipio de Riposto: el paseo marítimo histórico, los muelles de pescadores y los locales junto al mar. Un lugar genuino y poco turístico para disfrutar de pescado fresco."],
                    ['Giardini Naxos', "La bahía más frecuentada bajo Taormina: arena fina, aguas azules, un paseo lleno de restaurantes y chiringuitos. A un paso de la magia de Taormina."],
                    ['Isola Bella — Taormina', "La pequeña isla natural protegida unida por un istmo de arena. Aguas de postal, snorkel excepcional y el escenario único con Taormina arriba y el Etna al fondo."],
                    ['Playas de Siracusa y Vendicari', "Más al sur: la Reserva Natural de Vendicari y las playas de Plemmirio. Aguas cristalinas y arena blanca — entre las más bellas de Italia, casi caribeñas."],
                ],
                'summer'   => "En verano la costa jónica está en su mejor momento: mar cálido y cristalino, pueblos animados, mercados nocturnos en el paseo marítimo. Desde Almaretna, una playa diferente cada día.",
                'winter'   => "También en invierno el mar de Sicilia tiene su encanto: paseos por el malecón desierto, restaurantes de pescado frecuentados por locales, luz dorada sobre el agua.",
                'distance' => '10 min (Fondachello)',
                'dist_note'=> 'Torre Archirafi 15 min · Giardini Naxos 20 min · Taormina/Isola Bella 30 min.',
            ],
        ],
    ];

    if (!isset($all[$slug])) return [];
    return $all[$slug][$lang] ?? $all[$slug]['it'];
}

/* ─── SEO per ogni destinazione ─────────────────────────────────────────── */

function alm_destinazione_seo(string $slug): array {
    $lang = alm_get_lang();

    $geo = [
        'taormina'        => ['lat' => 37.8536, 'lon' => 15.2866, 'type' => 'TouristAttraction'],
        'etna'            => ['lat' => 37.7510, 'lon' => 14.9952, 'type' => 'LandmarkOrHistoricalBuilding'],
        'catania'         => ['lat' => 37.5079, 'lon' => 15.0830, 'type' => 'City'],
        'siracusa'        => ['lat' => 37.0666, 'lon' => 15.2936, 'type' => 'TouristAttraction'],
        'messina'         => ['lat' => 38.1938, 'lon' => 15.5540, 'type' => 'City'],
        'mare-di-sicilia' => ['lat' => 37.7267, 'lon' => 15.2600, 'type' => 'TouristAttraction'],
    ];

    $seo = [
        'taormina' => [
            'it' => ['title' => 'Taormina | Almaretna – Villa in Sicilia Orientale',  'desc' => 'A 25 min da Almaretna: Teatro Greco antico, Isola Bella e panorami sul golfo ionico. Scopri Taormina con escursioni guidate su richiesta.'],
            'en' => ['title' => 'Taormina | Almaretna – Villa Eastern Sicily',         'desc' => '25 min from Almaretna: ancient Greek Theatre, Isola Bella and stunning Ionian gulf views. Explore Taormina with guided excursions on request.'],
            'de' => ['title' => 'Taormina | Almaretna – Villa Ostsizilien',            'desc' => '25 Min. von Almaretna: Antikes Theater, Isola Bella und Ionen-Meerespanoramen. Taormina mit geführten Ausflügen auf Anfrage erkunden.'],
            'fr' => ['title' => 'Taormine | Almaretna – Villa Sicile Orientale',      'desc' => 'À 25 min d\'Almaretna : Théâtre grec antique, Isola Bella et panoramas sur le golfe ionique. Découvrez Taormine avec excursions guidées.'],
            'es' => ['title' => 'Taormina | Almaretna – Villa Sicilia Oriental',      'desc' => 'A 25 min de Almaretna: Teatro Griego antiguo, Isola Bella y panoramas del golfo jónico. Explora Taormina con excursiones guiadas.'],
        ],
        'etna' => [
            'it' => ['title' => 'Etna | Almaretna – Villa in Sicilia Orientale',      'desc' => 'Il vulcano più alto d\'Europa a 20 min da Almaretna: trekking e funivia in estate, sci a Piano Provenzana in inverno. Escursioni guidate.'],
            'en' => ['title' => 'Mount Etna | Almaretna – Villa Eastern Sicily',       'desc' => 'Europe\'s highest volcano 20 min from Almaretna: summer trekking & cable car, winter skiing at Piano Provenzana. Guided excursions available.'],
            'de' => ['title' => 'Ätna | Almaretna – Villa Ostsizilien',               'desc' => 'Europas höchster Vulkan, 20 Min. von Almaretna: Sommer-Trekking und Seilbahn, Ski in Piano Provenzana im Winter. Geführte Ausflüge buchbar.'],
            'fr' => ['title' => 'Etna | Almaretna – Villa Sicile Orientale',          'desc' => 'Le plus haut volcan d\'Europe à 20 min d\'Almaretna : trekking estival, téléphérique et ski à Piano Provenzana en hiver. Excursions guidées.'],
            'es' => ['title' => 'Etna | Almaretna – Villa Sicilia Oriental',          'desc' => 'El volcán más alto de Europa a 20 min de Almaretna: senderismo y teleférico en verano, esquí en Piano Provenzana en invierno. Excursiones.'],
        ],
        'catania' => [
            'it' => ['title' => 'Catania | Almaretna – Villa in Sicilia Orientale',   'desc' => 'Catania barocca UNESCO a 30 min: fontana dell\'Elefante, Pescheria storica e street food autentico ai piedi dell\'Etna.'],
            'en' => ['title' => 'Catania | Almaretna – Villa Eastern Sicily',          'desc' => 'UNESCO baroque Catania 30 min away: Elephant Fountain, historic Pescheria fish market and authentic Sicilian street food at Etna\'s foot.'],
            'de' => ['title' => 'Catania | Almaretna – Villa Ostsizilien',             'desc' => 'Barockes Catania (UNESCO) 30 Min.: Elefantenbrunnen, historischer Fischmarkt Pescheria und authentisches Street Food am Fuß des Ätna.'],
            'fr' => ['title' => 'Catane | Almaretna – Villa Sicile Orientale',        'desc' => 'Catane baroque UNESCO à 30 min : fontaine de l\'Éléphant, Pescheria historique et street food sicilien authentique au pied de l\'Etna.'],
            'es' => ['title' => 'Catania | Almaretna – Villa Sicilia Oriental',       'desc' => 'Catania barroca UNESCO a 30 min: Fuente del Elefante, Pescheria histórica y street food siciliano auténtico al pie del Etna.'],
        ],
        'siracusa' => [
            'it' => ['title' => 'Siracusa | Almaretna – Villa in Sicilia Orientale',  'desc' => 'Siracusa e Ortigia UNESCO a ~90 min: Teatro Greco, Orecchio di Dionisio e spiagge di Vendicari. Duemila anni di storia sul mare ionico.'],
            'en' => ['title' => 'Syracuse | Almaretna – Villa Eastern Sicily',         'desc' => 'Syracuse & Ortigia UNESCO ~90 min away: Greek Theatre, Ear of Dionysius and Vendicari beaches. Two thousand years of history by the sea.'],
            'de' => ['title' => 'Syrakus | Almaretna – Villa Ostsizilien',             'desc' => 'Syrakus & Ortigia UNESCO ~90 Min.: Griechisches Theater, Dionysiosohr und Vendicari-Strände. Zweitausend Jahre Geschichte am Meer.'],
            'fr' => ['title' => 'Syracuse | Almaretna – Villa Sicile Orientale',      'desc' => 'Syracuse et Ortygie UNESCO à ~90 min : Théâtre grec, Oreille de Denys et plages de Vendicari. Deux mille ans d\'histoire en bord de mer.'],
            'es' => ['title' => 'Siracusa | Almaretna – Villa Sicilia Oriental',      'desc' => 'Siracusa y Ortigia UNESCO a ~90 min: Teatro griego, Oreja de Dionisio y playas de Vendicari. Dos mil años de historia junto al mar.'],
        ],
        'messina' => [
            'it' => ['title' => 'Messina | Almaretna – Villa in Sicilia Orientale',   'desc' => 'Messina e lo Stretto a ~50 min: orologio astronomico del Duomo, Museo Regionale e gateway per le Isole Eolie e la Calabria.'],
            'en' => ['title' => 'Messina | Almaretna – Villa Eastern Sicily',          'desc' => 'Messina & the Strait ~50 min away: Cathedral astronomical clock, Regional Museum and gateway to the Aeolian Islands and Calabria.'],
            'de' => ['title' => 'Messina | Almaretna – Villa Ostsizilien',             'desc' => 'Messina und Meerenge ~50 Min.: Astronomische Uhr des Doms, Regionalmuseum und Ausgangspunkt zu den Äolischen Inseln und Kalabrien.'],
            'fr' => ['title' => 'Messine | Almaretna – Villa Sicile Orientale',       'desc' => 'Messine et le Détroit à ~50 min : horloge astronomique du dôme, Musée Régional et gateway vers les Éoliennes et la Calabre.'],
            'es' => ['title' => 'Mesina | Almaretna – Villa Sicilia Oriental',        'desc' => 'Mesina y el Estrecho a ~50 min: reloj astronómico de la Catedral, Museo Regional y gateway a las Islas Eolias y Calabria.'],
        ],
        'mare-di-sicilia' => [
            'it' => ['title' => 'Mare di Sicilia | Almaretna – Villa Sicilia Orientale', 'desc' => 'Spiagge ioniche a 10 min: Fondachello borgo marinaro, Torre Archirafi, Giardini Naxos e Isola Bella. Mare cristallino tutto l\'anno.'],
            'en' => ['title' => 'Sicilian Sea | Almaretna – Villa Eastern Sicily',        'desc' => 'Ionian beaches 10 min away: Fondachello fishing village, Torre Archirafi, Giardini Naxos and Isola Bella. Crystal-clear sea all year.'],
            'de' => ['title' => 'Sizilianisches Meer | Almaretna – Villa Ostsizilien',   'desc' => 'Ionische Strände 10 Min. entfernt: Fondachello Fischerdorf, Torre Archirafi, Giardini Naxos und Isola Bella. Kristallklares Meer.'],
            'fr' => ['title' => 'Mer de Sicile | Almaretna – Villa Sicile Orientale',   'desc' => 'Plages ioniennes à 10 min : Fondachello village de pêcheurs, Torre Archirafi, Giardini Naxos et Isola Bella. Mer cristalline toute l\'année.'],
            'es' => ['title' => 'Mar de Sicilia | Almaretna – Villa Sicilia Oriental',  'desc' => 'Playas jónicas a 10 min: Fondachello pueblo pesquero, Torre Archirafi, Giardini Naxos e Isola Bella. Mar cristalino todo el año.'],
        ],
    ];

    if (!isset($seo[$slug])) return [];
    $result         = $seo[$slug][$lang] ?? $seo[$slug]['it'] ?? [];
    $result['geo']  = $geo[$slug] ?? null;
    return $result;
}
