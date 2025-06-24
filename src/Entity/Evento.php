<?php

namespace App\Entity;

use App\Repository\EventoRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EventoRepository::class)]
class Evento
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nome = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $data_inicio = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTime $hora_inicio = null;

    #[ORM\ManyToOne(inversedBy: 'eventos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Usuario $criador = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNome(): ?string
    {
        return $this->nome;
    }

    public function setNome(string $nome): static
    {
        $this->nome = $nome;

        return $this;
    }

    public function getDataInicio(): ?\DateTime
    {
        return $this->data_inicio;
    }

    public function setDataInicio(\DateTime $data_inicio): static
    {
        $this->data_inicio = $data_inicio;

        return $this;
    }

    public function getHoraInicio(): ?\DateTime
    {
        return $this->hora_inicio;
    }

    public function setHoraInicio(\DateTime $hora_inicio): static
    {
        $this->hora_inicio = $hora_inicio;

        return $this;
    }

    public function getCriador(): ?Usuario
    {
        return $this->criador;
    }

    public function setCriador(?Usuario $criador): static
    {
        $this->criador = $criador;

        return $this;
    }
}
