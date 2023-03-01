<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * Contrainte pour vérifier l'unicité d'un enregistrement.
 *
 * Pour fonctionner on part du principe que l'objet et l'entité aura une méthode "getId()"
 *
 * @Annotation
 */
class Unique extends Constraint
{
    public string $message = 'Cette valeur est déjà utilisée';

    public $entityClass = null;

    public $field = '';

    public function getRequiredOptions(): array
    {
        return ['field'];
    }

    public function getTargets(): array|string
    {
        return self::CLASS_CONSTRAINT;
    }
}
