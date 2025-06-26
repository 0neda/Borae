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

    public function getDataHoraInicio(): ?\DateTime
    {
        if (!$this->data_inicio || !$this->hora_inicio) {
            return null;
        }

        $dataHora = clone $this->data_inicio;
        $dataHora->setTime(
            (int)$this->hora_inicio->format('H'),
            (int)$this->hora_inicio->format('i'),
            (int)$this->hora_inicio->format('s')
        );

        return $dataHora;
    }

    public function isPassado(): bool
    {
        $dataHoraInicio = $this->getDataHoraInicio();
        return $dataHoraInicio && $dataHoraInicio < new \DateTime();
    }

    public function isProximo(int $dias = 7): bool
    {
        $dataHoraInicio = $this->getDataHoraInicio();
        if (!$dataHoraInicio) {
            return false;
        }

        $agora = new \DateTime();
        $limite = (clone $agora)->modify("+{$dias} days");

        return $dataHoraInicio >= $agora && $dataHoraInicio <= $limite;
    }

    public function getDataFormatada(string $formato = 'd/m/Y'): ?string
    {
        return $this->data_inicio?->format($formato);
    }

    public function getHoraFormatada(string $formato = 'H:i'): ?string
    {
        return $this->hora_inicio?->format($formato);
    }

    public function isHoje(): bool
    {
        if (!$this->data_inicio) {
            return false;
        }

        $hoje = new \DateTime();
        return $this->data_inicio->format('Y-m-d') === $hoje->format('Y-m-d');
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

    public function deletarEvento(EventoRepository $eventoRepository): void
    {
        $eventoRepository->deletarEvento($this);
    }
}