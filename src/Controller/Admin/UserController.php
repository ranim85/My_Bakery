<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/staff')]
class UserController extends AbstractController
{
    #[Route('/', name: 'admin_user_index')]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('admin/user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'admin_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST')) {
            $user = new User();
            $user->setUsername($request->request->get('username'));
            
            $roles = [$request->request->get('role', 'ROLE_WORKER')];
            $user->setRoles($roles);

            $hashedPassword = $passwordHasher->hashPassword($user, $request->request->get('password'));
            $user->setPasswordHash($hashedPassword);

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Nouvel employé ajouté : ' . $user->getUsername());
            return $this->redirectToRoute('admin_user_index');
        }

        return $this->render('admin/user/new.html.twig');
    }

    #[Route('/reset-password/{id}', name: 'admin_user_reset_password', methods: ['POST'])]
    public function resetPassword(Request $request, User $user, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        $newPassword = $request->request->get('new_password');
        if ($newPassword) {
            $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
            $user->setPasswordHash($hashedPassword);
            $entityManager->flush();
            $this->addFlash('success', 'Le mot de passe de ' . $user->getUserIdentifier() . ' a été réinitialisé.');
        }
        return $this->redirectToRoute('admin_user_index');
    }

    #[Route('/delete/{id}', name: 'admin_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        // Don't let an admin delete themselves
        if ($user === $this->getUser()) {
            $this->addFlash('error', 'Vous ne pouvez pas supprimer votre propre compte.');
            return $this->redirectToRoute('admin_user_index');
        }

        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
            $this->addFlash('success', 'Accès révoqué pour cet employé.');
        }
        return $this->redirectToRoute('admin_user_index');
    }
}
