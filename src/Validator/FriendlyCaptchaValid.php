<?php

declare(strict_types=1);

namespace CORS\Bundle\FriendlyCaptchaBundle\Validator;

use Symfony\Component\Validator\Constraint;

class FriendlyCaptchaValid extends Constraint
{
    /**
     * @var string
     */
    public $message = 'Friendly Captcha is Invalid';

    /**
     * @var string
     */
    public $sitekey = null;

    /**
     * @var string
     */
    public $secret = null;

    /**
     * @return string
     */
    public function validatedBy(): string
    {
        return 'cors_friendly_captcha_validator';
    }
}
