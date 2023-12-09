<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as Controller;
use Symfony\Component\HttpFoundation\Response;
use function Symfony\Component\String\u;

class AbstractController extends Controller
{
    /**
     * @inheritDoc
     */
    protected function render(string $view, array $parameters = [], Response $response = null): Response
    {
        return parent::render(vsprintf('%s/%s.twig', [
            $this->getIdentifier(),
            $view,
        ]), array_merge($parameters, $this->getLayoutParameters()), $response);
    }

    /**
     * Global parameters used for layout.
     */
    protected function getLayoutParameters(): array
    {
        return [
            'navbar_items' => $this->getParameter('frontend.navbar.items'),
            'footer_features' => $this->getParameter('frontend.footer.features'),
            'footer_resources' => $this->getParameter('frontend.footer.resources'),
            'footer_about' => $this->getParameter('frontend.footer.about'),
        ];
    }

    /**
     * Get idientifier for current controller (`snake_case`).
     */
    protected function getIdentifier(): string
    {
        return (string)u(call_user_func(function (): string {
            $items = explode('\\', get_called_class());

            return end($items);
        }))->snake()->replaceMatches('/_controller$/', '');
    }
}
