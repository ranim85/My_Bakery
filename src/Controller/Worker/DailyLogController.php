<?php

namespace App\Controller\Worker;

use App\Entity\ProductionLog;
use App\Entity\SalesLog;
use App\Repository\ProductRepository;
use App\Repository\ProductionLogRepository;
use App\Repository\SalesLogRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/worker')]
class DailyLogController extends AbstractController
{
    #[Route('/', name: 'worker_daily_entry', methods: ['GET', 'POST'])]
    public function index(
        Request $request, 
        ProductRepository $productRepository,
        ProductionLogRepository $productionLogRepository,
        SalesLogRepository $salesLogRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $products = $productRepository->findAll();
        
        $today = new \DateTime('today');
        $todayLogs = $salesLogRepository->createQueryBuilder('s')
            ->where('s.date >= :today')
            ->setParameter('today', $today)
            ->getQuery()
            ->getResult();

        $submittedProductIds = array_map(function($log) {
            return $log->getProduct()->getId();
        }, $todayLogs);

        $availableProducts = array_filter($products, function($p) use ($submittedProductIds) {
            return !in_array($p->getId(), $submittedProductIds);
        });

        if ($request->isMethod('POST')) {
            $productId = $request->request->get('product_id');
            $qtyProduced = (int) $request->request->get('quantity_produced');
            $qtySold = (int) $request->request->get('quantity_sold');
            $announcedTotal = (float) $request->request->get('announced_total');
            $date = new \DateTime($request->request->get('date') ?: 'now');
            
            $product = $productRepository->find($productId);
            if (!$product) {
                $this->addFlash('error', 'Produit invalide. Veuillez réessayer.');
                return $this->redirectToRoute('worker_daily_entry');
            }

            if ($qtyProduced > 0) {
                $pLog = new ProductionLog();
                $pLog->setProduct($product);
                $pLog->setQuantity($qtyProduced);
                $pLog->setDate($date);
                $entityManager->persist($pLog);
            }

            if ($qtySold > 0 || $announcedTotal > 0) {
                $sLog = new SalesLog();
                $sLog->setProduct($product);
                $sLog->setQuantitySold($qtySold);
                $sLog->setAnnouncedTotal($announcedTotal);
                $sLog->setCalculatedTotal($qtySold * $product->getPrice());
                $sLog->setDate($date);
                $sLog->setWorker($this->getUser());
                $entityManager->persist($sLog);
            }

            $entityManager->flush();

            $this->addFlash('success', 'La déclaration de ' . $product->getName() . ' a été validée !');
            return $this->redirectToRoute('worker_daily_entry');
        }

        // Fetch production logs separately to map amounts to the table
        $prodLogs = $productionLogRepository->createQueryBuilder('p')
            ->where('p.date >= :today')
            ->setParameter('today', $today)
            ->getQuery()
            ->getResult();
            
        $prodLogsMapped = [];
        foreach ($prodLogs as $pl) {
            $prodLogsMapped[$pl->getProduct()->getId()] = $pl->getQuantity();
        }

        return $this->render('worker/daily_entry.html.twig', [
            'products' => $availableProducts,
            'todayLogs' => $todayLogs,
            'prodLogsMapped' => $prodLogsMapped,
        ]);
    }

    #[Route('/delete/{id}', name: 'worker_entry_delete', methods: ['POST'])]
    public function deleteEntry(
        SalesLog $salesLog, 
        ProductionLogRepository $productionLogRepository, 
        EntityManagerInterface $entityManager
    ): Response {
        $product = $salesLog->getProduct();
        $date = $salesLog->getDate() ? clone $salesLog->getDate() : new \DateTime('today');

        // Delete associated ProductionLog
        $pLog = $productionLogRepository->findOneBy([
            'product' => $product,
            'date' => $date
        ]);

        if ($pLog) {
            $entityManager->remove($pLog);
        }

        $entityManager->remove($salesLog);
        $entityManager->flush();

        $this->addFlash('warning', 'La saisie pour ' . $product->getName() . ' a été annulée.');
        return $this->redirectToRoute('worker_daily_entry');
    }

    #[Route('/checkout', name: 'worker_checkout', methods: ['POST'])]
    public function checkout(Request $request): Response
    {
        $file = $request->files->get('daily_note_image');
        if ($file) {
            $uploadDir = $this->getParameter('kernel.project_dir').'/public/uploads/notes';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            // Use safe standard name
            $username = preg_replace('/[^a-zA-Z0-9]/', '_', $this->getUser()->getUserIdentifier());
            $filename = 'Cloture_' . date('Y-m-d') . '_' . $username . '.' . $file->getClientOriginalExtension();
            $file->move($uploadDir, $filename);
        }

        $this->addFlash('success', 'Votre fin de service est validée avec la preuve papier transmise ! Bonne fin de journée.');
        return $this->redirectToRoute('worker_daily_entry');
        // Normally this would redirect to logout directly, but keeping them on screen to read the success message is nice.
    }
}
