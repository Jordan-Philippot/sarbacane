<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\ZipCodeType;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Doctrine\Persistence\ManagerRegistry;

class UserType extends AbstractType
{

    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastname', TextType::class,  [
                'required' => true,
                'label' => 'Nom',
                // 'error' => "Veuillez renseigner votre nom",
                'attr' => [
                    'minlength' => 2,
                    'maxlength' => 50,
                ],
            ])
            ->add('firstname', TextType::class,  [
                'required' => true,
                'label' => 'Prénom',
                'attr' => [
                    'minlength' => 2,
                    'maxlength' => 50,
                ]
            ])
            ->add('email', EmailType::class, [
                'constraints' => [
                    new Callback(function ($email, ExecutionContextInterface $context){

                        $user = $this->doctrine->getRepository(User::class)->findOneBy(['email' =>$email]);
                        if($user){
                            $context->BuildViolation("Vous êtes déjà inscrit.")
                            ->atPath('email')
                            ->addViolation();
                        }
                    })
                ],
                'required' => true,
                'label' => 'Email',
                'attr' => [
                    'minlength' => 2,
                    'maxlength' => 50,
                ]
            ])
            ->add('company', TextType::class,  [
                'required' => true,
                'label' => 'Société',
                'attr' => [
                    'minlength' => 2,
                    'maxlength' => 50,
                ]
            ])
            ->add('phone', TelType::class,  [
                'constraints' => [
                    new Regex(
                        [
                            'pattern' => '/^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/',
                            'message' => 'Veuillez renseigner un numéro de téléphone valide.'
                        ]
                    )
                ],
                'required' => true,
                'label' => 'Téléphone',
                'attr' => [
                    'minlength' => 4,
                    'maxlength' => 15,
                ],

            ])
            ->add('address', TextType::class,  [
                'required' => true,
                'label' => 'Adresse',
                'attr' => [
                    'minlength' => 4,
                    'maxlength' => 200,
                ]
            ])
            ->add('zipCode', TextType::class,  [
                'constraints' => [
                    new Regex(
                        [
                            'pattern' => '/^((0[1-9])|([1-8][0-9])|(9[0-8])|(2A)|(2B)) *([0-9]{3})?$/i',
                            'message' => 'Veuillez renseigner un code postal valide.'
                        ]
                    )
                ],
                'required' => true,
                'label' => 'Code postal',
                'attr' => [
                    'minlength' => 2,
                    'maxlength' => 8,
                ],

            ])
            ->add('city', TextType::class,  [
                'required' => true,
                'label' => 'Ville',
                'attr' => [
                    'minlength' => 2,
                    'maxlength' => 100,
                ]
            ])
            ->add('siren', TextType::class,  [
                'constraints' => [
                    new Regex(
                        [
                            'pattern' => '/^\d{9}$/',
                            'message' => 'Veuillez renseigner un N° SIREN valide.'
                        ]
                    )
                ],
                'required' => true,
                'label' => 'SIREN',
                'attr' => [
                    'minlength' => 9,
                    'maxlength' => 9,
                ]
               
            ])
            ->add('naf', TextType::class,  [
                'required' => true,
                'label' => 'Code NAF',
                'attr' => [
                    'minlength' => 5,
                    'maxlength' => 6,
                ]
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn-submit',
                    'data' => true,
                ],
                'label' => 'Valider l\'inscription'
            ])
            ->getForm();
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
