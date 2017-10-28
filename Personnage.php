<?php


// Classe en PHP Objet qui représente le personnage et ses caractéristiques
class Personnage {

	// Déclaration privée des attributs (ils ne sont pas accessibles directement)	
	private $_charHp;
	private $_charId;
	private $_charName;
	private $_charKo;

	// Déclaration des constantes de la classe Personnage
	const CEST_MOI = 1;
	const PERSONNAGE_KO = 2;
	const PERSONNAGE_FRAPPE = 3;

	// Méthode d'hydratation de l'objet Personnage passée dans le constructeur
	public function __construct(array $donnees) {
		$this->hydrate($donnees);
	}

	// Méthode pour frapper un personnage, si c'est moi return constante CEST_MOI
	public function frapper(Personnage $perso) {
		if ($perso->charId() == $this->_charId) {
			return self::CEST_MOI;
		}
		return $perso->recevoirDegats();
	}

	// Méthode d'hydratation
	public function hydrate(array $donnees) {
		foreach ($donnees as $key => $value) {
			$method = 'set'.ucfirst($key);
			if (method_exists($this, $method)) {
				$this->$method($value);
			}
		}
	}

	// Méthode recevoirDegats, si les PdV du personnage frappé tombe en dessous de zéro retourne la constante PERSO_KO
	public function recevoirDegats() {
		$this->_charHp -= 5;
		if ($this->_charHp <= 0) {
			return self::PERSONNAGE_KO;
		}
		return self::PERSONNAGE_FRAPPE;
	}

	public function nomValide() {
		return !empty($this->_charName);
	}

	// GETTERS // 

	public function charHp() {
		return $this->_charHp;
	}

	public function charId() {
		return $this->_charId;
	}

	public function charName() {
		return $this->_charName;
	}

	public function charKo() {
	    return $this->_charKo;
    }

	public function setCharHp($charHp) {
		$charHp = (int) $charHp;
		if ($charHp >= 0) {
			$this->_charHp = $charHp;
		}
	}

	public function setCharId($charId) {
		$charId = (int) $charId;
		if ($charId > 0) {
			$this->_charId = $charId;
		}
	}

	public function setCharName($charName) {
		if (is_string($charName)) {
			$this->_charName = $charName;
		}
	}

	public function setCharKo($charKo) {
	    $charKo = (int) $charKo;
	    if ($charKo == 0 || $charKo == 1) {
	        $this->_charKo = $charKo;
        }
    }
}
