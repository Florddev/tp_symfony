<?php
namespace App\Controller;

use App\Repository\ActivityRepository;
use App\Repository\EventRepository;
use App\Repository\PlaceRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function home(
        EventRepository $eventRepository,
        ActivityRepository $activityRepository,
        PlaceRepository $placeRepository,
        UserRepository $userRepository
    ): Response {
        $events = $eventRepository->createQueryBuilder('e')
            ->where('e.begin_at >= :now')
            ->setParameter('now', new \DateTime())
            ->orderBy('e.begin_at', 'ASC')
            ->setMaxResults(6)
            ->getQuery()
            ->getResult();

        $statistics = [];
        if ($this->isGranted('ROLE_ADMIN')) {
            $statistics = [
                'events' => $eventRepository->count([]),
                'activities' => $activityRepository->count([]),
                'places' => $placeRepository->count([]),
                'users' => $userRepository->count([]),
            ];
        }

        return $this->render('index.html.twig', [
            'events' => $events,
            'totalEvents' => $statistics['events'] ?? 0,
            'totalActivities' => $statistics['activities'] ?? 0,
            'totalPlaces' => $statistics['places'] ?? 0,
            'totalUsers' => $statistics['users'] ?? 0,
        ]);
    }
}