<?php 

class PersonnagesManager {
	
	private $_db;

	public function __construct($db) {
		$this->setDb($db);
	}

	public function add(Personnage $perso) {
		$q = $this->_db->prepare('INSERT INTO lcsp2017_chars SET charName = :charName');
		$q->bindValue(':charName', $perso->charName());
		$q->execute();

		$perso->hydrate(array(
			'charId' => $this->_db->lastInsertId(),
			'charHp' => 100
		));
	}

	public function count() {
		return $this->_db->query('SELECT COUNT(*) FROM lcsp2017_chars')->fetchColumn();
	}

	public function knockout(Personnage $perso) {
		$q = $this->_db->prepare('UPDATE lcsp2017_chars SET charHp = :charHp, charKo = :charKo WHERE charId = :charId');
		$q->bindValue(':charHp', $perso->charHp(), PDO::PARAM_INT);
		$q->bindValue(':charKo', 1, PDO::PARAM_INT);
		$q->bindValue(':charId', $perso->charId(), PDO::PARAM_INT);
		$q->execute();
	}

	public function exists($info) {
		if (is_int($info)) {
			return (bool) $this->_db->query('SELECT COUNT(*) FROM lcsp2017_chars WHERE charId = '.$info)->fetchColumn();
		}
		$q = $this->_db->prepare('SELECT COUNT(*) FROM lcsp2017_chars WHERE charName = :charName');
		$q->execute(array(':charName' => $info));

		return (bool) $q->fetchColumn();
	}

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

	public function getList($charName) {
		$persos = array();

		$q = $this->_db->prepare('SELECT charId, charName, charHp FROM lcsp2017_chars WHERE charName <> :charName AND charKo = 0 ORDER BY charName');
		$q->execute(array(':charName' => $charName));

		while ($donnees = $q->fetch(PDO::FETCH_ASSOC)) {
			$persos[] = new Personnage($donnees);
		}

		return $persos;
	}

	public function update(Personnage $perso) {
		$q = $this->_db->prepare('UPDATE lcsp2017_chars SET charHp = :charHp WHERE charId = :charId');

		$q->bindValue(':charHp', $perso->charHp(), PDO::PARAM_INT);
		$q->bindValue(':charId', $perso->charId(), PDO::PARAM_INT);

		$q->execute();
	}

	public function setDB(PDO $db) {
		$this->_db = $db;
	}
}