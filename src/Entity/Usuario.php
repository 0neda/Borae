<?php

	namespace App\Entity;

	use App\Repository\UsuarioRepository;
	use Doctrine\Common\Collections\ArrayCollection;
	use Doctrine\Common\Collections\Collection;
	use Doctrine\ORM\Mapping as ORM;
	use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
	use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
	use Symfony\Component\Security\Core\User\UserInterface;
	use Symfony\Component\Validator\Constraints as Assert;

	#[ORM\Entity(repositoryClass : UsuarioRepository::class)]
	#[ORM\Table(
		name              : 'usuario',
		uniqueConstraints : [
			new ORM\UniqueConstraint(
				name    : 'UNIQ_IDENTIFIER_USUARIO',
				columns : ['usuario'],
			),
			new ORM\UniqueConstraint(name : 'UNIQ_EMAIL', columns : ['email']),
		]
	)]
	#[UniqueEntity(fields : ['usuario'], message : 'Já existe uma conta com este nome de usuário.')]
	#[UniqueEntity(fields : ['email'], message : 'Já existe uma conta com este email.')]
	class Usuario implements UserInterface, PasswordAuthenticatedUserInterface
	{
		#[ORM\Id]
		#[ORM\GeneratedValue]
		#[ORM\Column]
		private ?int $id = null;

		#[ORM\Column(length : 180)]
		private ?string $usuario = null;

		/**
		 * @var list<string> The user roles
		 */
		#[ORM\Column]
		private array $roles = ["ROLE_USUARIO"];

		/**
		 * @var string The hashed password
		 */
		#[ORM\Column]
		private ?string $password = null;

		#[ORM\Column(length : 255)]
		private ?string $nome = null;

		#[ORM\Column(length : 24, nullable : true)]
		private ?string $telefone = null;

		/**
		 * @var Collection<int, Evento>
		 */
		#[ORM\OneToMany(targetEntity : Evento::class, mappedBy : 'criador', orphanRemoval : true)]
		private Collection $eventos;

		#[ORM\Column]
		private ?\DateTime $data_criacao = null;

		#[ORM\Column(length : 128)]
		#[Assert\Email(message : "O e-mail '{{ value }}' não é válido.")]
		private ?string $email = null;

		public function __construct()
		{
			$this->eventos = new ArrayCollection();
			$this->data_criacao = new \DateTime();
		}

		public function getId(): ?int
		{
			return $this->id;
		}

		public function getUsuario(): ?string
		{
			return $this->usuario;
		}

		public function setUsuario(string $usuario): static
		{
			$this->usuario = $usuario;

			return $this;
		}

		/**
		 * A visual identifier that represents this user.
		 *
		 * @see UserInterface
		 */
		public function getUserIdentifier(): string
		{
			return (string)$this->usuario;
		}

		public function getDisplayName(): string
		{
			return $this->nome ?? $this->usuario ?? 'Usuário #'.$this->id;
		}

		/**
		 * @see UserInterface
		 */
		public function getRoles(): array
		{
			$roles = $this->roles;
			// guarantee every user at least has ROLE_USUARIO
			$roles[] = 'ROLE_USUARIO';

			return array_unique($roles);
		}

		/**
		 * @param  list<string>  $roles
		 */
		public function setRoles(array $roles): static
		{
			$this->roles = $roles;

			return $this;
		}

		public function addRole(string $role): static
		{
			if (!in_array($role, $this->roles)) {
				$this->roles[] = $role;
			}

			return $this;
		}

		public function removeRole(string $role): static
		{
			$this->roles = array_filter($this->roles, fn($r) => $r !== $role);

			return $this;
		}

		public function hasRole(string $role): bool
		{
			return in_array($role, $this->getRoles());
		}

		public function getRolesFormatadas(): string
		{
			$roles = array_filter(
				$this->getRoles(),
				fn($role) => $role !== 'ROLE_USUARIO',
			);

			$rolesFormatted = array_map(function ($role) {
				return match ($role) {
					'ROLE_ADMIN' => 'Administrador',
					'ROLE_EMPRESA' => 'Empresa',
					default => str_replace('ROLE_', '', $role)
				};
			}, $roles);

			return empty($rolesFormatted)
				? 'Usuário'
				: implode(
					', ',
					$rolesFormatted,
				);
		}

		public function setAsAdmin(): static
		{
			return $this->addRole('ROLE_ADMIN');
		}

		public function setAsEmpresa(): static
		{
			return $this->addRole('ROLE_EMPRESA');
		}

		public function removeAdmin(): static
		{
			return $this->removeRole('ROLE_ADMIN');
		}

		public function removeEmpresa(): static
		{
			return $this->removeRole('ROLE_EMPRESA');
		}

		public function isAdmin(): bool
		{
			return $this->hasRole('ROLE_ADMIN');
		}

		public function isEmpresa(): bool
		{
			return $this->hasRole('ROLE_EMPRESA');
		}

		public function toggleAdmin(): static
		{
			return $this->isAdmin() ? $this->removeAdmin() : $this->setAsAdmin();
		}

		public function toggleEmpresa(): static
		{
			return $this->isEmpresa() ? $this->removeEmpresa()
				: $this->setAsEmpresa();
		}

		/**
		 * @see PasswordAuthenticatedUserInterface
		 */
		public function getPassword(): ?string
		{
			return $this->password;
		}

		public function setPassword(string $password): static
		{
			$this->password = $password;

			return $this;
		}

		/**
		 * @see UserInterface
		 */
		public function eraseCredentials(): void
		{
			// If you store any temporary, sensitive data on the user, clear it here
			// $this->senhaCrua = null;
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

		public function getTelefone(): ?string
		{
			return $this->telefone;
		}

		public function setTelefone(?string $telefone): static
		{
			$this->telefone = $telefone;

			return $this;
		}

		/**
		 * @return Collection<int, Evento>
		 */
		public function getEventos(): Collection
		{
			return $this->eventos;
		}

		public function addEvento(Evento $evento): static
		{
			if (!$this->eventos->contains($evento)) {
				$this->eventos->add($evento);
				$evento->setCriador($this);
			}

			return $this;
		}

		public function removeEvento(Evento $evento): static
		{
			if ($this->eventos->removeElement($evento)) {
				// set the owning side to null (unless already changed)
				if ($evento->getCriador() === $this) {
					$evento->setCriador(null);
				}
			}

			return $this;
		}

		public function getDataCriacao(): ?\DateTime
		{
			return $this->data_criacao;
		}

		public function setDataCriacao(\DateTime $data_criacao): static
		{
			$this->data_criacao = $data_criacao;

			return $this;
		}

		public function getEmail(): ?string
		{
			return $this->email;
		}

		public function setEmail(string $email): static
		{
			$this->email = $email;

			return $this;
		}
	}
