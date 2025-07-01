<?php

	namespace App\Controller;

	use App\Entity\Usuario;
	use App\Form\FormCadastro;
	use Doctrine\ORM\EntityManagerInterface;
	use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
	use Symfony\Component\Routing\Attribute\Route;

	class CadastroController extends AbstractController
	{
		#[Route('/cadastro', name : 'borae_cadastro')]
		public function register(
			Request $request,
			UserPasswordHasherInterface $userPasswordHasher,
			EntityManagerInterface $entityManager,
		): Response {
			$user = new Usuario();
			$form = $this->createForm(FormCadastro::class, $user);
			$form->handleRequest($request);

			if ($form->isSubmitted()) {
				$senha = $form->get('senhaCrua')->getData();
				$senhaConf = $form->get('senhaConf')->getData();

				if ($form->isValid() && $senha === $senhaConf) {
					/** @var string $senhaCrua */
					$senhaCrua = $form->get('senhaCrua')->getData();

					// encode the plain password
					$user->setPassword(
						$userPasswordHasher->hashPassword($user, $senhaCrua),
					);

					$entityManager->persist($user);
					$entityManager->flush();

					// do anything else you need here, like send an email

					return $this->redirectToRoute('borae_entrar');
				} else {
					if ($senha !== $senhaConf) {
						$form->get('senhaConf')->addError(
							new \Symfony\Component\Form\FormError(
								'As senhas não coincidem.',
							),
						);
					}
					foreach ($form->getErrors(true) as $erro) {
						$this->addFlash('erro', $erro->getMessage());
					}
				}
			}

			return $this->render('cadastro/cadastro.html.twig', [
				'formCadastro' => $form,
			]);
		}
	}
