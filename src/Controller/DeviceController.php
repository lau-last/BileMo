<?php

namespace App\Controller;

use App\Entity\Device;
use App\Repository\DeviceRepository;
use JMS\Serializer\Serializer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/devices')]
class DeviceController extends AbstractController
{

    #[Route('', name: 'devices', methods: ['GET'])]
    public function getDeviceList(DeviceRepository $deviceRepository, Serializer $serializer, Request $request): JsonResponse
    {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 3);
        $deviceList = $deviceRepository->findAllWithPagination($page, $limit);
        $jsonDeviceList = $serializer->serialize($deviceList, 'json');
        return new JsonResponse($jsonDeviceList, Response::HTTP_OK, [], true);
    }


    #[Route('/{id}', name: 'detailDevices', methods: ['GET'])]
    public function getDetailDevice(Device $device, Serializer $serializer): JsonResponse
    {
        $jsonDevice = $serializer->serialize($device, 'json');
        return new JsonResponse($jsonDevice, Response::HTTP_OK, ['accept' => 'json'], true);
    }


}
