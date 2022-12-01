<?php

declare(strict_types=1);

namespace CORS\Bundle\FriendlyCaptchaBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class FriendlyCaptchaType extends AbstractType
{
    /**
     * @var string
     */
    protected $sitekey;

    /**
     * @var string
     */
    protected $useLocalScriptFiles;

    /**
     * @var string
     */
    protected $endpoint;

    /**
     * @param string $sitekey
     * @param string $useLocalScriptFiles
     * @param string $endpoint
     */
    public function __construct(string $sitekey, string $useLocalScriptFiles, string $endpoint)
    {
        $this->sitekey = $sitekey;
        $this->useLocalScriptFiles = $useLocalScriptFiles;
        $this->endpoint = $endpoint;
    }

    /**
     * @return string
     */
    public function getParent(): string
    {
        return HiddenType::class;
    }

    /**
     * @param FormView $view
     * @param FormInterface $form
     * @param array $options
     * @return void
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $fcValues = array_filter([
            'puzzle-puzzle-endpoint' => $this->endpoint,
            'lang' => $options['lang'] ?? null,
            'start' => $options['start'] ?? null,
            'callback' => $options['callback'] ?? null,
        ]);

        $view->vars['sitekey']                = $this->sitekey;
        $view->vars['use_local_script_files'] = $this->useLocalScriptFiles;
        $view->vars['friendly_captcha']       = $fcValues;
    }

    /**
     * @param OptionsResolver $resolver
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'lang' => null,
            'start' => 'focus',
            'callback' => null,
        ]);

        $resolver->setAllowedValues('start', ['auto', 'focus', 'none']);
    }

    /**
     * @return string
     */
    public function getBlockPrefix(): string
    {
        return 'cors_friendly_captcha_type';
    }

    /**
     * @return string
     */
    public function getSiteKey(): string
    {
        return $this->sitekey;
    }

}
