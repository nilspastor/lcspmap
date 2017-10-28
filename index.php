<?php 

function chargerClasse($classname) {
	require $classname.'.php';
}

spl_autoload_register('chargerClasse'); 

session_start(); 

if (isset($_GET['deconnexion'])) { 
	session_destroy(); 
	header('Location: .'); 
	exit(); 
}

if (isset($_SESSION['perso'])) {
	$perso = $_SESSION['perso']; 
} 

include('conf.php');

$manager = new PersonnagesManager($db); 

if (isset($_POST['creer']) && isset($_POST['name'])) {
	$perso = new Personnage(array('charName' => $_POST['name']));

	if (!$perso->nomValide()) { 
		$message = 'Le nom choisi est invalide.'; 
		unset($perso); 
	}
	elseif ($manager->exists($perso->charName())) {
		$message = 'Le nom du personnage est déjà pris.';
		unset($perso);
	}
	else {
		$manager->add($perso);
	}
} 
elseif (isset($_POST['utiliser']) && isset($_POST['name'])) {
	if ($manager->exists($_POST['name'])) {
		$perso = $manager->get($_POST['name']);
	}
	else {
		$message = 'Ce personage n\'existe pas !';
	}
}
elseif (isset($_GET['frapper'])) {
	if (!isset($perso)) {
		$message = 'Merci de créer un personnage ou de vous identifier.';
	}
	else {
		if (!$manager->exists((int) $_GET['frapper'])) {
			$manager = 'Le personnage que vous voulez frapper n\'existe pas !';
		}
		else {
			$persoAFrapper = $manager->get((int) $_GET['frapper']);

			$retour = $perso->frapper($persoAFrapper);

			switch($retour) {
				case Personnage::CEST_MOI : 
				  $message = 'Mais... pourquoi voulez-vous vous frapper ????';
				  break;

				case Personnage::PERSONNAGE_FRAPPE : 
				  $message = 'Le personnage a bien été frappé !';

				  $manager->update($perso);
				  $manager->update($persoAFrapper);

				  break;

				case Personnage::PERSONNAGE_KO : 
				  $message = 'Vous avez mis K.O ce personnage !';

				  $manager->update($perso);
				  $manager->knockout($persoAFrapper);

				  break;
			}
		}
	}
}
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Les Contes sans Plume - Carte</title>
  <link rel="stylesheet" href="css/lcsp.css">
</head>
<body>
	<p>Nombre de personnages créés : <?php echo $manager->count(); ?></p>
<?php
if (isset($message)) {
	echo '<p>', $message, '</p>';
}

if (isset($perso)) {
?>
	<p><a href="?deconnexion=1">Déconnexion</a></p>
	<fieldset>
		<legend>Mes informations</legend>
		<p>
			Nom : <?php echo htmlspecialchars($perso->charName()); ?><br />
			Points de vie : <?php echo $perso->charHp(); ?><br />
            Ko : <?php echo $perso->charKo(); ?>
		</p>
	</fieldset>

	<fieldset>
		<legend>Qui frapper ?</legend>
		<p>
<?php 
$persos = $manager->getList($perso->charName());

if (empty($persos)) {
	echo 'Personne à frapper !';
}
else {
	foreach ($persos as $unPerso) {
		echo '<a href="?frapper=', $unPerso->charId(), '">', htmlspecialchars($unPerso->charName()), '</a> (PdV : ', $unPerso-> charHp(), ')<br />';
	}
}
?>
		</p>
	</fieldset>
<?php
}
else {
?>
	<form action="" method="post">
		<p>
			Nom : <input type="text" name="name" maxlenght="30" /> 
		Mot de passe : <input type="password" name="password" maxlenght="25" />
		<input type="submit" value="Créer ce personnage" name="creer" />
		<input type="submit" value="Utiliser ce personnage" name="utiliser" /></p>
	</form>		
<?php	
}
?>
</body>
</html>
<?php
if (isset($perso)) {
	$_SESSION['perso'] = $perso;
}