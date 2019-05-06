<?php

namespace CitrespBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BaseCities
 *
 * @ORM\Table(name="BASE_CITIES")
 * @ORM\Entity (repositoryClass="CitrespBundle\Repository\BaseCitiesRepository")
 */
class BaseCities
{
    /**
     * @var string|null
     *
     * @ORM\Column(name="Code_commune_INSEE", type="string", length=5, nullable=true)
     */
    private $codeCommuneInsee;

    /**
     * @var string|null
     *
     * @ORM\Column(name="Nom_commune", type="string", length=38, nullable=true)
     */
    private $nomCommune;

    /**
     * @var int|null
     *
     * @ORM\Column(name="Code_postal", type="integer", nullable=true)
     */
    private $codePostal;

    /**
     * @var string|null
     *
     * @ORM\Column(name="Libelle_acheminement", type="string", length=32, nullable=true)
     */
    private $libelleAcheminement;

    /**
     * @var string|null
     *
     * @ORM\Column(name="coordonnees_gps", type="string", length=33, nullable=true)
     */
    private $coordonneesGps;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;



    /**
     * Set codeCommuneInsee.
     *
     * @param string|null $codeCommuneInsee
     *
     * @return BaseCities
     */
    public function setCodeCommuneInsee($codeCommuneInsee = null)
    {
        $this->codeCommuneInsee = $codeCommuneInsee;

        return $this;
    }

    /**
     * Get codeCommuneInsee.
     *
     * @return string|null
     */
    public function getCodeCommuneInsee()
    {
        return $this->codeCommuneInsee;
    }

    /**
     * Set nomCommune.
     *
     * @param string|null $nomCommune
     *
     * @return BaseCities
     */
    public function setNomCommune($nomCommune = null)
    {
        $this->nomCommune = $nomCommune;

        return $this;
    }

    /**
     * Get nomCommune.
     *
     * @return string|null
     */
    public function getNomCommune()
    {
        return $this->nomCommune;
    }

    /**
     * Set codePostal.
     *
     * @param int|null $codePostal
     *
     * @return BaseCities
     */
    public function setCodePostal($codePostal = null)
    {
        $this->codePostal = $codePostal;

        return $this;
    }

    /**
     * Get codePostal.
     *
     * @return int|null
     */
    public function getCodePostal()
    {
        return $this->codePostal;
    }

    /**
     * Set libelleAcheminement.
     *
     * @param string|null $libelleAcheminement
     *
     * @return BaseCities
     */
    public function setLibelleAcheminement($libelleAcheminement = null)
    {
        $this->libelleAcheminement = $libelleAcheminement;

        return $this;
    }

    /**
     * Get libelleAcheminement.
     *
     * @return string|null
     */
    public function getLibelleAcheminement()
    {
        return $this->libelleAcheminement;
    }

    /**
     * Set coordonneesGps.
     *
     * @param string|null $coordonneesGps
     *
     * @return BaseCities
     */
    public function setCoordonneesGps($coordonneesGps = null)
    {
        $this->coordonneesGps = $coordonneesGps;

        return $this;
    }

    /**
     * Get coordonneesGps.
     *
     * @return string|null
     */
    public function getCoordonneesGps()
    {
        return $this->coordonneesGps;
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }



    public function getCityLabel()
    {
      return $this->nomCommune . ' (' . $this->codePostal . ')';
    }
}
