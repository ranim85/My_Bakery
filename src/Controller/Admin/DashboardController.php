<?php

namespace App\Controller\Admin;

use App\Repository\SalesLogRepository;
use App\Repository\PurchaseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class DashboardController extends AbstractController
{
    #[Route('/', name: 'admin_dashboard')]
    public function index(Request $request, SalesLogRepository $salesLogRepository, PurchaseRepository $purchaseRepository): Response
    {
        $filterDateStr = $request->query->get('date', 'today'); // Default to 'today'
        
        $salesQuery = $salesLogRepository->createQueryBuilder('s')->orderBy('s.date', 'DESC');
        $purchaseQuery = $purchaseRepository->createQueryBuilder('p')->orderBy('p.date', 'DESC');

        if ($filterDateStr && $filterDateStr !== 'all') {
            try {
                if ($filterDateStr === 'today') {
                    $filterDate = new \DateTime('today');
                    $salesQuery->andWhere('s.date = :date')->setParameter('date', $filterDate->format('Y-m-d'));
                    $purchaseQuery->andWhere('p.date = :date')->setParameter('date', $filterDate->format('Y-m-d'));
                } elseif ($filterDateStr === 'month') {
                    $start = new \DateTime('first day of this month');
                    $end = new \DateTime('first day of next month');
                    $salesQuery->andWhere('s.date >= :start AND s.date < :end')
                               ->setParameter('start', $start->format('Y-m-d'))
                               ->setParameter('end', $end->format('Y-m-d'));
                    $purchaseQuery->andWhere('p.date >= :start AND p.date < :end')
                               ->setParameter('start', $start->format('Y-m-d'))
                               ->setParameter('end', $end->format('Y-m-d'));
                } elseif ($filterDateStr === 'year') {
                    $start = new \DateTime('first day of January this year');
                    $end = new \DateTime('first day of January next year');
                    $salesQuery->andWhere('s.date >= :start AND s.date < :end')
                               ->setParameter('start', $start->format('Y-m-d'))
                               ->setParameter('end', $end->format('Y-m-d'));
                    $purchaseQuery->andWhere('p.date >= :start AND p.date < :end')
                               ->setParameter('start', $start->format('Y-m-d'))
                               ->setParameter('end', $end->format('Y-m-d'));
                } else {
                    $filterDate = new \DateTime($filterDateStr);
                    $salesQuery->andWhere('s.date = :date')->setParameter('date', $filterDate->format('Y-m-d'));
                    $purchaseQuery->andWhere('p.date = :date')->setParameter('date', $filterDate->format('Y-m-d'));
                }
            } catch (\Exception $e) {
                // Ignore invalid
            }
        }

        $salesLogs = $salesQuery->getQuery()->getResult();
        $purchases = $purchaseQuery->getQuery()->getResult();
        
        $totalCalculated = 0;
        $totalAnnounced = 0;
        $discrepancies = [];

        foreach ($salesLogs as $log) {
            $totalCalculated += $log->getCalculatedTotal();
            $totalAnnounced += $log->getAnnouncedTotal();

            $diff = $log->getCalculatedTotal() - $log->getAnnouncedTotal();
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
            'currentDateFilter' => $filterDateStr,
        ]);
    }
}
