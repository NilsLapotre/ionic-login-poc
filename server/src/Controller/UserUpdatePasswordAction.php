<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserUpdatePasswordAction extends AbstractController
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

    public function __invoke(User $data, UserPasswordEncoderInterface $userPasswordEncoder): Response
    {
        $response = new Response();
        if (null !== $data->getPasswordRequestToken() && null !== $data->getPlainPassword()) {
            $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['passwordRequestToken' => $data->getPasswordRequestToken()]);
            if ($user) {
                $user->setPassword($userPasswordEncoder->encodePassword(
                    $user,
                    $data->getPlainPassword()
                ));
                $user->setPasswordRequestToken(null);
                $this->entityManager->flush();
                return new Response();
            }
            $response->setStatusCode(Response::HTTP_UNAUTHORIZED);
            return $response;
        }
        $response->setStatusCode(Response::HTTP_BAD_REQUEST);
        return $response;
    }
}
