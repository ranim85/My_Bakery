<?php

namespace App\Controller\Admin;

use App\Repository\SalesLogRepository;
use App\Repository\PurchaseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class DashboardController extends AbstractController
{
    #[Route('/', name: 'admin_dashboard')]
    public function index(SalesLogRepository $salesLogRepository, PurchaseRepository $purchaseRepository): Response
    {
        $salesLogs = $salesLogRepository->findBy([], ['date' => 'DESC']);
        $purchases = $purchaseRepository->findBy([], ['date' => 'DESC']);
        
        $totalCalculated = 0;
        $totalAnnounced = 0;
        $discrepancies = [];

        foreach ($salesLogs as $log) {
            $totalCalculated += $log->getCalculatedTotal();
            $totalAnnounced += $log->getAnnouncedTotal();

            $diff = $log->getCalculatedTotal() - $log->getAnnouncedTotal();
            // Store logs where worker announced total differs from formula based calculation
            if (abs($diff) > 0.01) {
                $discrepancies[] = [
                    'log' => $log,
                    'difference' => $diff
                ];
            }
        }
        
        $totalExpenses = 0;
        foreach ($purchases as $p) {
            $totalExpenses += $p->getAmount();
        }

        return $this->render('admin/dashboard/index.html.twig', [
            'totalCalculated' => $totalCalculated,
            'totalAnnounced' => $totalAnnounced,
            'totalDiscrepancy' => $totalCalculated - $totalAnnounced,
            'totalExpenses' => $totalExpenses,
            'netProfit' => $totalAnnounced - $totalExpenses,
            'discrepancies' => $discrepancies,
            'recentLogs' => array_slice($salesLogs, 0, 10),
        ]);
    }
}
