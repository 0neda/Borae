<?php

	namespace App\Form;

	use App\Entity\Usuario;
	use Symfony\Component\Form\AbstractType;
	use Symfony\Component\Form\Extension\Core\Type\PasswordType;
	use Symfony\Component\Form\Extension\Core\Type\EmailType;
	use Symfony\Component\Form\FormBuilderInterface;
	use Symfony\Component\OptionsResolver\OptionsResolver;
	use Symfony\Component\Validator\Constraints\Length;
	use Symfony\Component\Validator\Constraints\NotBlank;

	class FormCadastro extends AbstractType
	{
		public function buildForm(
			FormBuilderInterface $builder,
			array $options,
		): void {
			$builder
				->add('usuario')
				->add('nome')
				->add('telefone')
				->add('email', EmailType::class, [
					'constraints' => [
						new NotBlank([
							'message' => 'Por favor digite um email',
						]),
					],
				])
				->add('senhaCrua', PasswordType::class, [
					'mapped' => false,
					'attr' => ['autocomplete' => 'new-password'],
					'constraints' => [
						new NotBlank([
							'message' => 'Por favor digite uma senha',
						]),
						new Length([
							'min' => 6,
							'minMessage' => 'Sua senha deve ter pelo menos {{ limit }} caracteres',
							// max length allowed by Symfony for auth reasons
							'max' => 4096,
						]),
					],
				])
				->add('senhaConf', PasswordType::class, [
					'mapped' => false,
					'label' => 'Confirme a senha',
					'attr' => ['autocomplete' => 'new-password'],
					'constraints' => [
						new NotBlank([
							'message' => 'Por favor confirme a senha',
						]),
						new Length([
							'min' => 6,
							'minMessage' => 'Sua senha deve ter pelo menos {{ limit }} caracteres',
							'max' => 4096,
						]),
					],
				]);
		}

		public function configureOptions(OptionsResolver $resolver): void
		{
			$resolver->setDefaults([
				'data_class' => Usuario::class,
			]);
		}
	}
