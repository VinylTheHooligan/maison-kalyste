<?php

namespace App\Form\User;

use App\DTO\User\RegistrationDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Validator\Constraints as Assert;

class RegistrationFormType extends AbstractType
{
    public function __construct(
        private RouterInterface $router
    )
    {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(min: 2, max: 50),
                ],
                'label' => 'Prénom',
            ])
            ->add('lastName', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(min: 2, max: 50),
                ],
                'label' => 'Nom',
            ])
            ->add('username', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(min: 2, max: 50),
                ],
                'label' => 'Identifiant',
            ])
            ->add('email', EmailType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Email(),
                ],
                'label' => 'E-mail',
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe ne correspondent pas.',
                'first_options' => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Confirmer le mot de passe'],
                'mapped' => false,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(min: 8),
                ],
                'validation_groups' => ['registration'],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new Assert\IsTrue(message: 'Vous devez accepter nos politiques de confidentialités, nos CGV et nos CGU.'),
                ],
                'label_html' => true,
                'label' => sprintf(
                    'J\'accepte les 
                        <a href="%s" target="_blank" class="font-bold">conditions générales de vente</a>, 
                        <a href="%s" target="_blank" class="font-bold">conditions générales d\'utilisation</a> 
                        et la 
                        <a href="%s" target="_blank" class="font-bold">politique de confidentialité</a>.',
                    $this->router->generate('app_informations_cgv'),
                    $this->router->generate('app_informations_cgu'),
                    $this->router->generate('app_informations_privacy')
                ),
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Inscrivez-vous',
                'attr' => ['class' => 'form-button'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RegistrationDTO::class,
        ]);
    }
}
