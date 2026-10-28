<?php
/**
 * Cine Palácio - Semeador de Filmes Brasileiros
 * Popula a base inicial com filmes do cinema nacional (do clássico ao contemporâneo),
 * categorizados em "Mais Conhecidos" e "Menos Conhecidos / Lado B / Cult",
 * com capas, sinopses, classificação indicativa, diretor e elenco.
 */

require_once __DIR__ . '/../../config/bd.php';
require_once __DIR__ . '/../../config/audit.php';

function seedBrazilianMovies(PDO $pdo): int {
    $filmes = [
        // ==========================================
        // DESTAQUE & ESTÉTICA SETENTISTA / 2025
        // ==========================================
        [
            'imdb_id' => 'tt27814238',
            'titulo' => 'O Agente Secreto',
            'titulo_original' => 'O Agente Secreto',
            'ano' => 2025,
            'diretor' => 'Kleber Mendonça Filho',
            'elenco' => 'Wagner Moura, Maria Fernanda Cândido, Gabriel Leone, Hermila Guedes',
            'generos' => 'Suspense, Policial, Drama, Histórico',
            'classificacao_indicativa' => '16',
            'duracao_minutos' => 145,
            'sinopse' => 'No Brasil de 1977, durante a ditadura militar, Marcelo, um professor universitário em fuga, desembarca em Recife na semana do Carnaval para recomeçar a vida sob outra identidade, mas logo percebe que está sendo vigiado por agentes da repressão em um jogo de paranóia e espionagem tropical.',
            'capa_url' => 'https://upload.wikimedia.org/wikipedia/pt/6/60/O_Agente_Secreto_%28Cartaz_brasileiro%29.jpg',
            'imdb_rating' => 8.4,
            'imdb_votes' => 45000,
            'is_cult' => 0 // Mais Conhecido / Super Destaque
        ],

        // ==========================================
        // MAIS CONHECIDOS (MAINSTREAM / SUCESSOS)
        // ==========================================
        [
            'imdb_id' => 'tt0317248',
            'titulo' => 'Cidade de Deus',
            'titulo_original' => 'Cidade de Deus',
            'ano' => 2002,
            'diretor' => 'Fernando Meirelles, Kátia Lund',
            'elenco' => 'Alexandre Rodrigues, Leandro Firmino, Phellipe Haagensen, Seu Jorge, Alice Braga',
            'generos' => 'Crime, Drama, Ação',
            'classificacao_indicativa' => '16',
            'duracao_minutos' => 130,
            'sinopse' => 'Nas favelas do Rio de Janeiro entre os anos 1960 e 1980, dois jovens tomam caminhos opostos: Buscapé sonha em ser fotógrafo e registra o conflito da favela, enquanto Zé Pequeno constrói um império cruel no tráfico de drogas.',
            'capa_url' => 'https://upload.wikimedia.org/wikipedia/pt/1/10/CidadedeDeus.jpg',
            'imdb_rating' => 8.6,
            'imdb_votes' => 820000,
            'is_cult' => 0
        ],
        [
            'imdb_id' => 'tt0127536',
            'titulo' => 'Central do Brasil',
            'titulo_original' => 'Central do Brasil',
            'ano' => 1998,
            'diretor' => 'Walter Salles',
            'elenco' => 'Fernanda Montenegro, Vinícius de Oliveira, Marília Pêra, Soia Lira',
            'generos' => 'Drama',
            'classificacao_indicativa' => '12',
            'duracao_minutos' => 110,
            'sinopse' => 'Dora, uma ex-professora amargurada que escreve cartas para analfabetos na estação Central do Brasil, cruza seu destino com Josué, um menino que acaba de perder a mãe e procura pelo pai que nunca conheceu pelo interior do sertão.',
            'capa_url' => 'https://upload.wikimedia.org/wikipedia/pt/0/0c/Central_do_Brasil_1998_poster.jpg',
            'imdb_rating' => 8.0,
            'imdb_votes' => 52000,
            'is_cult' => 0
        ],
        [
            'imdb_id' => 'tt0861739',
            'titulo' => 'Tropa de Elite',
            'titulo_original' => 'Tropa de Elite',
            'ano' => 2007,
            'diretor' => 'José Padilha',
            'elenco' => 'Wagner Moura, André Ramiro, Caio Junqueira, Milhem Cortaz, Fernanda Machado',
            'generos' => 'Ação, Crime, Drama',
            'classificacao_indicativa' => '16',
            'duracao_minutos' => 115,
            'sinopse' => 'Em 1997, no Rio de Janeiro, o capitão Nascimento precisa encontrar um substituto para seu posto no BOPE enquanto combate o crime nas favelas e lida com o nascimento iminente de seu primeiro filho.',
            'capa_url' => 'https://upload.wikimedia.org/wikipedia/pt/2/2a/TropaDeElitePoster.jpg',
            'imdb_rating' => 8.0,
            'imdb_votes' => 125000,
            'is_cult' => 0
        ],
        [
            'imdb_id' => 'tt1555149',
            'titulo' => 'Tropa de Elite 2: O Inimigo Agora é Outro',
            'titulo_original' => 'Tropa de Elite 2',
            'ano' => 2010,
            'diretor' => 'José Padilha',
            'elenco' => 'Wagner Moura, Irandhir Santos, André Ramiro, Maria Ribeiro, Seu Jorge',
            'generos' => 'Ação, Crime, Drama',
            'classificacao_indicativa' => '16',
            'duracao_minutos' => 115,
            'sinopse' => 'Promovido a subsecretário de Inteligência, Nascimento enfrenta a corrupção do sistema e o avanço das milícias na estrutura do estado carioca, em um embate que atinge esferas políticas e pessoais.',
            'capa_url' => 'https://upload.wikimedia.org/wikipedia/pt/1/14/Tropa_de_elite_2_poster.jpg',
            'imdb_rating' => 8.0,
            'imdb_votes' => 92000,
            'is_cult' => 0
        ],
        [
            'imdb_id' => 'tt0271383',
            'titulo' => 'O Auto da Compadecida',
            'titulo_original' => 'O Auto da Compadecida',
            'ano' => 2000,
            'diretor' => 'Guel Arraes',
            'elenco' => 'Matheus Nachtergaele, Selton Mello, Fernanda Montenegro, Lima Duarte, Marco Nanini',
            'generos' => 'Comédia, Aventura, Fantasia',
            'classificacao_indicativa' => '12',
            'duracao_minutos' => 104,
            'sinopse' => 'As divertidas peripécias de João Grilo e Chicó, dois sertanejos pobretões que vivem de golpes no vilarejo de Taperoá, até enfrentarem o julgamento celestial diante da Virgem Maria e de Jesus Cristo.',
            'capa_url' => 'https://upload.wikimedia.org/wikipedia/pt/b/bf/O_auto_da_compadecida.jpg',
            'imdb_rating' => 8.6,
            'imdb_votes' => 45000,
            'is_cult' => 0
        ],
        [
            'imdb_id' => 'tt2762506',
            'titulo' => 'Bacurau',
            'titulo_original' => 'Bacurau',
            'ano' => 2019,
            'diretor' => 'Kleber Mendonça Filho, Juliano Dornelles',
            'elenco' => 'Bárbara Colen, Thomas Aquino, Silvero Pereira, Sônia Braga, Udo Kier',
            'generos' => 'Mistério, Ficção Científica, Faroeste, Suspense',
            'classificacao_indicativa' => '16',
            'duracao_minutos' => 131,
            'sinopse' => 'Os moradores de um pequeno povoado no sertão brasileiro descobrem que a comunidade desapareceu do mapa digital e está sob cerco de um grupo de mercenários estrangeiros sanguinários.',
            'capa_url' => 'https://upload.wikimedia.org/wikipedia/pt/d/db/Bacurau_%28cartaz%29.png',
            'imdb_rating' => 7.3,
            'imdb_votes' => 38000,
            'is_cult' => 0
        ],
        [
            'imdb_id' => 'tt4744198',
            'titulo' => 'Aquarius',
            'titulo_original' => 'Aquarius',
            'ano' => 2016,
            'diretor' => 'Kleber Mendonça Filho',
            'elenco' => 'Sônia Braga, Humberto Carrão, Irandhir Santos, Maeve Jinkings',
            'generos' => 'Drama',
            'classificacao_indicativa' => '16',
            'duracao_minutos' => 146,
            'sinopse' => 'Clara, uma jornalista aposentada de 65 anos e escritora, é a última moradora do lendário Edifício Aquarius na orla de Boa Viagem, e resiste bravamente às pressões de uma construtora predadora.',
            'capa_url' => 'https://upload.wikimedia.org/wikipedia/pt/c/cb/Aquarius_filme_cartaz.jpg',
            'imdb_rating' => 7.5,
            'imdb_votes' => 16000,
            'is_cult' => 0
        ],
        [
            'imdb_id' => 'tt3742378',
            'titulo' => 'Que Horas Ela Volta?',
            'titulo_original' => 'Que Horas Ela Volta?',
            'ano' => 2015,
            'diretor' => 'Anna Muylaert',
            'elenco' => 'Regina Casé, Camila Márdila, Karine Teles, Lourenço Mutarelli, Michel Joelsas',
            'generos' => 'Comédia, Drama',
            'classificacao_indicativa' => '12',
            'duracao_minutos' => 112,
            'sinopse' => 'Val é uma empregada doméstica nordestina que vive na casa dos patrões em São Paulo. A dinâmica social da mansão é abalada com a chegada de sua filha Jéssica para prestar vestibular para arquitetura.',
            'capa_url' => 'https://upload.wikimedia.org/wikipedia/pt/5/52/The_Second_Mother_poster.jpg',
            'imdb_rating' => 7.7,
            'imdb_votes' => 21000,
            'is_cult' => 0
        ],
        [
            'imdb_id' => 'tt0371606',
            'titulo' => 'Carandiru',
            'titulo_original' => 'Carandiru',
            'ano' => 2003,
            'diretor' => 'Héctor Babenco',
            'elenco' => 'Luiz Carlos Vasconcelos, Rodrigo Santoro, Milton Gonçalves, Lázaro Ramos, Caio Blat',
            'generos' => 'Crime, Drama',
            'classificacao_indicativa' => '16',
            'duracao_minutos' => 145,
            'sinopse' => 'As histórias de vida e sobrevivência dos detentos da Casa de Detenção de São Paulo vistas pelos olhos de um médico sanitarista voluntário, culminando no massacre de 1992.',
            'capa_url' => 'https://upload.wikimedia.org/wikipedia/pt/4/4b/Carandiru_filme.jpg',
            'imdb_rating' => 7.6,
            'imdb_votes' => 24000,
            'is_cult' => 0
        ],
        [
            'imdb_id' => 'tt0264426',
            'titulo' => 'Bicho de Sete Cabeças',
            'titulo_original' => 'Bicho de Sete Cabeças',
            'ano' => 2001,
            'diretor' => 'Laís Bodanzky',
            'elenco' => 'Rodrigo Santoro, Othon Bastos, Cássia Kis, Jairo Mattos, Gero Camilo',
            'generos' => 'Drama',
            'classificacao_indicativa' => '14',
            'duracao_minutos' => 74,
            'sinopse' => 'Neto é internado à força pelo pai conservador em um manicômio após ser flagrado com um baseado. No hospital psiquiátrico, ele é submetido aos horrores da violência manicomial brasileira.',
            'capa_url' => 'https://upload.wikimedia.org/wikipedia/pt/b/b5/Bicho_de_Sete_Cabe%C3%A7as.jpg',
            'imdb_rating' => 7.6,
            'imdb_votes' => 12000,
            'is_cult' => 0
        ],
        [
            'imdb_id' => 'tt0362725',
            'titulo' => 'O Homem que Copiava',
            'titulo_original' => 'O Homem que Copiava',
            'ano' => 2003,
            'diretor' => 'Jorge Furtado',
            'elenco' => 'Lázaro Ramos, Leandra Leal, Luana Piovani, Pedro Cardoso',
            'generos' => 'Comédia, Crime, Romance',
            'classificacao_indicativa' => '14',
            'duracao_minutos' => 123,
            'sinopse' => 'André é operador de fotocopiadora em Porto Alegre e se apaixona por Sílvia, que espia pela janela. Para conseguir se aproximar da amada, ele passa a imprimir notas falsas de dinheiro na fotocopiadora.',
            'capa_url' => 'https://upload.wikimedia.org/wikipedia/pt/f/f6/O_Homem_Que_Copiava.jpg',
            'imdb_rating' => 7.5,
            'imdb_votes' => 11000,
            'is_cult' => 0
        ],
        [
            'imdb_id' => 'tt1039967',
            'titulo' => 'Estômago',
            'titulo_original' => 'Estômago: Uma História Nada Secreta Sobre Poder, Sexo e Culinária',
            'ano' => 2007,
            'diretor' => 'Marcos Jorge',
            'elenco' => 'João Miguel, Fabiula Nascimento, Babu Santana, Carlo Briani',
            'generos' => 'Comédia, Crime, Drama',
            'classificacao_indicativa' => '16',
            'duracao_minutos' => 113,
            'sinopse' => 'Raimundo Nonato chega à cidade grande sem tostão e descobre um dom singular para a culinária, usando seus temperos para conquistar primeiro os clientes de um boteco e depois os líderes de uma penitenciária.',
            'capa_url' => 'https://upload.wikimedia.org/wikipedia/pt/3/30/Estomago_cartaz.jpg',
            'imdb_rating' => 7.6,
            'imdb_votes' => 15000,
            'is_cult' => 0
        ],
        [
            'imdb_id' => 'tt4711314',
            'titulo' => 'Bingo: O Rei das Manhãs',
            'titulo_original' => 'Bingo: O Rei das Manhãs',
            'ano' => 2017,
            'diretor' => 'Daniel Rezende',
            'elenco' => 'Vladimir Brichta, Leandra Leal, Augusto Madeira, Ana Lúcia Torre',
            'generos' => 'Biografia, Comédia, Drama',
            'classificacao_indicativa' => '16',
            'duracao_minutos' => 113,
            'sinopse' => 'Inspirado na trajetória de Arlindo Barreto nos anos 80, o ator alcança a fama astronômica interpretando um palhaço apresentador de programa infantil na TV, mergulhando nos excessos de drogas e anonimato.',
            'capa_url' => 'https://upload.wikimedia.org/wikipedia/pt/2/23/Bingo_-_O_Rei_das_Manh%C3%A3s_poster.jpg',
            'imdb_rating' => 7.9,
            'imdb_votes' => 13000,
            'is_cult' => 0
        ],

        // ==========================================
        // MENOS CONHECIDOS / CULT / LADO B / ANOS 70
        // (Obras autorais, Cinema Novo, Cinema Marginal e Pérolas Cult)
        // ==========================================
        [
            'imdb_id' => 'tt0062711',
            'titulo' => 'O Bandido da Luz Vermelha',
            'titulo_original' => 'O Bandido da Luz Vermelha',
            'ano' => 1968,
            'diretor' => 'Rogério Sganzerla',
            'elenco' => 'Paulo Villaça, Helena Ignez, Pagano Sobrinho, Luiz Linhares',
            'generos' => 'Crime, Suspense, Vanguarda, Cult',
            'classificacao_indicativa' => '16',
            'duracao_minutos' => 92,
            'sinopse' => 'Marco definitivo do Cinema Marginal paulistano. Um assaltante enigmático de São Paulo invade mansões armado com uma lanterna vermelha, dialoga com as vítimas e se torna obsessão da mídia marrom e da polícia.',
            'capa_url' => 'https://upload.wikimedia.org/wikipedia/pt/3/3a/O_Bandido_da_Luz_Vermelha.jpg',
            'imdb_rating' => 7.4,
            'imdb_votes' => 3100,
            'is_cult' => 1
        ],
        [
            'imdb_id' => 'tt0064623',
            'titulo' => 'Macunaíma',
            'titulo_original' => 'Macunaíma',
            'ano' => 1969,
            'diretor' => 'Joaquim Pedro de Andrade',
            'elenco' => 'Grande Otelo, Paulo José, Jardel Filho, Dina Sfat, Milton Gonçalves',
            'generos' => 'Comédia, Fantasia, Tropicália, Cult',
            'classificacao_indicativa' => '14',
            'duracao_minutos' => 108,
            'sinopse' => 'O anti-herói sem nenhum caráter nasce no fundo da floresta amazônica e viaja com os irmãos para a selva de concreto do Rio de Janeiro para recuperar a pedra mágica muiraquitã das garras do gigante devorador.',
            'capa_url' => 'https://upload.wikimedia.org/wikipedia/pt/c/c2/Macunaima_poster.jpg',
            'imdb_rating' => 7.0,
            'imdb_votes' => 2800,
            'is_cult' => 1
        ],
        [
            'imdb_id' => 'tt0074426',
            'titulo' => 'Dona Flor e Seus Dois Maridos',
            'titulo_original' => 'Dona Flor e Seus Dois Maridos',
            'ano' => 1976,
            'diretor' => 'Bruno Barreto',
            'elenco' => 'Sônia Braga, José Wilker, Mauro Mendonça, Dinorah Brillanti',
            'generos' => 'Comédia, Fantasia, Romance',
            'classificacao_indicativa' => '18',
            'duracao_minutos' => 118,
            'sinopse' => 'Em Salvador nos anos 1940 e 70, Florípia se casa com o respeitável farmacêutico Teodoro após a morte do boêmio e ardente Vadinho. Logo, o fantasma do falecido retorna para dividir o leito nupcial em segredo.',
            'capa_url' => 'https://upload.wikimedia.org/wikipedia/pt/7/7b/Dona_Flor_e_seus_dois_maridos.jpg',
            'imdb_rating' => 6.8,
            'imdb_votes' => 4200,
            'is_cult' => 1
        ],
        [
            'imdb_id' => 'tt0078920',
            'titulo' => 'Bye Bye Brasil',
            'titulo_original' => 'Bye Bye Brasil',
            'ano' => 1979,
            'diretor' => 'Carlos Diegues',
            'elenco' => 'José Wilker, Betty Faria, Fábio Júnior, Zaira Zambelli',
            'generos' => 'Comédia, Drama, Road Movie',
            'classificacao_indicativa' => '14',
            'duracao_minutos' => 110,
            'sinopse' => 'A Caravana Rolidei, trupe mambembe de artistas de variedades, cruza os confins do Brasil profundo e da Amazônia dos anos 70 disputando o fascínio da população contra o avanço das antenas de televisão.',
            'capa_url' => 'https://upload.wikimedia.org/wikipedia/pt/a/a2/Bye_bye_brasil_poster.jpg',
            'imdb_rating' => 7.2,
            'imdb_votes' => 2500,
            'is_cult' => 1
        ],
        [
            'imdb_id' => 'tt0082934',
            'titulo' => 'Pra Frente, Brasil',
            'titulo_original' => 'Pra Frente, Brasil',
            'ano' => 1982,
            'diretor' => 'Roberto Farias',
            'elenco' => 'Reginaldo Faria, Antônio Fagundes, Natália do Vale, Elizabeth Savalla, Carlos Zara',
            'generos' => 'Drama, Histórico, Suspense Político',
            'classificacao_indicativa' => '16',
            'duracao_minutos' => 105,
            'sinopse' => 'Durante o ufanismo do tricampeonato de futebol do Brasil na Copa de 70, um pacato cidadão de classe média é confundido por engano com um militante guerrilheiro e tragado pelas masmorras da tortura de estado.',
            'capa_url' => 'https://upload.wikimedia.org/wikipedia/pt/b/b2/Pra_frente_brasil_poster.jpg',
            'imdb_rating' => 7.5,
            'imdb_votes' => 1800,
            'is_cult' => 1
        ],
        [
            'imdb_id' => 'tt0082321',
            'titulo' => 'Eles Não Usam Black-Tie',
            'titulo_original' => 'Eles Não Usam Black-Tie',
            'ano' => 1981,
            'diretor' => 'Leon Hirszman',
            'elenco' => 'Gianfrancesco Guarnieri, Fernanda Montenegro, Carlos Alberto Riccelli, Bete Mendes',
            'generos' => 'Drama, Político, Social',
            'classificacao_indicativa' => '14',
            'duracao_minutos' => 134,
            'sinopse' => 'Em meio a uma efervescente greve metalúrgica no ABC paulista na virada dos anos 70 para 80, pai sindicalista histórico e filho operário entram em doloroso confronto ideológico sobre greve e subsistência.',
            'capa_url' => 'https://upload.wikimedia.org/wikipedia/pt/0/07/Eles_nao_usam_black_tie.jpg',
            'imdb_rating' => 7.9,
            'imdb_votes' => 2100,
            'is_cult' => 1
        ],
        [
            'imdb_id' => 'tt0082054',
            'titulo' => 'O Beijo no Asfalto',
            'titulo_original' => 'O Beijo no Asfalto',
            'ano' => 1981,
            'diretor' => 'Bruno Barreto',
            'elenco' => 'Ney Latorraca, Tarcísio Meira, Lídia Brondi, Daniel Filho, Christiane Torloni',
            'generos' => 'Drama, Suspense, Mistério',
            'classificacao_indicativa' => '16',
            'duracao_minutos' => 102,
            'sinopse' => 'Ao presenciar um atropelamento no centro do Rio, Arandir atende ao último pedido do homem agonizante e lhe dá um beijo nos lábios. O gesto é transformado em manchete sensacionalista criminosa por um repórter e um delegado corruptos.',
            'capa_url' => 'https://upload.wikimedia.org/wikipedia/pt/c/c5/O_beijo_no_asfalto_1981.jpg',
            'imdb_rating' => 7.1,
            'imdb_votes' => 1100,
            'is_cult' => 1
        ],
        [
            'imdb_id' => 'tt0082912',
            'titulo' => 'Pixote: A Lei do Mais Fraco',
            'titulo_original' => 'Pixote: A Lei do Mais Fraco',
            'ano' => 1980,
            'diretor' => 'Héctor Babenco',
            'elenco' => 'Fernando Ramos da Silva, Marília Pêra, Jardel Filho, Rubens de Falco',
            'generos' => 'Crime, Drama',
            'classificacao_indicativa' => '18',
            'duracao_minutos' => 128,
            'sinopse' => 'Um retrato pungente e cru dos meninos em situação de rua de São Paulo, internados num reformatório juvenil desumano e empurrados precocemente para o submundo do crime e da prostituição.',
            'capa_url' => 'https://upload.wikimedia.org/wikipedia/pt/a/a3/Pixote_A_Lei_do_Mais_Fraco.jpg',
            'imdb_rating' => 8.0,
            'imdb_votes' => 9500,
            'is_cult' => 1
        ],
        [
            'imdb_id' => 'tt0057001',
            'titulo' => 'Deus e o Diabo na Terra do Sol',
            'titulo_original' => 'Deus e o Diabo na Terra do Sol',
            'ano' => 1964,
            'diretor' => 'Glauber Rocha',
            'elenco' => 'Geraldo Del Rey, Yoná Magalhães, Othon Bastos, Maurício do Valle',
            'generos' => 'Faroeste Sertanejo, Drama, Cinema Novo, Obra-Prima',
            'classificacao_indicativa' => '14',
            'duracao_minutos' => 120,
            'sinopse' => 'Manoel, um vaqueiro explorado pelo coronel, mata o patrão em legítima defesa e foge com a esposa Rosa pelo sertão, aliando-se ao beato Sebastião e depois ao bando do temido cangaceiro Corisco, caçados por Antônio das Mortes.',
            'capa_url' => 'https://upload.wikimedia.org/wikipedia/pt/d/d4/Deus_e_o_diabo_na_terra_do_sol.jpg',
            'imdb_rating' => 7.6,
            'imdb_votes' => 6400,
            'is_cult' => 1
        ],
        [
            'imdb_id' => 'tt0062354',
            'titulo' => 'Terra em Transe',
            'titulo_original' => 'Terra em Transe',
            'ano' => 1967,
            'diretor' => 'Glauber Rocha',
            'elenco' => 'Jardel Filho, Paulo Autran, José Lewgoy, Glauce Rocha, Paulo Gracindo',
            'generos' => 'Drama, Político, Cinema Novo, Poesia',
            'classificacao_indicativa' => '14',
            'duracao_minutos' => 111,
            'sinopse' => 'No fictício e febril país de Eldorado, o poeta e jornalista Paulo Martins transita entre as ambições demagógicas do líder populista e a fúria autoritária do ditador de extrema-direita apoiado pelo capital estrangeiro.',
            'capa_url' => 'https://upload.wikimedia.org/wikipedia/pt/9/91/Terra_em_Transe_1967_poster.jpg',
            'imdb_rating' => 7.5,
            'imdb_votes' => 4300,
            'is_cult' => 1
        ],
        [
            'imdb_id' => 'tt0219590',
            'titulo' => 'O Som ao Redor',
            'titulo_original' => 'O Som ao Redor',
            'ano' => 2012,
            'diretor' => 'Kleber Mendonça Filho',
            'elenco' => 'Irandhir Santos, Gustavo Jahn, Maeve Jinkings, Waldemar José Solha',
            'generos' => 'Drama, Suspense, Mistério',
            'classificacao_indicativa' => '16',
            'duracao_minutos' => 131,
            'sinopse' => 'A presença de uma empresa particular de vigilância em uma rua de classe média na Zona Sul do Recife traz uma falsa sensação de segurança, desenterrando tensões ancestrais de coronelismo e medo.',
            'capa_url' => 'https://upload.wikimedia.org/wikipedia/pt/d/d6/Neighboring_Sounds_poster.jpg',
            'imdb_rating' => 7.1,
            'imdb_votes' => 9800,
            'is_cult' => 1
        ],
        [
            'imdb_id' => 'tt0348513',
            'titulo' => 'Amarelo Manga',
            'titulo_original' => 'Amarelo Manga',
            'ano' => 2002,
            'diretor' => 'Cláudio Assis',
            'elenco' => 'Matheus Nachtergaele, Leona Cavalli, Jonas Bloch, Dira Paes, Chico Diaz',
            'generos' => 'Drama, Cult, Cinema Marginal Moderno',
            'classificacao_indicativa' => '18',
            'duracao_minutos' => 103,
            'sinopse' => 'Um mosaico visceral de personagens do submundo recifense gira em torno do açougue de Wellington, da sensualidade de Kika, da religiosidade fanática de Regina e das obsessões mórbidas do cozinheiro Lígia.',
            'capa_url' => 'https://upload.wikimedia.org/wikipedia/pt/a/a0/Amarelo_manga_poster.jpg',
            'imdb_rating' => 7.2,
            'imdb_votes' => 3100,
            'is_cult' => 1
        ],
        [
            'imdb_id' => 'tt0455404',
            'titulo' => 'Cinema, Aspirinas e Urubus',
            'titulo_original' => 'Cinema, Aspirinas e Urubus',
            'ano' => 2005,
            'diretor' => 'Marcelo Gomes',
            'elenco' => 'Peter Ketnath, João Miguel, Hermila Guedes, Zezita Matos',
            'generos' => 'Aventura, Drama, Road Movie',
            'classificacao_indicativa' => '14',
            'duracao_minutos' => 99,
            'sinopse' => 'Em 1942, Johann foge da Segunda Guerra na Alemanha e viaja pelo sertão nordestino vendendo aspirina com a ajuda de um projetor de cinema ambulante, construindo amizade inesperada com Ranulpho.',
            'capa_url' => 'https://upload.wikimedia.org/wikipedia/pt/4/4e/Cinema%2C_Aspirinas_e_Urubus.jpg',
            'imdb_rating' => 7.1,
            'imdb_votes' => 2900,
            'is_cult' => 1
        ],
        [
            'imdb_id' => 'tt0330602',
            'titulo' => 'Madame Satã',
            'titulo_original' => 'Madame Satã',
            'ano' => 2002,
            'diretor' => 'Karim Aïnouz',
            'elenco' => 'Lázaro Ramos, Marcélia Cartaxo, Flavio Bauraqui, Fellipe Marques, Renata Sorrah',
            'generos' => 'Biografia, Crime, Drama',
            'classificacao_indicativa' => '16',
            'duracao_minutos' => 105,
            'sinopse' => 'Na boemia da Lapa dos anos 1930, João Francisco dos Santos sonha em ser astro de cabaré enquanto sobrevive entre pequenos delitos, brigas lendárias e a gestação do mito Madame Satã.',
            'capa_url' => 'https://upload.wikimedia.org/wikipedia/pt/8/87/Madame_sata_cartaz.jpg',
            'imdb_rating' => 7.1,
            'imdb_votes' => 4100,
            'is_cult' => 1
        ],
        [
            'imdb_id' => 'tt6430102',
            'titulo' => 'As Boas Maneiras',
            'titulo_original' => 'As Boas Maneiras',
            'ano' => 2017,
            'diretor' => 'Juliana Rojas, Marco Dutra',
            'elenco' => 'Isabél Zuaa, Marjorie Estiano, Miguel Lobo, Cida Moreira',
            'generos' => 'Fantasia, Terror, Drama, Cult',
            'classificacao_indicativa' => '14',
            'duracao_minutos' => 135,
            'sinopse' => 'Clara, enfermeira da periferia paulistana, é contratada pela misteriosa e rica Ana para ser babá de seu filho que vai nascer. Conforme a gravidez avança, segredos sobrenaturais emergem nas noites de lua cheia.',
            'capa_url' => 'https://upload.wikimedia.org/wikipedia/pt/2/23/As_Boas_Maneiras_poster.jpg',
            'imdb_rating' => 6.8,
            'imdb_votes' => 3300,
            'is_cult' => 1
        ],
        [
            'imdb_id' => 'tt15495570',
            'titulo' => 'Marte Um',
            'titulo_original' => 'Marte Um',
            'ano' => 2022,
            'diretor' => 'Gabriel Martins',
            'elenco' => 'Cícero Lucas, Carlos Francisco, Rejane Faria, Camilla Damião',
            'generos' => 'Drama, Família',
            'classificacao_indicativa' => '16',
            'duracao_minutos' => 115,
            'sinopse' => 'Uma família negra da periferia de Contagem enfrenta as tensões cotidianas e políticas do Brasil contemporâneo enquanto o caçula Deivinho sonha secretamente em se tornar astrofísico e colonizar Marte.',
            'capa_url' => 'https://upload.wikimedia.org/wikipedia/pt/3/36/Marte_Um_poster.jpg',
            'imdb_rating' => 7.4,
            'imdb_votes' => 3700,
            'is_cult' => 1
        ]
    ];

    $count = 0;
    $stmtCheck = $pdo->prepare("SELECT id FROM filmes WHERE titulo = :titulo OR imdb_id = :imdb_id LIMIT 1");
    $stmtInsert = $pdo->prepare("
        INSERT INTO filmes (imdb_id, titulo, titulo_original, ano, diretor, elenco, generos, classificacao_indicativa, duracao_minutos, sinopse, capa_url, imdb_rating, imdb_votes, is_cult)
        VALUES (:imdb_id, :titulo, :titulo_original, :ano, :diretor, :elenco, :generos, :classificacao_indicativa, :duracao_minutos, :sinopse, :capa_url, :imdb_rating, :imdb_votes, :is_cult)
    ");

    foreach ($filmes as $f) {
        $stmtCheck->execute([
            ':titulo' => $f['titulo'],
            ':imdb_id' => $f['imdb_id']
        ]);
        if (!$stmtCheck->fetch()) {
            $stmtInsert->execute([
                ':imdb_id' => $f['imdb_id'],
                ':titulo' => $f['titulo'],
                ':titulo_original' => $f['titulo_original'],
                ':ano' => $f['ano'],
                ':diretor' => $f['diretor'],
                ':elenco' => $f['elenco'],
                ':generos' => $f['generos'],
                ':classificacao_indicativa' => $f['classificacao_indicativa'],
                ':duracao_minutos' => $f['duracao_minutos'],
                ':sinopse' => $f['sinopse'],
                ':capa_url' => $f['capa_url'],
                ':imdb_rating' => $f['imdb_rating'],
                ':imdb_votes' => $f['imdb_votes'],
                ':is_cult' => $f['is_cult']
            ]);
            $count++;
        }
    }

    if ($count > 0) {
        logAudit($pdo, null, 'SISTEMA', 'SEED_FILMES', "Banco de filmes semeado com {$count} títulos do cinema brasileiro.");
    }

    return $count;
}
