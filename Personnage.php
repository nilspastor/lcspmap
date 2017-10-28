<?php

class Personnage {
	
	private $_charHp;
	private $_charId;
	private $_charName;
	private $_charKo;

	const CEST_MOI = 1;
	const PERSONNAGE_KO = 2;
	const PERSONNAGE_FRAPPE = 3;

	public function __construct(array $donnees) {
		$this->hydrate($donnees);
	}

	public function frapper(Personnage $perso) {
		if ($perso->charId() == $this->_charId) {
			return self::CEST_MOI;
		}
		return $perso->recevoirDegats();
	}

	public function hydrate(array $donnees) {
		foreach ($donnees as $key => $value) {
			$method = 'set'.ucfirst($key);
			if (method_exists($this, $method)) {
				$this->$method($value);
			}
		}
	}

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