<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Form\UserRegistType;
use Symfony\Component\Mime\Email;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\ByteString;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @Route("/{_locale}", requirements={"_locale": "hu|en|de|"}, name="portal_security_")
 */
class SecurityController extends AbstractController
{
    protected EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    ){
        $this->entityManager = $entityManager;
    }
    
    /**
     * @Route("/login", name="login")
     */
    public function login(AuthenticationUtils $authenticationUtils, SessionInterface $session)
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername() ?: $session->get('email');
        return $this->render('admin/security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
     * @Route("/register", name="register")
     */
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordEncoder,
        MailerInterface $mailer,
    ) {
        $user = new User();
        $form = $this->createForm(UserRegistType::class, $user, ['locale' => $request->getLocale()]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $oldUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $form->get('email')->getData()]);
                if ($oldUser) {
                    $user = $oldUser;
                    $user->setUsedPassword(false);
                    $user->setUpdatedAt(new DateTime());
                } else {
                    $user->setEmail($form->get('email')->getData());
                    $user->setCreatedAt(new DateTime());
                    $user->setRoles([]);
                }
                $password = strtoupper(ByteString::fromRandom(6)->toString());
                $user->setPassword($passwordEncoder->hashPassword(
                    $user,
                    $password
                ));
                $user->setPasswordAvaibleUntil(new DateTime('+ 1 hour'));
                $this->entityManager->persist($user);
                $this->entityManager->flush();
                $email = (new Email())
                    ->from($this->getParameter('mailer_from_address'))
                    ->to($form->get('email')->getData())
                    ->subject('Sikeres regisztráció')
                    ->html('
                        <p>Kedves Felhasználó</p>
                        <p>Ezzel a jelszóval tudja azonosítani magát: ' . $password .'</p>
                    ')
                ;
                $mailer->send($email);
                return $this->redirectToRoute('portal_security_login');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Sikertelen regisztráció.');
            }
        }
        return $this->render('portal/register.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/success", name="success")
     * @throws Exception
     */
    public function success()
    {
        $user = $this->getUser();

        if (!$this->isGranted("ROLE_ADMIN")){
            $user->setUsedPassword(true);
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        if ($this->isGranted("ROLE_ADMIN")){
            return $this->redirectToRoute('admin_dashboard');
        } else {
            return $this->redirectToRoute('portal_page_booking');
        }
    }
}
