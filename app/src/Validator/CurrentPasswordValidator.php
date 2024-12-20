<?php
namespace App\Validator;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CurrentPasswordValidator extends ConstraintValidator
{
    private $security;
    private $passwordHasher;

    public function __construct(Security $security, UserPasswordHasherInterface $passwordHasher)
    {
        $this->security = $security;
        $this->passwordHasher = $passwordHasher;
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$value) {
            return;
        }

        $user = $this->security->getUser();
        if (!$user instanceof PasswordAuthenticatedUserInterface) {
            throw new \LogicException('The current user must implement PasswordAuthenticatedUserInterface.');
        }

        if (!$this->passwordHasher->isPasswordValid($user, $value)) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}