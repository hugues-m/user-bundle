<?php

declare (strict_types = 1);

namespace HMLB\UserBundle\Form\Type\Command;

use HMLB\UserBundle\Command\ChangePassword;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * ChangePasswordType.
 *
 * @author Hugues Maignol <hugues@hmlb.fr>
 */
class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'password',
                'password'
            )
            ->add(
                'user',
                'ddd_identity'
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'label' => 'Email',
                'data_class' => 'HMLB\UserBundle\Command\ChangePassword',
                'empty_data' => function (Options $options) {
                        return function (FormInterface $form) use ($options) {
                            return new ChangePassword(
                                $form->get('password')->getData(),
                                $form->get('user')->getData()
                            );
                        };
                    },
            )
        );
    }

    public function getName()
    {
        return 'change_password';
    }
}
