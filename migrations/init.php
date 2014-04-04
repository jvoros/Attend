<?
// script to load initial data into database

date_default_timezone_set('America/Denver');
require '../vendor/autoload.php';

// initialize RedBean
R::setup('sqlite:../emcanon.sqlite');

// emtpy old database
R::nuke();
echo "Nuked old dbase<br />";

// ROLES
// initial user roles
$user = R::dispense('role');
$user->role = 'user';
$user_id = R::store($user);

$author = R::dispense('role');
$author->role = 'author';
$author_id = R::store($author);

$editor = R::dispense('role');
$editor->role = 'editor';
$editor_id = R::store($editor);

echo "Created roles <br />";


//USERS
$users                    = R::dispense('user', 3);

$users[0]                 = R::dispense('user');
$users[0]->name           = "Scott Weingert";
$users[0]->title          = "MD";
$users[0]->email          = "swine@emcrit.com";
$users[0]->sharedRole[]   = $user;
$users[0]->created        = "2013-11-01 17:30:30";
$users[0]->last_visit     = date("Y-m-d H:i:s");
$users[0]->twitter        = "swinemcrit"; //twitter nickname
$users[0]->facebook       = "scotty.winegart"; //facebook nickname
$users[0]->google         = "swine@gmail.com"; //google email
$users[0]->gplus          = "https://plus.google.com/115814176630336921439"; //google plus
$users[0]->thumb          = "https://lh3.googleusercontent.com/-l0uuuL-hNEk/AAAAAAAAAAI/AAAAAAAAAAA/6aSflWv5hNQ/photo.jpg";
$users[0]->bio            = "Etsy trust fund yr hoodie selfies, beard scenester pop-up drinking vinegar pickled plaid iPhone cliche kale chips before they sold out. Hella scenester PBR&B High Life American Apparel, twee flannel. Pinterest cred chillwave, distillery food truck Bushwick locavore McSweeney's lo-fi selfies letterpress blog readymade tofu.";

$users[1]                 = R::dispense('user');
$users[1]->name           = "Jeremy Voros";
$users[1]->title          = "MD";
$users[1]->email          = "jeremyvoros@gmail.com";
$users[1]->sharedRole[]   = $editor;
$users[1]->created        = "2013-10-01 17:30:30";
$users[1]->last_visit     = date("Y-m-d H:i:s");
$users[1]->twitter        = "jeremyvoros"; //twitter nickname
$users[1]->facebook       = "jeremy.voros"; //facebook nickname
$users[1]->google         = "jeremyvoros@gmail.com"; //google email
$users[1]->gplus          = "https://plus.google.com/115814176630336921439"; //google plus
$users[1]->thumb          = "https://lh3.googleusercontent.com/-l0uuuL-hNEk/AAAAAAAAAAI/AAAAAAAAAAA/6aSflWv5hNQ/photo.jpg";
$users[1]->bio            = "Etsy trust fund yr hoodie selfies, beard scenester pop-up drinking vinegar pickled plaid iPhone cliche kale chips before they sold out. Hella scenester PBR&B High Life American Apparel, twee flannel. Pinterest cred chillwave, distillery food truck Bushwick locavore McSweeney's lo-fi selfies letterpress blog readymade tofu.";

$users[2]                 = R::dispense('user');
$users[2]->name           = "M. Austin Johnson";
$users[2]->title          = "MD PhD";
$users[2]->email          = "austinjohnson@gmail.com";
$users[2]->sharedRole[]   = $author;
$users[2]->created        = "2013-10-12 17:30:30";
$users[2]->last_visit     = date("Y-m-d H:i:s");
$users[2]->twitter        = "maustin"; //twitter nickname
$users[2]->facebook       = "austin.johnson"; //facebook nickname
$users[2]->google         = "austinjohnson@gmail.com"; //google email
$users[2]->gplus          = "https://plus.google.com/115814176630336921439"; //google plus
$users[2]->thumb          = "https://lh3.googleusercontent.com/-l0uuuL-hNEk/AAAAAAAAAAI/AAAAAAAAAAA/6aSflWv5hNQ/photo.jpg";
$users[2]->bio            = "Etsy trust fund yr hoodie selfies, beard scenester pop-up drinking vinegar pickled plaid iPhone cliche kale chips before they sold out. Hella scenester PBR&B High Life American Apparel, twee flannel. Pinterest cred chillwave, distillery food truck Bushwick locavore McSweeney's lo-fi selfies letterpress blog readymade tofu.";

foreach ($users as $user) { $user_id = R::store($user); }

echo "Created users <br />";

// TAGS
$tags = array(
    'cardiology',
    'critical care',
    'infectious disease',
    'pulmonary',
    'gi',
    'trauma',
    'pediatrics',
    'toxicology',
    'neurology',
    'ems',
    'radiology'
);


// DUMMY WRITEUPS
$teasers = array(
    'During a cardiopulmonary resuscitation, an end-tidal CO2 of less than 10mm Hg in patients with a PEA rhythm portends a poor prognosis.',
    'Pariatur messenger bag tote bag tousled, officia Tonx dolore Marfa labore. Twee non messenger bag id sapiente fanny pack. Incididunt Truffaut seitan before they sold out, meh mixtape pug pour-over 8-bit readymade.',
    'Sriracha jean shorts dolor, assumenda kitsch ea in tattooed forage. PBR reprehenderit gastropub squid. Terry Richardson placeat voluptate church-key lomo readymade.',
    'Sustainable eiusmod locavore, excepteur authentic ethnic sriracha jean shorts aliquip American Apparel ennui commodo keffiyeh twee. Banksy plaid next level proident, quis reprehenderit cillum asymmetrical High Life Portland Wes Anderson'
);

$writeups = array(
    '<h2>Takehome</h2>
    <p>During a cardiopulmonary resuscitation, an end-tidal CO2 of less than 10mm Hg in patients with a PEA rhythm portends a poor prognosis.</p>
    <h2>Background</h2>
    <p>There are over 300,000 out-of-hospital cardiac arrests every year in the United States, with approximately 10% of patients surviving to hospital discharge. The number of patients who are admitted to the hospital but die prior to discharge is costly both to society as well as to individual families. At the time that this paper was written there were few markers of prognosis that could be used during a resuscitation to predict whether a patient would regain circulation or survive to discharge. Prior research had demonstrated that end-tidal carbon dioxide (etCO2) could be used as an indirect measure of cardiac output during a resuscitation, and these authors hypothesized that etCO2 could be used in the pre-hospital setting to predict which patient in pulseless-electrical activity (PEA) would die. </p>
    <h2>Analysis</h2>
    <p>This was a prospective study involving 154 consecutive cardiac arrest victims in a single county in the United States of America with a population of approximately 160,000 people. Between 1991-1995, patients over the age of 18 who had a cardiac arrest with associated PEA were included in the study. Patients who had arrests due to hypothermia, poisoning, trauma, hypovolemia, tension pneumothorax, and cardiac tamponade were excluded. All patients received basic ACLS interventions including intubation by the paramedics. After intubation, etCO2 was measured continuously during the remaining resuscitation. A final etCO2 measurement was taken after 20 minutes of resuscitation, and a level of 10 mmHg or less was determined as the cutoff for patients who would likely not survive. Groups were compared using a Wilcoxon-rank sum test.</p>
    <p>150 patients were included in the final patient population. In an initial analysis looking at survival to hospital admission (ROSC), there were no differences between survivors and non-survivors in the initial etCO2 level recorded (non-survivors 12.3Â±6.9; survivors 12.2Â±4.6), although survivors had a statistically increased etCO2 at 20 minutes (non-survivors 4.4Â±2.9; survivors 32.8Â±7.4) (table 1, page 302). An etCO2 of 10mmHg or less had a 100% sensitivity, specificity, positive predictive value and negative predictive value in determining who would die prior to admission to the hospital. Of the patients who survived to hospital admission, 20-minute etCO2 levels failed to predict who would survive to hospital discharge (non-survivors 31.8Â±7.3; survivors 34.0Â±7.7). The authors concluded that an etCO2 less than 10mmHg measured after 20 minutes of CPR could accurately predict who would survive to hospital admission, but was not predictive of who would go on to survive to discharge. The authors recommended that in patients with an etCO2 less than 10mm Hg after 20 minutes of CPR a resuscitation could be terminated.</p>
    <h2>Strength</h2>
    <p>Although the results of this paper have gained widespread usage within the emergency medicine and critical care fields, there are a number of methodological weaknesses. The first limitation of this study is its lack of blinding. Although patients were enrolled prospectively, it is unclear from the methods whether there was any blinding of the purpose of the study, or of the a priori determined 10mm Hg etCO2 cutoff, to the paramedics enrolling and treating patients. This lack of blinding could result in significant response bias, potentially altering providers interventions based upon the etCO2. A second weakness is that the exclusion criteria used for this study greatly limited its application during a resuscitation. Many patients were excluded based upon the mechanism of the arrest, but there was no explanation for why these criteria were used. During a resuscitation it is often difficult to know the exact cause of the arrest, and the extensive exclusion criteria may limit the cases that these results can be applied to.</p>
    <h2>Impact</h2>
    <p>Considerable effort has been focused on determining who will survive from a cardiac arrest in order to appropriately focus interventions on salvageable patients. Epidemiological studies have identified that the use of bystander CPR, having a shockable rhythm upon presentation, and the use of a defibrillator are all predictive of improved outcomes, but prior to the publication of this manuscript there were few measurable variables that could be used during a resuscitation to predict which patients were not going to survive. This was the first study to suggest that low etCO2 was a sensitive and specific predictor of patients who would not undergo ROSC, and suggested that it be used in determining when a resuscitation could be terminated. Since the publication of this paper further research has demonstrated that these same concepts can be applied to patients with all initial rhythms (Kolar et al, Salen et al). These concepts have now been widely adopted and are described in emergency medicine textbooks (Rosen\'s).</p>',
    
    "<h2>Takehome</h2>
    <p>Pariatur messenger bag tote bag tousled, officia Tonx dolore Marfa labore. Twee non messenger bag id sapiente fanny pack. Incididunt Truffaut seitan before they sold out, meh mixtape pug pour-over 8-bit readymade. Ugh tattooed mixtape flexitarian McSweeneys umami trust fund, aesthetic fingerstache minim Helvetica delectus. Butcher bitters PBR&B bespoke gastropub, polaroid raw denim odio occupy chambray veniam authentic meh id letterpress. Swag wolf Odd Future beard, duis aute American Apparel non officia ethical gluten-free reprehenderit. Reprehenderit High Life polaroid, Brooklyn vero wayfarers ea exercitation.</p>
    <h2>Background</h2>
    <p>Gastropub sunt Godard, polaroid in blog farm-to-table organic iPhone cillum butcher officia food truck. Id salvia voluptate, post-ironic drinking vinegar polaroid DIY put a bird on it chia. Distillery literally pickled artisan est voluptate. Placeat food truck PBR&B, enim velit gentrify cred Blue Bottle. Aliquip XOXO velit reprehenderit, aesthetic brunch seitan nostrud Carles skateboard kitsch readymade. Sed ullamco swag sriracha. Mollit pork belly street art ugh tempor semiotics.</p>
    <h2>Analysis</h2>
    <p>Next level kogi 90's leggings. Synth pug consequat drinking vinegar, eu beard cred biodiesel Schlitz culpa laborum street art bicycle rights. Pickled organic pop-up post-ironic 8-bit. Wolf craft beer elit, skateboard qui banjo labore food truck laboris salvia Bushwick. Culpa fanny pack enim, letterpress pug tattooed VHS banjo art party master cleanse post-ironic aliquip. Tofu anim bicycle rights Blue Bottle Truffaut Austin. Swag locavore asymmetrical, ugh esse gluten-free id artisan actually.</p>
    <p>150 patients were included in the final patient population. In an initial analysis looking at survival to hospital admission (ROSC), there were no differences between survivors and non-survivors in the initial etCO2 level recorded (non-survivors 12.3Â±6.9; survivors 12.2Â±4.6), although survivors had a statistically increased etCO2 at 20 minutes (non-survivors 4.4Â±2.9; survivors 32.8Â±7.4) (table 1, page 302). An etCO2 of 10mmHg or less had a 100% sensitivity, specificity, positive predictive value and negative predictive value in determining who would die prior to admission to the hospital. Of the patients who survived to hospital admission, 20-minute etCO2 levels failed to predict who would survive to hospital discharge (non-survivors 31.8Â±7.3; survivors 34.0Â±7.7). The authors concluded that an etCO2 less than 10mmHg measured after 20 minutes of CPR could accurately predict who would survive to hospital admission, but was not predictive of who would go on to survive to discharge. The authors recommended that in patients with an etCO2 less than 10mm Hg after 20 minutes of CPR a resuscitation could be terminated.</p>
    <h2>Strength</h2>
    <p>Tempor assumenda occaecat et ad officia, meggings Banksy fashion axe food truck labore PBR&B. Slow-carb lo-fi Truffaut, forage fap PBR occaecat shabby chic cardigan Pinterest gentrify pour-over tempor. Est church-key photo booth, excepteur Cosby sweater viral lo-fi sed Pinterest locavore stumptown next level aliquip butcher. Odd Future Austin flannel nihil enim in. Vegan incididunt deserunt, aliqua Vice farm-to-table scenester cray messenger bag excepteur. Nesciunt cardigan you probably haven't heard of them, reprehenderit do cillum in bicycle rights jean shorts odio ea et McSweeney's officia. Polaroid artisan narwhal Shoreditch Neutra.</p>
    <h2>Impact</h2>
    <p>Considerable effort has been focused on determining who will survive from a cardiac arrest in order to appropriately focus interventions on salvageable patients. Epidemiological studies have identified that the use of bystander CPR, having a shockable rhythm upon presentation, and the use of a defibrillator are all predictive of improved outcomes, but prior to the publication of this manuscript there were few measurable variables that could be used during a resuscitation to predict which patients were not going to survive. This was the first study to suggest that low etCO2 was a sensitive and specific predictor of patients who would not undergo ROSC, and suggested that it be used in determining when a resuscitation could be terminated. Since the publication of this paper further research has demonstrated that these same concepts can be applied to patients with all initial rhythms (Kolar et al, Salen et al). These concepts have now been widely adopted and are described in emergency medicine textbooks (Rosen\'s).</p>",
        
    "<h2>Takehome</h2>
    <p>Sriracha jean shorts dolor, assumenda kitsch ea in tattooed forage. PBR reprehenderit gastropub squid. Terry Richardson placeat voluptate church-key lomo readymade. Scenester Helvetica slow-carb, reprehenderit McSweeney's Pinterest ethical four loko +1 quis Bushwick pop-up readymade. Fingerstache before they sold out Carles fap deep v. Hoodie commodo 3 wolf moon mixtape pug. Id Pitchfork accusamus, craft beer labore art party tattooed meh messenger bag mumblecore meggings sustainable occaecat.</p>
    <h2>Background</h2>
    <p>Bicycle rights sriracha whatever food truck Terry Richardson gentrify eiusmod, magna 8-bit organic officia fugiat next level. Vice excepteur seitan eiusmod Wes Anderson, authentic semiotics veniam pug polaroid. Roof party nihil lo-fi, scenester seitan exercitation pug ennui letterpress blog Pinterest incididunt aesthetic placeat. Pour-over farm-to-table XOXO Shoreditch biodiesel, hoodie street art fanny pack commodo quis. Do eu kitsch Wes Anderson Intelligentsia nisi. Enim squid tote bag, pariatur next level 3 wolf moon narwhal asymmetrical you probably haven't heard of them. American Apparel esse flannel bicycle rights, sriracha quinoa wolf master cleanse skateboard sunt butcher nesciunt deep v selvage.</p>
    <h2>Analysis</h2>
    <p>Minim aesthetic asymmetrical, tote bag sartorial distillery PBR&B. Esse Portland labore, occaecat paleo next level kitsch. 90's mollit roof party viral, artisan freegan sed mustache butcher church-key et enim mixtape consequat. Elit et distillery selvage, aute hoodie fugiat flexitarian mlkshk hella Shoreditch church-key bespoke. Selfies et polaroid cupidatat. You probably haven't heard of them ex esse photo booth twee. Mlkshk velit sed sartorial swag non.</p>
    <h2>Strength</h2>
    <p>Tempor assumenda occaecat et ad officia, meggings Banksy fashion axe food truck labore PBR&B. Slow-carb lo-fi Truffaut, forage fap PBR occaecat shabby chic cardigan Pinterest gentrify pour-over tempor. Est church-key photo booth, excepteur Cosby sweater viral lo-fi sed Pinterest locavore stumptown next level aliquip butcher. Odd Future Austin flannel nihil enim in. Vegan incididunt deserunt, aliqua Vice farm-to-table scenester cray messenger bag excepteur. Nesciunt cardigan you probably haven't heard of them, reprehenderit do cillum in bicycle rights jean shorts odio ea et McSweeney's officia. Polaroid artisan narwhal Shoreditch Neutra.</p>
    <h2>Impact</h2>
    <p>Dolor nihil nulla, fashion axe Banksy eiusmod disrupt ethical High Life fap. Assumenda mumblecore occaecat fingerstache, velit laboris stumptown gluten-free cornhole eiusmod slow-carb brunch. Etsy ex church-key, asymmetrical bicycle rights artisan anim deep v roof party yr Pitchfork. Meggings ethical Bushwick aesthetic, biodiesel ennui sapiente cray ad magna nulla assumenda nisi. Eu literally aliquip sint non hella fap post-ironic pork belly twee. Ad nesciunt Schlitz aliqua, nisi hoodie meh minim post-ironic food truck organic Echo Park roof party. Mlkshk est aliquip, et trust fund fanny pack tousled mollit meh pariatur nisi enim mumblecore.</p>",

    "<h2>Takehome</h2>
    <p>Sustainable eiusmod locavore, excepteur authentic ethnic sriracha jean shorts aliquip American Apparel ennui commodo keffiyeh twee. Banksy plaid next level proident, quis reprehenderit cillum asymmetrical High Life Portland Wes Anderson elit blog Marfa. Aute butcher ethnic, banjo distillery fashion axe bitters hella American Apparel trust fund. Photo booth labore whatever literally irure ethical. Duis Intelligentsia id minim tousled. Austin nostrud pug, Neutra ethnic keytar readymade asymmetrical mollit reprehenderit ea in fingerstache irony. Echo Park meggings meh, flexitarian fingerstache stumptown organic trust fund.</p>
    <h2>Background</h2>
    <p>Ennui hoodie Bushwick, hashtag try-hard pickled jean shorts swag sunt. Fanny pack disrupt anim, irure vegan Pitchfork slow-carb gentrify. Before they sold out Schlitz Terry Richardson art party Carles incididunt vegan. Pour-over artisan master cleanse anim viral, cray kale chips duis eu sed. Est id lomo enim consequat Etsy. Stumptown mustache VHS lo-fi velit, 3 wolf moon consequat. Magna DIY paleo velit.</p>
    <h2>Analysis</h2>
    <p>Mustache mlkshk 90's gastropub. Laboris keffiyeh wayfarers ethical. Helvetica dreamcatcher pop-up, Vice Schlitz do messenger bag. Sed nostrud pariatur hoodie chia, do aute small batch chillwave art party pickled nesciunt. Odd Future keffiyeh Terry Richardson biodiesel, Tumblr Thundercats gentrify sartorial kogi photo booth slow-carb fashion axe ea laborum. Fingerstache readymade ad, slow-carb ex Austin vegan photo booth adipisicing vinyl assumenda organic Marfa hella aute. Exercitation aliqua letterpress vero, before they sold out laboris Banksy.</p>
    <h2>Strength</h2>
    <p>Yr sriracha ugh Brooklyn, forage pork belly delectus sed nesciunt literally meh. Commodo twee forage asymmetrical Brooklyn. Retro VHS adipisicing, hoodie chambray irony gentrify blog wolf nostrud tote bag voluptate keytar Bushwick pork belly. Voluptate bespoke Austin eiusmod, Tumblr leggings chillwave put a bird on it sint yr. Accusamus 3 wolf moon et vinyl Terry Richardson viral, yr pickled veniam before they sold out tattooed. Artisan keffiyeh 3 wolf moon ad Wes Anderson authentic. Try-hard officia street art lo-fi.</p>
    <h2>Impact</h2>
    <p>Dolor nihil nulla, fashion axe Banksy eiusmod disrupt ethical High Life fap. Assumenda mumblecore occaecat fingerstache, velit laboris stumptown gluten-free cornhole eiusmod slow-carb brunch. Etsy ex church-key, asymmetrical bicycle rights artisan anim deep v roof party yr Pitchfork. Meggings ethical Bushwick aesthetic, biodiesel ennui sapiente cray ad magna nulla assumenda nisi. Eu literally aliquip sint non hella fap post-ironic pork belly twee. Ad nesciunt Schlitz aliqua, nisi hoodie meh minim post-ironic food truck organic Echo Park roof party. Mlkshk est aliquip, et trust fund fanny pack tousled mollit meh pariatur nisi enim mumblecore.</p>" 
);


// ARTICLES
$urls = array(  
    '2386557',
    'http://www.ncbi.nlm.nih.gov/pubmed/12930925',
    'http://www.ncbi.nlm.nih.gov/pubmed/11856794',
    'http://www.ncbi.nlm.nih.gov/pubmed/20924010',
    'http://www.ncbi.nlm.nih.gov/pubmed/1309182',
    'http://www.ncbi.nlm.nih.gov/pubmed/6121481',
    'http://www.ncbi.nlm.nih.gov/pubmed/11907287',
    'http://www.ncbi.nlm.nih.gov/pubmed/18596271',
    'http://www.ncbi.nlm.nih.gov/pubmed/8204123',
    '10685714',
    '11856793',
    '2899772',
    'http://www.ncbi.nlm.nih.gov/pubmed/14411374',
    '10486418',
    'http://www.ncbi.nlm.nih.gov/pubmed/9233867',
    'http://www.ncbi.nlm.nih.gov/pubmed/10770981',
    'http://www.ncbi.nlm.nih.gov/pubmed/8559200',
    'http://www.ncbi.nlm.nih.gov/pubmed/9171064',
    '15306666',
    '1522840',
    '12571258',
    '15657322',
    'http://www.ncbi.nlm.nih.gov/pubmed/2233916',
    '11389488',
    'http://www.ncbi.nlm.nih.gov/pubmed/12432041',
    '15726056',
    '17192537',
    '11794169',
    '2368794',
    '12724479',
    '2910162',
    '9971864',
    '20179283',
    '15286537',
    '1548720',
    '10793162',
    '7651472',
    '8995086',
    '18318689',
    '14523886',
    '13366553',
    '16549851',
    'http://www.ncbi.nlm.nih.gov/pubmed/7752753',
    'http://www.ncbi.nlm.nih.gov/pubmed/11453709',
    '14507948',
    '11103723'
);

// parse and store articles with dummy writeups for the first 10
foreach ($urls as $url) {
    $article = PMParser::getArticleFromURL($url);
    $article['type'] = 'article';
    $articleBean = R::graph($article);
    
    // tag article with random tag
    $num = array_rand($tags, 1);
    R::tag($articleBean, array($tags[$num]));
    
    // add some votes
    $updown = array('voteup', 'votedown');
    
    $vote1              = R::dispense('votedown');
    $vote1->user        = $users[0];
    $vote1->article     = $articleBean;
    $vote1->timestamp   = date("Y-m-d H:i:s");
    $vote1_id           = R::store($vote1);

    $vote2              = R::dispense('voteup');
    $vote2->user        = $users[2];
    $vote2->article     = $articleBean;
    $vote2->timestamp   = date("Y-m-d H:i:s");
    $vote2_id           = R::store($vote2);
    
    // random votes from me for testing of voting
    $shouldvote         = rand(1,9);
    if($shouldvote < 4) {
        $vote3              = R::dispense($updown[rand(0,1)]);
        $vote3->user        = $users[1];
        $vote3->article     = $articleBean;
        $vote3->timestamp   = date("Y-m-d H:i:s");
        $vote3_id           = R::store($vote3);
    }
    
    // add some comments
    $comments           = R::dispense('comment', rand(2,4));
    foreach($comments as $comment) {
        $comment->article = $articleBean;
        $comment->user = $users[rand(0,2)];
        $comment->timestamp = date("Y-m-d H:i:s");
        $comment->text = $teasers[array_rand($teasers, 1)];
        $comment_id = R::store($comment);
        
        $like = R::dispense('commentlike');
        $like->user = $users[rand(0,3)];
        $like->comment = $comment;
        $like->timestamp = date("Y-m-d H:i:s");
        $like_id = R::store($like);
        
        
    };
    
    // add writeups to first 10 articles
    if ($articleBean->id < 11) {
        $wu = R::dispense('review');
        $wu->article = $articleBean;
        $wu->draft = 'false';
        $wu->user = $users[rand(1,2)];
        $wu->published = date("Y-m-d H:i:s", mktime(0,0,0,10,$articleBean->id,2013));
        $num = array_rand($writeups, 1);
        $wu->teaser = $teasers[$num];
        $wu->text = $writeups[$num];
        $wu_id = R::store($wu);
    }

    // save the article
    $article_id = R::store($articleBean);    
}

echo "Parsed articles with writeups, votes, comments<br />";


// BLOG
$blog               = R::dispense('blog');
$blog->user         = $users[1];
$blog->teaser       = $teasers[array_rand($teasers, 1)];
$blog->text         = $writeups[array_rand($writeups, 1)];
$blog->draft        = FALSE;
$blog->published    = date("Y-m-d H:i:s");
$blog_id            = R::store($blog);

$bloglike           = R::dispense('bloglike');
$bloglike->user     = $users[rand(0,3)];
$bloglike->blog     = $blog;
$bloglike->timestamp = date("Y-m-d H:i:s");
$bloglike_id        = R::store($bloglike);

echo "Made a blog entry with a 'like'<br />";


// URLS FOR ALL THE ARTICLES
$all_urls = array(
    '2386557',
    'http://www.ncbi.nlm.nih.gov/pubmed/12930925',
    'http://www.ncbi.nlm.nih.gov/pubmed/11856794',
    'http://www.ncbi.nlm.nih.gov/pubmed/20924010',
    'http://www.ncbi.nlm.nih.gov/pubmed/1309182',
    'http://www.ncbi.nlm.nih.gov/pubmed/6121481',
    'http://www.ncbi.nlm.nih.gov/pubmed/11907287',
    'http://www.ncbi.nlm.nih.gov/pubmed/18596271',
    'http://www.ncbi.nlm.nih.gov/pubmed/8204123',
    '10685714',
    '11856793',
    '2899772',
    'http://www.ncbi.nlm.nih.gov/pubmed/14411374',
    '10486418',
    'http://www.ncbi.nlm.nih.gov/pubmed/9233867',
    'http://www.ncbi.nlm.nih.gov/pubmed/10770981',
    'http://www.ncbi.nlm.nih.gov/pubmed/8559200',
    'http://www.ncbi.nlm.nih.gov/pubmed/9171064',
    '15306666',
    '1522840',
    '12571258',
    '15657322',
    'http://www.ncbi.nlm.nih.gov/pubmed/2233916',
    '11389488',
    'http://www.ncbi.nlm.nih.gov/pubmed/12432041',
    '15726056',
    '17192537',
    '11794169',
    '2368794',
    '12724479',
    '2910162',
    '9971864',
    '20179283',
    '15286537',
    '1548720',
    '10793162',
    '7651472',
    '8995086',
    '18318689',
    '14523886',
    '13366553',
    '16549851',
    'http://www.ncbi.nlm.nih.gov/pubmed/7752753',
    'http://www.ncbi.nlm.nih.gov/pubmed/11453709',
    '14507948',
    '11103723',
    'http://www.ncbi.nlm.nih.gov/pubmed/19091393',
    '2278545',
    '7935634',
    '10789667',
    '8189470',
    '12169503',
    'http://www.ncbi.nlm.nih.gov/pubmed/2587134',
    '15385657',
    '15123483',
    '9113963',
    '10390264',
    '14757604',
    '19758692',
    '12359784',
    '1881722',
    '9606243',
    '9685461',
    '23062548',
    '16968847',
    '15459622',
    '10080845',
    '11172179',
    '11525706',
    '2859819',
    '3706169',
    '4100436',
    '12362006',
    '3059186',
    'http://www.ncbi.nlm.nih.gov/pubmed/18396107',
    'http://www.ncbi.nlm.nih.gov/pubmed/20044534',
    '11401607',
    '11742046',
    'http://www.ncbi.nlm.nih.gov/pubmed/11147987',
    '17258668',
    '7477192',
    'http://www.ncbi.nlm.nih.gov/pubmed/12239256',
    '4136544',
    'http://www.ncbi.nlm.nih.gov/pubmed/7893293',
    'http://www.ncbi.nlm.nih.gov/pubmed/10683058',
    '15258866',
    '10891517',
    '12131380',
    '10891516',
    '9498497',
    '16374287',
    '2332918',
    '11586156',
    '1554175',
    '8114236',
    '9403421',
    'http://www.ncbi.nlm.nih.gov/pubmed/11356436',
    '14695411',
    '16738268'
);