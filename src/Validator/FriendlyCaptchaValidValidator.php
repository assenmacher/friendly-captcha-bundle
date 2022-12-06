<?php

declare(strict_types=1);

namespace CORS\Bundle\FriendlyCaptchaBundle\Validator;

use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Exception as Exception;

class FriendlyCaptchaValidValidator extends ConstraintValidator
{
    /**
     * Enable captcha?
     *
     * @var bool
     */
    protected $enabled;

    /**
     * @var HttpClientInterface
     */
    protected $httpClient;

    /**
     * @var string
     */
    protected $secret;

    /**
     * @var string
     */
    protected $sitekey;

    /**
     * @var string
     */
    protected $endpoint;

    /**
     * @param HttpClientInterface $httpClient
     * @param string $secret
     * @param string $sitekey
     * @param bool   $enabled                      Recaptcha status
     * @param string $endpoint
     */
    public function __construct(HttpClientInterface $httpClient, string $secret, string $sitekey, bool $enabled, string $endpoint)
    {
        $this->httpClient = $httpClient;
        $this->secret = $secret;
        $this->sitekey = $sitekey;
        $this->enabled = $enabled;
        $this->endpoint = $endpoint;
    }

    /**
     * @param $value
     * @param Constraint $constraint
     * @return void
     */
    public function validate($value, Constraint $constraint)
    {
        // if captcha is disabled, always valid
        if (!$this->enabled) {
            return;
        }

        if (!$constraint instanceof FriendlyCaptchaValid) {
            throw new UnexpectedTypeException($constraint, FriendlyCaptchaValid::class);
        }

        try
        {
            $secret  = $constraint->secret ?: $this->secret;
            $sitekey = $constraint->sitekey ?: $this->sitekey;


            $response = $this->httpClient->request('POST', $this->endpoint, [
                'body' => [
                    'secret' => $secret,
                    'sitekey' => $sitekey,
                    'solution' => $value,
                ],
            ]);

            $content = $response->getContent();

            if (!$content) {
                throw new Exception($constraint->message);
            }

            $result = json_decode($content, true, 512, JSON_THROW_ON_ERROR);

            if (!array_key_exists('success', $result) || $result['success'] !== true) {
                throw new Exception($constraint->message);
            }
        }
        catch (Exception $e)
        {
            $this->context->addViolation($e->getMessage());
        }
    }
}
