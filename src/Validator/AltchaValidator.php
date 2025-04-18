<?php

declare(strict_types=1);

namespace Huluti\AltchaBundle\Validator;

use AltchaOrg\Altcha\Altcha;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class AltchaValidator extends ConstraintValidator
{
    public function __construct(
        private readonly bool $enable,
        private readonly string $hmacKey,
        private readonly RequestStack $requestStack,
    ) {
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed      $altchaEncoded The value that should be validated
     * @param Constraint $constraint    The constraint for the validation
     */
    public function validate($altchaEncoded, Constraint $constraint): void
    {
        if (false === $this->enable) {
            return;
        }

        if (!$altchaEncoded) {
            $request = $this->requestStack->getCurrentRequest();
            $altchaEncoded = $request->request->get('altcha');
        }
        if (!is_string($altchaEncoded)) {
            $this->context->buildViolation($constraint->message)
                ->addviolation();

            return;
        }
        $altchaJson = base64_decode($altchaEncoded, true);
        if (!is_string($altchaJson)) {
            $this->context->buildViolation($constraint->message)
                ->addviolation();

            return;
        }
        $payload = json_decode($altchaJson, true, 512, JSON_THROW_ON_ERROR);

        if (!Altcha::verifySolution($payload, $this->hmacKey, true)) {
            $this->context->buildViolation($constraint->message)
                ->addviolation();
        }
    }
}
