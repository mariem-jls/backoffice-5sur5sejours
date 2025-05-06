<?php 

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class AppExtension extends AbstractExtension implements GlobalsInterface
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function getGlobals(): array
    {
        $user = $this->security->getUser();

        // Check if the user is an instance of UserInterface to ensure the user object is valid.
        return [
            'current_user' => ($user instanceof UserInterface) ? $user : null,
        ];
    }
}
