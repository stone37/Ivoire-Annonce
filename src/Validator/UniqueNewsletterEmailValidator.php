<?php

namespace App\Validator;

use App\Entity\NewsletterData;
use App\Repository\NewsletterDataRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

class UniqueNewsletterEmailValidator extends ConstraintValidator
{
    public function __construct( private NewsletterDataRepository $repository)
    {
    }

    public function validate($email, Constraint $constraint): void
    {
        /** @var UniqueNewsletterEmail $constraint */
        Assert::isInstanceOf($constraint, UniqueNewsletterEmail::class);

        if ($this->isEmailValid($email) === false) {
            $this->context->addViolation($constraint->message);
        }
    }

    private function isEmailValid($email): bool
    {
        $newsletter = $this->repository->findOneBy(['email' => $email]);

        if ($newsletter instanceof NewsletterData) {
            return false;
        }

        return true;
    }
}

