<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
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

    public function __invoke(User $data, TokenGeneratorInterface $tokenGenerator, MailerInterface $mailer): Response
    {
        if (null !== $data->getEmail()) {
            $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $data->getEmail()]);
            if ($user) {
                $token = $tokenGenerator->generateToken();
                $user->setPasswordRequestToken($token);
                $this->entityManager->flush();
                $this->sendEmail($user->getEmail(), $user->getPasswordRequestToken(), $mailer);

                return new Response();
            }
        }

        return new Response();
    }

    public function sendEmail(string $email, string $token, MailerInterface $mailer)
    {
        $url = 'http://localhost:8100/reset-password/'.$token;
        $email = (new Email())
            ->from('hello@example.com')
            ->to($email)
            ->subject('Reset password')
            ->text('Click this link to reset your password : '.$url);

        try {
            $mailer->send($email);
        } catch (TransportExceptionInterface $e) {
        }
    }
}
