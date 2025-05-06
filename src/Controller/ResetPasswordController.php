<?php

namespace App\Controller;

use App\Entity\User;
use Psr\Log\LoggerInterface;
use App\Service\ResendEmailService;
use App\Form\ChangePasswordFormType;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\ResetPasswordRequestFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetToken;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;

#[Route('/forgot')]
class ResetPasswordController extends AbstractController
{
    use ResetPasswordControllerTrait;

    private ResendEmailService $resendEmailService;
    private LoggerInterface $logger;

    public function __construct(
        private ResetPasswordHelperInterface $resetPasswordHelper,
        private EntityManagerInterface $entityManager,
        ResendEmailService $resendEmailService,
        LoggerInterface $logger,
    ) {
        $this->resendEmailService = $resendEmailService;
        $this->logger = $logger;
    }

    #[Route('', name: 'forgot')]
    public function request(Request $request): Response
    {
        $form = $this->createForm(ResetPasswordRequestFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->processSendingPasswordResetEmail($form->get('email')->getData());
        }

        return $this->render('security/forgot.html.twig', [
            'requestForm' => $form->createView(),
        ]);
    }

    #[Route('/check-email/{token}', name: 'app_check_email')]
    public function checkEmail(string $token = null): Response
    {
        if (!$token) {
            throw $this->createNotFoundException('No reset password token found.');
        }

        return $this->render('security/check_email.html.twig', [
            'resetToken' => $token,
        ]);
    }

    #[Route('/reset/{token}', name: 'app_reset_password')]
    public function reset(Request $request, UserPasswordHasherInterface $passwordHasher, string $token = null): Response
    {
        if (!$token) {
            throw $this->createNotFoundException('No reset password token found in the URL.');
        }

        try {
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
        } catch (ResetPasswordExceptionInterface $e) {
            $this->logger->error('Password reset error for token: ' . $token . ' - Reason: ' . $e->getReason());
            return $this->redirectToRoute('forgot');
        }

        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->resetPasswordHelper->removeResetRequest($token);

            $encodedPassword = $passwordHasher->hashPassword(
                $user,
                $form->get('plainPassword')->getData()
            );

            $user->setPassword($encodedPassword);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/reset.html.twig', [
            'resetForm' => $form->createView(),
        ]);
    }

    private function processSendingPasswordResetEmail(string $emailFormData): RedirectResponse
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy([
            'email' => $emailFormData,
        ]);
        // dd($user);

        if (!$user) {
            return $this->redirectToRoute('forgot');
        }

        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
            $token = $resetToken->getToken();
            $this->logger->info('Generated reset token: ' . $token);
            
            $this->sendPasswordResetEmail($user->getEmail(), $token);

            return $this->redirectToRoute('app_check_email', ['token' => $resetToken->getToken()]);
        } catch (ResetPasswordExceptionInterface $e) {
            $this->logger->error('Password reset error for user: ' . $user->getEmail() . ' - Reason: ' . $e->getReason());
            return $this->redirectToRoute('forgot');
        } catch (\Exception $e) {
            $this->logger->error('An error occurred during password reset for user: ' . $user->getEmail() . ' - Error: ' . $e->getMessage());
            return $this->redirectToRoute('forgot');
        }
    }

    private function sendPasswordResetEmail(string $email, string $token): void
    {
        $body = $this->renderView('security/email.html.twig', [
            'resetToken' => $token,
        ]);

        try {
            $this->resendEmailService->sendEmail($email, 'Password Reset Request', $body, ['verify' => false]);
        } catch (\Exception $e) {
            $this->logger->error('Failed to send password reset email to: ' . $email . ' - Error: ' . $e->getMessage());
            $this->addFlash('reset_password_error', 'There was a problem sending the reset email. Please try again later.');
        }
    }
}
