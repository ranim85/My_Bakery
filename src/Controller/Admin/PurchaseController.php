<?php

namespace App\Controller\Admin;

use App\Entity\Purchase;
use App\Repository\PurchaseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/purchase')]
class PurchaseController extends AbstractController
{
    #[Route('/', name: 'admin_purchase_index', methods: ['GET'])]
    public function index(PurchaseRepository $purchaseRepository): Response
    {
        return $this->render('admin/purchase/index.html.twig', [
            'purchases' => $purchaseRepository->findBy([], ['date' => 'DESC']),
        ]);
    }

    #[Route('/new', name: 'admin_purchase_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $purchase = new Purchase();
        if ($request->isMethod('POST')) {
            $purchase->setDescription($request->request->get('description'));
            $purchase->setAmount((float)$request->request->get('amount'));
            $purchase->setDate(new \DateTime($request->request->get('date') ?: 'now'));
            
            $file = $request->files->get('image');
            if ($file) {
                $uploadDir = $this->getParameter('kernel.project_dir').'/public/uploads/purchases';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $filename = uniqid().'.'.$file->getClientOriginalExtension();
                $file->move($uploadDir, $filename);
                $purchase->setImagePath($filename);
            }

            $entityManager->persist($purchase);
            $entityManager->flush();

            return $this->redirectToRoute('admin_purchase_index');
        }

        return $this->render('admin/purchase/new.html.twig', [
            'purchase' => $purchase,
        ]);
    }
}
