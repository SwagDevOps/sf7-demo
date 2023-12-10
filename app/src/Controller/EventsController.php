<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\EventRepository;

/**
 * Provide a simple planner app.
 */
class EventsController extends AbstractController
{
    /**
     * Index page, with events sorted by ``begin_date`` (excludes events with a ``end_date`` greater than now).
     */
    #[Route('/', name: 'home')]
    public function index(Request $request, EventRepository $repository): Response
    {
        $events = $repository->paginate(call_user_func(function () use ($request): int {
            return $request->query->getInt('page', 1);
        }));

        return $this->render('index.html', [
            'events' => $events,
        ]);
    }
}
