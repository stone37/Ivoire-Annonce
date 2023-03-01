<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class NewsletterValidator
{
    public function __construct(private ValidatorInterface $validator)
    {
    }

    public function validate($email): array
    {
        $violations = $this->validator->validate($email, [
            new Email(['message' => 'L\'adresse email est invalide']),
            new NotBlank(['message' => 'Veuillez remplir le champ d\'email']),
            new UniqueNewsletterEmail(),
        ]);


        $errors = [];

        if (count($violations) === 0) {
            return $errors;
        }

        /** @var ConstraintViolation $violation */
        foreach ($violations as $violation) {
            $errors[] = $violation->getMessage();
        }

        return $errors;
    }
}

