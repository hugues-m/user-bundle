<?php

namespace HMLB\UserBundle\Form\Type\Command;

use HMLB\UserBundle\Command\ChangeEmail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * ChangeEmailType.
 *
 * @author Hugues Maignol <hugues@hmlb.fr>
 */
class ChangeEmailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'email',
                'email'
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
                'data_class' => 'HMLB\UserBundle\Command\ChangeEmail',
                'empty_data' => function (Options $options) {
                        return function (FormInterface $form) use ($options) {
                            return new ChangeEmail(
                                $form->get('email')->getData(),
                                $form->get('user')->getData()
                            );
                        };
                    },
            )
        );
    }

    public function getName()
    {
        return 'change_email';
    }
}
