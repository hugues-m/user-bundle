<?php

namespace HMLB\UserBundle\Form\Type;

use HMLB\UserBundle\Command\RegisterUser;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * RegisterUserType.
 *
 * @author Hugues Maignol <hugues@hmlb.fr>
 */
class RegisterUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'username',
                'text',
                [
                    'required' => true,
                ]
            )
            ->add(
                'email',
                'email',
                [
                    'required' => true,
                ]
            )
            ->add(
                'password',
                'password',
                [
                    'required' => true,
                ]
            )
            ->add(
                'submit',
                'submit',
                [
                    'attr' => [
                        'class' => 'pull-right',
                    ],
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'label' => 'Sign up',
                'data_class' => 'HMLB\UserBundle\Command\RegisterUser',
                'empty_data' => function (FormInterface $form) {
                        $command = new RegisterUser(
                            $form->get('username')->getData(),
                            $form->get('email')->getData(),
                            $form->get('password')->getData(),
                            []
                        );

                        return $command;
                },
            ]
        );
    }

    public function getName()
    {
        return 'register_user';
    }
}
