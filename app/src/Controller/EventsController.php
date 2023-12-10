<?php

namespace App\Controller;

use App\Entity\Event;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Provide a simple planner app.
 */
class EventsController extends AbstractController
{
    /**
     * Index page, with events sorted by ``begin_date`` (excludes events with a ``end_date`` greater than now).
     */
    #[Route('/', name: 'home', methods: ['HEAD', 'GET'])]
    public function index(Request $request, EventRepository $repository): Response
    {
        $events = $repository->paginate(call_user_func(function () use ($request): int {
            return $request->query->getInt('page', 1);
        }));

        return $this->render('index.html', [
            'events' => $events,
        ]);
    }

    #[Route('/events/{id}', name: 'events.edit', methods: ['HEAD', 'GET'])]
    public function edit(int $id, EventRepository $repository): Response
    {
        return $this->render('edit.html', [
            'event' => $repository->find($id),
        ]);
    }

    #[Route('/events/{id}/update', name: 'events.update', methods: ['POST'])]
    public function update(int $id, Request $request, EntityManagerInterface $em, ValidatorInterface $validator): Response
    {
        if (!($event = $em->getRepository(Event::class)->find($id))) {
            throw $this->createNotFoundException("No event found for id ${id}");
        }

        foreach ($request->request->all() as $key => $value) {
            call_user_func_array([$event, sprintf("set%s", ucfirst($key))], [$value]);
        }

        // @todo add notifications
        $errors = $validator->validate($event);
        if (count($errors) <= 0) {
            $em->flush();
        }

        return $this->redirectToRoute('events.edit', [
            'id' => $id,
        ]);
    }
}
