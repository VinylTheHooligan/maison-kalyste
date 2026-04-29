<?php

namespace App\Form;

use App\Entity\ContactMessage;
use App\Enum\ContactTopic;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Validator\Constraints\IsTrue;

class ContactMessageType extends AbstractType
{
    public function __construct(
        private RouterInterface $router,
    )
    {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // ne pas faire attention au champ 'website', c'est un honeypot pour les bots spammer

        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom complet'
            ])
            ->add('email', EmailType::class, [
                'required' => true,
            ])
            ->add('topic', ChoiceType::class, [
                'placeholder' => 'Choisir un sujet',
                'choices' => [
                    'Question sur une commande' => ContactTopic::ORDER,
                    'Question sur un produit' => ContactTopic::PRODUCT,
                    'Livraison' => ContactTopic::DELIVERY,
                    'Retour' => ContactTopic::RETURN,
                    'Autre' => ContactTopic::OTHER,
                ],
                'required' => true,
                'label' => 'Sujet du message'
            ])
            ->add('message', TextareaType::class, [
                'required' => true,
            ])
            ->add('privacy', CheckboxType::class, [
                'label' => "J'accepte la <a class=\"font-bold\" href=\"". $this->router->generate('app_informations_privacy') ."\" target=\"_blank\">politique de confidentialité</a>.",
                'label_html' => true,
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter la politique de confidentialité pour continuer.',
                    ]),
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Envoyer le message',
                'attr' => ['class' => 'form-button'],
            ])
            ->add('website', TextareaType::class, [
                'mapped' => false,
                'required' => false,
                'label' => false,
                'attr' => ['class' => 'hidden'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ContactMessage::class,
        ]);
    }
}
