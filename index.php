<?php
session_start();

$admin_password = 'password123';
$jsonFile = __DIR__ . '/data.json';

// Initialisation des données par défaut
$defaultData = [
    'marquee_text' => 'Flash Info: Nos activités reprennent le 15 Septembre 2025. Restez connectés !',
    'hero_title' => 'Bâtir un avenir de Foi et de Lumière',
    'hero_subtitle' => "Notre mission est d'éclairer le monde avec les valeurs de l'Évangile et de former des leaders engagés.",
    'services_title' => 'Nos Services',
    'activities_title' => 'Programme des Activités',
    'news_ads_title' => 'Actualités & Annonces',
    'videos_title' => 'Nos vidéos',
    'publications_title' => 'Nos Publications',
    'contact_title' => 'Contactez-nous',
    'services' => [
        1 => ['title' => 'Formation des Jeunes', 'description' => 'Des programmes complets pour équiper la jeunesse.', 'image' => 'https://picsum.photos/400/300/?random=2'],
        2 => ['title' => 'Évangélisation et Mission', 'description' => 'Participez à nos campagnes de sensibilisation et de partage.', 'image' => 'https://picsum.photos/400/300/?random=3'],
        3 => ['title' => 'Œuvres Sociales', 'description' => 'Aidez-nous à soutenir les communautés les plus vulnérables.', 'image' => 'https://picsum.photos/400/300/?random=4'],
    ],
    'activities' => [
        1 => ['title' => 'Retraite Spirituelle Annuelle', 'description' => 'Venez vivre un moment de reconnexion et de prière.', 'date' => '20-22 Octobre 2025', 'image' => 'https://picsum.photos/400/300/?random=5'],
        2 => ['title' => 'Camp d\'Été pour les Jeunes', 'description' => 'Activités ludiques et éducatives pour la jeunesse.', 'date' => '10-15 Août 2025', 'image' => 'https://picsum.photos/400/300/?random=6'],
        3 => ['title' => 'Conférence Nationale', 'description' => 'Échangeons sur les défis et opportunités de notre mission.', 'date' => '1-3 Décembre 2025', 'image' => 'https://picsum.photos/400/300/?random=7'],
    ],
    'news_ads' => [
        1 => ['title' => 'Lancement de notre nouveau programme de mentorat', 'content' => "Un nouveau chapitre pour l'accompagnement de nos jeunes.", 'image' => 'https://picsum.photos/600/400/?random=8'],
        2 => ['title' => 'Report de la Conférence Régionale', 'content' => 'La Conférence Régionale est reportée au 25 novembre 2025.', 'image' => 'https://picsum.photos/600/400/?random=9'],
        3 => ['title' => 'Appel à bénévoles pour notre mission humanitaire', 'content' => 'Rejoignez-nous pour notre prochaine mission humanitaire.', 'image' => 'https://picsum.photos/600/400/?random=10'],
    ],
    'videos' => [
        1 => ['title' => 'Message de la Direction', 'embed' => 'https://www.youtube.com/embed/dQw4w9WgXcQ'],
        2 => ['title' => 'Témoignages de nos membres', 'embed' => 'https://www.youtube.com/embed/dQw4w9WgXcQ'],
    ],
    'publications' => [
        1 => ['title' => 'Rapport Annuel 2024', 'description' => "Résumé des activités et des accomplissements de l'année.", 'image' => 'https://picsum.photos/300/400/?random=11'],
        2 => ['title' => 'Magazine Semestriel', 'description' => 'Découvrez nos histoires, nos témoignages et nos projets.', 'image' => 'https://picsum.photos/300/400/?random=12'],
    ],
];

// Charger ou initialiser les données
if (file_exists($jsonFile)) {
    $data = json_decode(file_get_contents($jsonFile), true);
    if (!$data) $data = $defaultData;
} else {
    $data = $defaultData;
    file_put_contents($jsonFile, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

// Sauvegarde des données
function saveData($data, $jsonFile) {
    file_put_contents($jsonFile, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

// Gestion admin
if (isset($_POST['action']) && $_POST['action'] === 'login') {
    if ($_POST['password'] === $admin_password) {
        $_SESSION['admin_mode'] = true;
    }
    header("Location: index.php");
    exit;
}
if (isset($_GET['logout'])) {
    unset($_SESSION['admin_mode']);
    header('Location: index.php');
    exit();
}

// Édition de textes simples (exemple)
if (isset($_POST['action']) && $_POST['action'] === 'update_text' && isset($_SESSION['admin_mode'])) {
    $target = $_POST['target'];
    $newText = trim($_POST['text-content']);
    if ($target && isset($data[$target])) {
        $data[$target] = $newText;
        saveData($data, $jsonFile);
    }
    header('Location: index.php');
    exit();
}

// Ajout, édition, suppression d'éléments dynamiques
if (isset($_POST['action']) && isset($_SESSION['admin_mode'])) {
    $type = $_POST['type'] ?? null;
    if ($type && isset($data[$type])) {
        // Ajout
        if ($_POST['action'] === 'add' && !empty($_POST['item'])) {
            $nextId = (count($data[$type]) ? max(array_keys($data[$type])) + 1 : 1);
            $data[$type][$nextId] = $_POST['item'];
            saveData($data, $jsonFile);
        }
        // Modification
        if ($_POST['action'] === 'edit' && isset($_POST['id'], $_POST['item'])) {
            $id = intval($_POST['id']);
            $data[$type][$id] = $_POST['item'];
            saveData($data, $jsonFile);
        }
        // Suppression
        if ($_POST['action'] === 'delete' && isset($_POST['id'])) {
            $id = intval($_POST['id']);
            unset($data[$type][$id]);
            saveData($data, $jsonFile);
        }
    }
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($data['hero_title']) ?></title>
    <meta name="description" content="<?= htmlspecialchars($data['hero_subtitle']) ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            margin:0; font-family: 'Poppins',sans-serif;
            background: #f5f7f6;
            color: #222;
        }
        header {
            background:#1A5319;color:#fff;
            display:flex;flex-wrap:wrap;justify-content:space-between;align-items:center;
            padding:1rem 2rem;
            box-shadow:0 2px 6px rgba(0,0,0,0.07);
        }
        .logo {height:50px;}
        .admin-btn {
            background:#FFC107;color:#1A5319;
            border:none;padding:8px 16px;border-radius:4px;
            font-weight:bold;cursor:pointer;
        }
        .marquee {
            background:#FFC107;color:#1A5319;
            padding:10px 0;text-align:center;
            font-weight:bold;overflow:hidden;white-space:nowrap;
        }
        @keyframes scroll {0%{transform:translateX(100%);}100%{transform:translateX(-100%);}}
        .marquee span {display:inline-block;animation:scroll 15s linear infinite;}
        main {max-width:1100px;margin:2rem auto;padding:1rem;background:#fff;border-radius:10px;box-shadow:0 3px 12px rgba(0,0,0,0.06);}
        h1,h2,h3 {font-family:'Montserrat',sans-serif;color:#1A5319;}
        .section {margin-bottom:3rem;}
        .services,.activities,.news,.videos,.publications {
            display:grid;gap:1.2rem;
            grid-template-columns:repeat(auto-fit,minmax(250px,1fr));
        }
        .card {
            background:#f8fbf8;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,0.07);
            padding:1rem;display:flex;flex-direction:column;align-items:center;text-align:center;
        }
        .card img {width:100%;max-width:270px;height:160px;object-fit:cover;border-radius:8px;}
        .date {color:#FFC107;font-weight:bold;}
        footer {text-align:center;padding:1.5rem 0;color:#fff;background:#1A5319;margin-top:2rem;}
        /* Responsive */
        @media(max-width:800px){main{padding:.5rem;}header{padding:1rem .5rem;} .logo{height:38px;}}
        @media(max-width:600px){
            .services,.activities,.news,.videos,.publications{grid-template-columns:1fr;}
            h1{font-size:1.4rem;}
        }
        /* Admin mode */
        .admin-bar {background:#e6ffe6;color:#1A5319;text-align:center;padding:8px 0;font-weight:700;}
        .admin-edit {margin-top:10px;}
        .admin-edit button {margin-right:5px;}
        .admin-form {padding:1rem;background:#f2f2f2;border-radius:8px;max-width:320px;margin:20px auto;}
        .admin-form input,.admin-form textarea {width:100%;margin-bottom:10px;padding:8px;background:#fff;border:1px solid #ccc;border-radius:5px;}
        .admin-form label{font-weight:bold;}
    </style>
</head>
<body>
<?php if(isset($_SESSION['admin_mode']) && $_SESSION['admin_mode']): ?>
    <div class="admin-bar">
        Mode Administration activé | <a href="?logout" style="color:#1A5319;">Déconnexion</a>
    </div>
<?php endif; ?>
<header>
    <img src="https://picsum.photos/96/96" class="logo" alt="Logo">
    <div>
        <h1 style="margin:0;"><?= htmlspecialchars($data['hero_title']) ?></h1>
        <div><?= htmlspecialchars($data['hero_subtitle']) ?></div>
    </div>
    <?php if(isset($_SESSION['admin_mode']) && $_SESSION['admin_mode']): ?>
        <button class="admin-btn" onclick="document.getElementById('editTexts').style.display='block'">Éditer textes</button>
    <?php else: ?>
        <button class="admin-btn" onclick="document.getElementById('loginAdmin').style.display='block'">Admin</button>
    <?php endif; ?>
</header>
<div class="marquee">
    <span><?= htmlspecialchars($data['marquee_text']) ?></span>
</div>
<main>
    <!-- SERVICES -->
    <div class="section">
        <h2><?= htmlspecialchars($data['services_title']) ?></h2>
        <div class="services">
            <?php foreach($data['services'] as $id=>$s): ?>
                <div class="card">
                    <img src="<?= htmlspecialchars($s['image']) ?>" alt="">
                    <h3><?= htmlspecialchars($s['title']) ?></h3>
                    <div><?= htmlspecialchars($s['description']) ?></div>
                    <?php if(isset($_SESSION['admin_mode']) && $_SESSION['admin_mode']): ?>
                        <div class="admin-edit">
                            <form method="post" style="display:inline">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="type" value="services">
                                <input type="hidden" name="id" value="<?= $id ?>">
                                <button type="submit" class="admin-btn" style="background:#dc3545;color:#fff;">Suppr</button>
                            </form>
                            <button onclick="editItem('services',<?= $id ?>)" class="admin-btn">Éditer</button>
                        </div>
                    <?php endif;?>
                </div>
            <?php endforeach;?>
        </div>
        <?php if(isset($_SESSION['admin_mode']) && $_SESSION['admin_mode']): ?>
        <div class="admin-edit">
            <button onclick="addItem('services')" class="admin-btn">+ Ajouter un service</button>
        </div>
        <?php endif;?>
    </div>
    <!-- ACTIVITIES -->
    <div class="section">
        <h2><?= htmlspecialchars($data['activities_title']) ?></h2>
        <div class="activities">
            <?php foreach($data['activities'] as $id=>$a): ?>
                <div class="card">
                    <img src="<?= htmlspecialchars($a['image']) ?>" alt="">
                    <h3><?= htmlspecialchars($a['title']) ?></h3>
                    <div><?= htmlspecialchars($a['description']) ?></div>
                    <div class="date"><?= htmlspecialchars($a['date']) ?></div>
                    <?php if(isset($_SESSION['admin_mode']) && $_SESSION['admin_mode']): ?>
                        <div class="admin-edit">
                            <form method="post" style="display:inline">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="type" value="activities">
                                <input type="hidden" name="id" value="<?= $id ?>">
                                <button type="submit" class="admin-btn" style="background:#dc3545;color:#fff;">Suppr</button>
                            </form>
                            <button onclick="editItem('activities',<?= $id ?>)" class="admin-btn">Éditer</button>
                        </div>
                    <?php endif;?>
                </div>
            <?php endforeach;?>
        </div>
        <?php if(isset($_SESSION['admin_mode']) && $_SESSION['admin_mode']): ?>
        <div class="admin-edit">
            <button onclick="addItem('activities')" class="admin-btn">+ Ajouter une activité</button>
        </div>
        <?php endif;?>
    </div>
    <!-- NEWS -->
    <div class="section">
        <h2><?= htmlspecialchars($data['news_ads_title']) ?></h2>
        <div class="news">
            <?php foreach($data['news_ads'] as $id=>$n): ?>
                <div class="card">
                    <img src="<?= htmlspecialchars($n['image']) ?>" alt="">
                    <h3><?= htmlspecialchars($n['title']) ?></h3>
                    <div><?= htmlspecialchars($n['content']) ?></div>
                    <?php if(isset($_SESSION['admin_mode']) && $_SESSION['admin_mode']): ?>
                        <div class="admin-edit">
                            <form method="post" style="display:inline">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="type" value="news_ads">
                                <input type="hidden" name="id" value="<?= $id ?>">
                                <button type="submit" class="admin-btn" style="background:#dc3545;color:#fff;">Suppr</button>
                            </form>
                            <button onclick="editItem('news_ads',<?= $id ?>)" class="admin-btn">Éditer</button>
                        </div>
                    <?php endif;?>
                </div>
            <?php endforeach;?>
        </div>
        <?php if(isset($_SESSION['admin_mode']) && $_SESSION['admin_mode']): ?>
        <div class="admin-edit">
            <button onclick="addItem('news_ads')" class="admin-btn">+ Ajouter une actu</button>
        </div>
        <?php endif;?>
    </div>
    <!-- VIDEOS -->
    <div class="section">
        <h2><?= htmlspecialchars($data['videos_title']) ?></h2>
        <div class="videos">
            <?php foreach($data['videos'] as $id=>$v): ?>
                <div class="card">
                    <iframe width="100%" height="150" src="<?= htmlspecialchars($v['embed']) ?>" frameborder="0" allowfullscreen></iframe>
                    <h3><?= htmlspecialchars($v['title']) ?></h3>
                    <?php if(isset($_SESSION['admin_mode']) && $_SESSION['admin_mode']): ?>
                        <div class="admin-edit">
                            <form method="post" style="display:inline">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="type" value="videos">
                                <input type="hidden" name="id" value="<?= $id ?>">
                                <button type="submit" class="admin-btn" style="background:#dc3545;color:#fff;">Suppr</button>
                            </form>
                            <button onclick="editItem('videos',<?= $id ?>)" class="admin-btn">Éditer</button>
                        </div>
                    <?php endif;?>
                </div>
            <?php endforeach;?>
        </div>
        <?php if(isset($_SESSION['admin_mode']) && $_SESSION['admin_mode']): ?>
        <div class="admin-edit">
            <button onclick="addItem('videos')" class="admin-btn">+ Ajouter une vidéo</button>
        </div>
        <?php endif;?>
    </div>
    <!-- PUBLICATIONS -->
    <div class="section">
        <h2><?= htmlspecialchars($data['publications_title']) ?></h2>
        <div class="publications">
            <?php foreach($data['publications'] as $id=>$p): ?>
                <div class="card">
                    <img src="<?= htmlspecialchars($p['image']) ?>" alt="">
                    <h3><?= htmlspecialchars($p['title']) ?></h3>
                    <div><?= htmlspecialchars($p['description']) ?></div>
                    <?php if(isset($_SESSION['admin_mode']) && $_SESSION['admin_mode']): ?>
                        <div class="admin-edit">
                            <form method="post" style="display:inline">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="type" value="publications">
                                <input type="hidden" name="id" value="<?= $id ?>">
                                <button type="submit" class="admin-btn" style="background:#dc3545;color:#fff;">Suppr</button>
                            </form>
                            <button onclick="editItem('publications',<?= $id ?>)" class="admin-btn">Éditer</button>
                        </div>
                    <?php endif;?>
                </div>
            <?php endforeach;?>
        </div>
        <?php if(isset($_SESSION['admin_mode']) && $_SESSION['admin_mode']): ?>
        <div class="admin-edit">
            <button onclick="addItem('publications')" class="admin-btn">+ Ajouter une publication</button>
        </div>
        <?php endif;?>
    </div>
    <!-- CONTACT -->
    <div class="section">
        <h2><?= htmlspecialchars($data['contact_title']) ?></h2>
        <p>Adresse : 123 Rue de la Foi, Abidjan, Côte d'Ivoire<br>
        Téléphone : +225 00 00 00 00<br>
        Email : info@flambeaux-lumieres.ci</p>
    </div>
</main>
<footer>
    &copy; <?= date('Y') ?> Coordination Nationale Des Flambeaux-Lumières CMA CI. Site propulsé par PHP.
</footer>

<!-- MODALS Admin -->
<div id="loginAdmin" class="admin-form" style="display:none;position:fixed;top:20%;left:50%;transform:translateX(-50%);z-index:999;">
    <form method="post">
        <label>Mot de passe admin :</label>
        <input type="password" name="password" required>
        <input type="hidden" name="action" value="login">
        <button class="admin-btn" type="submit">Connexion</button>
        <button type="button" class="admin-btn" onclick="this.parentNode.parentNode.style.display='none'">Fermer</button>
    </form>
</div>
<div id="editTexts" class="admin-form" style="display:none;position:fixed;top:10%;left:50%;transform:translateX(-50%);z-index:999;">
    <form method="post">
        <label>Flash Info (marquee):</label>
        <input name="text-content" value="<?= htmlspecialchars($data['marquee_text']) ?>">
        <input type="hidden" name="target" value="marquee_text">
        <input type="hidden" name="action" value="update_text">
        <button class="admin-btn" type="submit">Enregistrer</button>
    </form>
    <form method="post">
        <label>Titre accueil :</label>
        <input name="text-content" value="<?= htmlspecialchars($data['hero_title']) ?>">
        <input type="hidden" name="target" value="hero_title">
        <input type="hidden" name="action" value="update_text">
        <button class="admin-btn" type="submit">Enregistrer</button>
    </form>
    <form method="post">
        <label>Sous-titre accueil :</label>
        <input name="text-content" value="<?= htmlspecialchars($data['hero_subtitle']) ?>">
        <input type="hidden" name="target" value="hero_subtitle">
        <input type="hidden" name="action" value="update_text">
        <button class="admin-btn" type="submit">Enregistrer</button>
    </form>
    <button type="button" class="admin-btn" onclick="this.parentNode.style.display='none'">Fermer</button>
</div>

<!-- MODAL ADD/EDIT Item (en JS) -->
<div id="itemModal" class="admin-form" style="display:none;position:fixed;top:12%;left:50%;transform:translateX(-50%);z-index:999;">
    <form id="itemForm" method="post">
        <div id="itemFields"></div>
        <input type="hidden" name="type" id="itemType">
        <input type="hidden" name="id" id="itemId">
        <input type="hidden" name="action" id="itemAction">
        <button class="admin-btn" type="submit">Valider</button>
        <button type="button" class="admin-btn" onclick="document.getElementById('itemModal').style.display='none'">Annuler</button>
    </form>
</div>

<script>
function addItem(type){
    showItemModal(type, null, {});
}
function editItem(type, id){
    // On récupère les valeurs dans le HTML
    var card = document.querySelector('[onclick="editItem(\\''+type+'\\','+id+')"]').closest('.card');
    var vals = {};
    if(type=='services'){
        vals.title = card.querySelector('h3').textContent.trim();
        vals.description = card.querySelector('div').textContent.trim();
        vals.image = card.querySelector('img').src;
    }else if(type=='activities'){
        vals.title = card.querySelector('h3').textContent.trim();
        vals.description = card.querySelectorAll('div')[1].textContent.trim();
        vals.date = card.querySelector('.date').textContent.trim();
        vals.image = card.querySelector('img').src;
    }else if(type=='news_ads'){
        vals.title = card.querySelector('h3').textContent.trim();
        vals.content = card.querySelector('div').textContent.trim();
        vals.image = card.querySelector('img').src;
    }else if(type=='videos'){
        vals.title = card.querySelector('h3').textContent.trim();
        vals.embed = card.querySelector('iframe').src;
    }else if(type=='publications'){
        vals.title = card.querySelector('h3').textContent.trim();
        vals.description = card.querySelectorAll('div')[1].textContent.trim();
        vals.image = card.querySelector('img').src;
    }
    showItemModal(type, id, vals);
}
function showItemModal(type, id, vals){
    var fields = '';
    if(type=='services'){
        fields = `<label>Titre</label><input name="item[title]" value="${vals.title||''}" required>
        <label>Description</label><textarea name="item[description]" required>${vals.description||''}</textarea>
        <label>Image (URL)</label><input name="item[image]" value="${vals.image||''}" required>`;
    }else if(type=='activities'){
        fields = `<label>Titre</label><input name="item[title]" value="${vals.title||''}" required>
        <label>Description</label><textarea name="item[description]" required>${vals.description||''}</textarea>
        <label>Date</label><input name="item[date]" value="${vals.date||''}" required>
        <label>Image (URL)</label><input name="item[image]" value="${vals.image||''}" required>`;
    }else if(type=='news_ads'){
        fields = `<label>Titre</label><input name="item[title]" value="${vals.title||''}" required>
        <label>Contenu</label><textarea name="item[content]" required>${vals.content||''}</textarea>
        <label>Image (URL)</label><input name="item[image]" value="${vals.image||''}" required>`;
    }else if(type=='videos'){
        fields = `<label>Titre</label><input name="item[title]" value="${vals.title||''}" required>
        <label>Intégration vidéo (URL YouTube embed)</label><input name="item[embed]" value="${vals.embed||''}" required>`;
    }else if(type=='publications'){
        fields = `<label>Titre</label><input name="item[title]" value="${vals.title||''}" required>
        <label>Description</label><textarea name="item[description]" required>${vals.description||''}</textarea>
        <label>Image (URL)</label><input name="item[image]" value="${vals.image||''}" required>`;
    }
    document.getElementById('itemFields').innerHTML = fields;
    document.getElementById('itemType').value = type;
    document.getElementById('itemId').value = id!==null?id:'';
    document.getElementById('itemAction').value = id!==null?'edit':'add';
    document.getElementById('itemModal').style.display='block';
}
</script>
</body>
</html>