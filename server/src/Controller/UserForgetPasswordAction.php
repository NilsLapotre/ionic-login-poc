<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class UserForgetPasswordAction extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    public function __invoke(User $data, TokenGeneratorInterface $tokenGenerator): Response
    {
        if (null !== $data->getEmail()) {
            $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $data->getEmail()]);
            if ($user) {
                $token = $tokenGenerator->generateToken();
                $user->setPasswordRequestToken($token);
                $this->entityManager->flush();

                return new Response(json_encode(['token' => $user->getPasswordRequestToken()]));
            }
        }

        return new Response();
    }
}
