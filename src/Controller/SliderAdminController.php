<?php

declare(strict_types=1);

namespace Mgd\Slider\Controller;

use Mgd\Slider\Service\SliderService;
use Shopware\Core\Framework\Context;
use Shopware\Core\PlatformRequest;
use Shopware\Core\Framework\Log\Package;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Admin API: Liste für den Dropdown im CMS Element + Anlegen (Limit-Prüfung steckt im Service).
 * Ungetestet, ACL fehlt noch.
 */
#[Route(defaults: [PlatformRequest::ATTRIBUTE_ROUTE_SCOPE => ['api']])]
#[Package('framework')]
class SliderAdminController extends AbstractController
{
    public function __construct(private readonly SliderService $sliders)
    {
    }

    #[Route(path: '/api/_action/mgd-slider/list', name: 'api.action.mgd-slider.list', methods: ['GET'])]
    public function list(Context $context): JsonResponse
    {
        $rows = array_map(
            static fn (array $s): array => ['id' => $s['id'], 'name' => $s['name'], 'mode' => $s['config']['mode']],
            $this->sliders->list()
        );

        return new JsonResponse(['data' => $rows]);
    }

    #[Route(path: '/api/_action/mgd-slider/create', name: 'api.action.mgd-slider.create', methods: ['POST'])]
    public function create(Request $request, Context $context): JsonResponse
    {
        $id = $this->sliders->create(
            (string) $request->request->get('name', 'Slider'),
            (array) $request->request->all('config')
        );

        return new JsonResponse(['id' => $id], JsonResponse::HTTP_CREATED);
    }
}
