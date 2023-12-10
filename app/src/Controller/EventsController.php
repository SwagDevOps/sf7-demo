<?php

namespace App\Controller;

use App\Entity\Event;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Provides events operations.
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

    /**
     * Displays a creation form.
     */
    #[Route('/events/new', name: 'events.create', methods: ['HEAD', 'GET'])]
    public function create(): Response
    {
        return $this->render('create.html', [
            'event' => new Event(),
        ]);
    }

    /**
     * Displays en edit form.
     */
    #[Route('/events/{id}', name: 'events.edit', requirements: ['id' => '\d+'], methods: ['HEAD', 'GET'])]
    public function edit(int $id, EventRepository $repository): Response
    {
        return $this->render('edit.html', [
            'event' => $repository->find($id),
        ]);
    }

    /**
     * Processes update payload.
     */
    #[Route('/events/{id}/update', name: 'events.update', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function update(int $id, Request $request, EntityManagerInterface $em, ValidatorInterface $validator): Response
    {
        if (!($event = $em->getRepository(Event::class)->find($id))) {
            throw $this->createNotFoundException("No event found for id {$id}");
        }

        // @todo add notifications
        $this->store($event, $request->request->all(), $em, $validator);

        return $this->redirectToRoute('events.edit', [
            'id' => $id,
        ]);
    }

    /**
     * Processes creation payload.
     */
    #[Route('/events/insert', name: 'events.insert', methods: ['POST'])]
    public function insert(Request $request, EntityManagerInterface $em, ValidatorInterface $validator): Response
    {
        $event = new Event();

        // @todo add notifications
        $this->store($event, $request->request->all(), $em, $validator);

        return $this->redirectToRoute('events.edit', [
            'id' => $event->getId(),
        ]);
    }

    /**
     * Removes event for given id.
     */
    #[Route('/events/{id}/destroy', name: 'events.destroy', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function destroy(int $id, EntityManagerInterface $em): Response
    {
        if (!($event = $em->getRepository(Event::class)->find($id))) {
            throw $this->createNotFoundException("No event found for id {$id}");
        }

        $em->remove($event);
        $em->flush();

        return $this->redirectToRoute('events.index');
    }

    /**
     * Stores given event (create or update with given payload), returns validation errors.
     */
    protected function store(Event $event, array $payload, EntityManagerInterface $em, ValidatorInterface $validator): ConstraintViolationListInterface
    {
        foreach ($payload as $key => $value) {
            call_user_func_array([$event, sprintf("set%s", ucfirst($key))], [$value]);
        }

        $errors = $validator->validate($event);
        if (count($errors) <= 0) {
            if (!$event->getId()) {
                $em->persist($event);
            }

            $em->flush();
        }

        return $errors;
    }
}
