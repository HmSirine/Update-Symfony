<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PaiementReservationEvenement
 *
 * @ORM\Table(name="paiement_reservation_evenement")
 * @ORM\Entity
 */
class PaiementReservationEvenement
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_paiement_reservation_evenement", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idPaiementReservationEvenement;

    /**
     * @var int|null
     *
     * @ORM\Column(name="id_evenement", type="integer", nullable=true)
     */
    private $idEvenement;

    /**
     * @var int|null
     *
     * @ORM\Column(name="id_reservation", type="integer", nullable=true)
     */
    private $idReservation;

    /**
     * @var float
     *
     * @ORM\Column(name="prix", type="float", precision=10, scale=0, nullable=false)
     */
    private $prix;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=30, nullable=false)
     */
    private $description;

    public function getIdPaiementReservationEvenement(): ?int
    {
        return $this->idPaiementReservationEvenement;
    }

    public function getIdEvenement(): ?int
    {
        return $this->idEvenement;
    }

    public function setIdEvenement(?int $idEvenement): self
    {
        $this->idEvenement = $idEvenement;

        return $this;
    }

    public function getIdReservation(): ?int
    {
        return $this->idReservation;
    }

    public function setIdReservation(?int $idReservation): self
    {
        $this->idReservation = $idReservation;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }


}
