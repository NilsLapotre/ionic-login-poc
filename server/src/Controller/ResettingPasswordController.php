<?php

namespace App\Controller;

use App\Entity\User;
use App\Event\PasswordRequestedEvent;
use App\Event\PasswordResetEvent;
use App\Form\ResettingFormType;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ResettingPasswordController extends AbstractController
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var UserPasswordEncoderInterface */
    private $passwordEncoder;

    /** @var TokenGeneratorInterface */
    private $tokenGenerator;

    /** @var EventDispatcherInterface */
    private $dispatcher;

    /**
     * Durée de vie du token, en minutes.
     *
     * @var int
     */
    private $passwordRequestTokenTtl;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder, TokenGeneratorInterface $tokenGenerator, EventDispatcherInterface $dispatcher, int $passwordRequestTokenTtl = 10)
    {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->tokenGenerator = $tokenGenerator;
        $this->dispatcher = $dispatcher;
        $this->passwordRequestTokenTtl = $passwordRequestTokenTtl;
    }

    /**
     * @Route("/requestPassword", name="request_password")
     */
    public function requestPassword(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');

            $entityManager = $this->getDoctrine()->getManager();
            $user = $entityManager->getRepository(User::class)->findOneByEmail($email);
            /* @var $user User */

            if (!($user instanceof User)) {
                $this->addFlash('error', 'Email Inconnu');
            //return $this->redirectToRoute('homepage');
            } elseif ($this->isPasswordRequestTokenNonExpired($user)) {
                $this->addFlash('notice', 'Demande en cours');
            } else {
                $token = $this->tokenGenerator->generateToken();

                try {
                    $user->setPasswordRequestToken($token);
                    $user->setPasswordRequestedAt(new DateTime());
                    $entityManager->flush();

                    $this->dispatcher->dispatch(new PasswordRequestedEvent($user), PasswordRequestedEvent::NAME);

                    $this->addFlash('notice', 'Your request is registered.');

                    return $this->redirectToRoute('app_homepage');
                } catch (\Exception $e) {
                    $this->addFlash('warning', $e->getMessage());
                    //return $this->redirectToRoute('app_homepage');
                }
            }
            /*
            $url = $this->generateUrl('app_reset_password', array('token' => $token), UrlGeneratorInterface::ABSOLUTE_URL);

            $message = (new \Swift_Message('Forgot Password'))
                ->setFrom('g.ponty@dev-web.io')
                ->setTo($user->getEmail())
                ->setBody(
                    "blablabla voici le token pour reseter votre mot de passe : " . $url,
                    'text/html'
                );

            $mailer->send($message);
            */
        }

        return $this->render('resetting_password/request_password.html.twig');
    }

    /**
     * @Route("/resetPassword/{token}", name="reset_password")
     */
    public function resetPassword(Request $request, string $token): Response
    {
        $user = $this->entityManager->getRepository(User::class)->findOneByPasswordRequestToken($token);

        if (!($user instanceof User)) {
            $this->addFlash('error', 'Token Inconnu');

            return $this->redirectToRoute('request_password');
        }

        if (!$this->isPasswordRequestTokenNonExpired($user)) {
            $this->addFlash('warning', 'Token expiré');

            return $this->redirectToRoute('request_password');
        }

        $form = $this->createForm(ResettingFormType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPasswordRequestToken(null);
            $user->setPasswordRequestedAt(null);
            $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPlainPassword()));
            $this->entityManager->flush();

            $this->addFlash('notice', 'Mot de passe mis à jour');

            $this->dispatcher->dispatch(new PasswordResetEvent($user), PasswordResetEvent::NAME);

            return $this->redirectToRoute('app_login');
        }

        return $this->render('resetting_password/reset_password.html.twig', [
            'token' => $token,
            'resettingForm' => $form->createView(),
        ]);
    }

    private function isPasswordRequestTokenNonExpired(User $user): bool
    {
        $datePasswordRequestTokenValid = new DateTime(sprintf('-%d minutes', $this->passwordRequestTokenTtl));

        if ($user->getPasswordRequestedAt() instanceof DateTimeInterface) {
            return $user->getPasswordRequestedAt() > $datePasswordRequestTokenValid;
        }

        return false;
    }
}
