<?php

namespace App\Validator;

use App\Entity\CheeseListing;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ValidIsPublishedValidator extends ConstraintValidator
{

    private Security $security;
    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->security = $security;
    }

    public function validate($value, Constraint $constraint)
    {


        /* @var $constraint \App\Validator\ValidIsPublished */

        if (!$value instanceof CheeseListing) {
            throw new \LogicException('Only CheeseListing is supported');
        }

        if ($value->getIsPublished()) {
            if (strlen($value->getDescription()) < 100) {
                $this->context->buildViolation('Cannot publish: description is too short!')
                    ->atPath('description')
                    ->addViolation();
            }

            return;
        }


        if ($value->getIsPublished()) {
            // we are publishing!
            // don't allow short descriptions, unless you are an admin
            if (strlen($value->getDescription()) < 100 && !$this->security->isGranted('ROLE_ADMIN')) {
            }
        }

        if (!$this->security->isGranted('ROLE_ADMIN')) {
            $this->context->buildViolation('Only admin users can unpublish')
                ->addViolation();
        }
    }
}
