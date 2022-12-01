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
     * @param string $endpoint
     */
    public function __construct(HttpClientInterface $httpClient, string $secret, string $sitekey, string $endpoint)
    {
        $this->httpClient = $httpClient;
        $this->secret = $secret;
        $this->sitekey = $sitekey;
        $this->endpoint = $endpoint;
    }

    /**
     * @param $value
     * @param Constraint $constraint
     * @return void
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof FriendlyCaptchaValid) {
            throw new UnexpectedTypeException($constraint, FriendlyCaptchaValid::class);
        }

        try
        {
            $response = $this->httpClient->request('POST', $this->endpoint, [
                'body' => [
                    'secret' => $this->secret,
                    'sitekey' => $this->sitekey,
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
