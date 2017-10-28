<?php 


// Classe en PHP Objet qui manage la classe Personnage
class PersonnagesManager {
	
	// Déclaration privée de l'attribut $_db (innacessible directement, mais par la méthode publique setDb)
	private $_db;

	// Construction auquel on passe $db (voir conf.php)
	public function __construct($db) {
		$this->setDb($db);
	}

	// Méthode ajout d'un personnage
	public function add(Personnage $perso) {
		$q = $this->_db->prepare('INSERT INTO lcsp2017_chars SET charName = :charName');
		$q->bindValue(':charName', $perso->charName());
		$q->execute();

		$perso->hydrate(array(
			'charId' => $this->_db->lastInsertId(),
			'charHp' => 100
		));
	}

	// Méthode qui compte les personnages présents sur la map
	public function count() {
		return $this->_db->query('SELECT COUNT(*) FROM lcsp2017_chars')->fetchColumn();
	}

	// Méthode qui met à jour la base de donnée si un personnage tombe K.O.
	public function knockout(Personnage $perso) {
		$q = $this->_db->prepare('UPDATE lcsp2017_chars SET charHp = :charHp, charKo = :charKo WHERE charId = :charId');
		$q->bindValue(':charHp', $perso->charHp(), PDO::PARAM_INT);
		$q->bindValue(':charKo', 1, PDO::PARAM_INT);
		$q->bindValue(':charId', $perso->charId(), PDO::PARAM_INT);
		$q->execute();
	}

	// Méthode vérifiant l'existence d'un personnage 
	public function exists($info) {
		if (is_int($info)) {
			return (bool) $this->_db->query('SELECT COUNT(*) FROM lcsp2017_chars WHERE charId = '.$info)->fetchColumn();
		}
		$q = $this->_db->prepare('SELECT COUNT(*) FROM lcsp2017_chars WHERE charName = :charName');
		$q->execute(array(':charName' => $info));

		return (bool) $q->fetchColumn();
	}

	// Méthode qui cherche l'ID du personnage a frapper (index.php)
	public function get($info) {
		if (is_int($info)) {
			$q = $this->_db->query('SELECT charId, charName, charHp FROM lcsp2017_chars WHERE charId = '.$info);
			$donnees = $q->fetch(PDO::FETCH_ASSOC);

			return new Personnage($donnees);
		}
		else {
			$q = $this->_db->prepare('SELECT charId, charName, charHp, charKo FROM lcsp2017_chars WHERE charName = :charName');
			$q->execute(array(':charName' => $info));

			return new Personnage($q->fetch(PDO::FETCH_ASSOC));
		}
	}

	// Méthode qui liste les personnages pour les afficher sur la map
	public function getList($charName) {
		$persos = array();

		$q = $this->_db->prepare('SELECT charId, charName, charHp FROM lcsp2017_chars WHERE charName <> :charName AND charKo = 0 ORDER BY charName');
		$q->execute(array(':charName' => $charName));

		while ($donnees = $q->fetch(PDO::FETCH_ASSOC)) {
			$persos[] = new Personnage($donnees);
		}

		return $persos;
	}

	// Mise à jour des données d'un personnage
	public function update(Personnage $perso) {
		$q = $this->_db->prepare('UPDATE lcsp2017_chars SET charHp = :charHp WHERE charId = :charId');

		$q->bindValue(':charHp', $perso->charHp(), PDO::PARAM_INT);
		$q->bindValue(':charId', $perso->charId(), PDO::PARAM_INT);

		$q->execute();
	}

	// Méthode publique pour accéder à $db
	public function setDB(PDO $db) {
		$this->_db = $db;
	}
}
