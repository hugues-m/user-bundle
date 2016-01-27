<?php

namespace HMLB\UserBundle\Validator\Constraint;

use Doctrine\Common\Persistence\ObjectManager;
use HMLB\UserBundle\User\User;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * UnusedUsernameValidator.
 *
 * @author Hugues Maignol <hugues@hmlb.fr>
 */
class UnusedUsernameValidator extends ConstraintValidator
{
    /**
     * @var ObjectManager
     */
    protected $om;

    /**
     * @var string The User class
     */
    protected $class;

    public function __construct(ObjectManager $om, string $class)
    {
        $this->om = $om;
        $this->class = $class;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        $res = $this
            ->om
            ->getRepository($this->class)
            ->findOneBy(['usernameCanonical' => User::canonicalize($value)]);

        if (null !== $res) {
            $this->context
                ->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
