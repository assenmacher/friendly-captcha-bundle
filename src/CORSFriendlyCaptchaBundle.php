<?php

declare(strict_types=1);

namespace CORS\Bundle\FriendlyCaptchaBundle;

use CORS\Bundle\FriendlyCaptchaBundle\DependencyInjection\CORSFriendlyCaptchaExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class CORSFriendlyCaptchaBundle extends Bundle
{
    /**
     * @return CORSFriendlyCaptchaExtension
     */
    public function getContainerExtension()
    {
        return new CORSFriendlyCaptchaExtension();
    }

}
