<?php

namespace App\Controller;

use App\Entity\Device;
use App\Repository\DeviceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/devices')]
class DeviceController extends AbstractController
{

    #[Route('', name: 'devices', methods: ['GET'])]
    public function getDeviceList(DeviceRepository $deviceRepository, SerializerInterface $serializer): JsonResponse
    {
        $deviceList = $deviceRepository->findAll();
        $jsonDeviceList = $serializer->serialize($deviceList, 'json');
        return new JsonResponse($jsonDeviceList, Response::HTTP_OK, [], true);
    }


    #[Route('/{id}', name: 'detailDevices', methods: ['GET'])]
    public function getDetailDevice(Device $device, SerializerInterface $serializer): JsonResponse
    {
        $jsonDevice = $serializer->serialize($device, 'json');
        return new JsonResponse($jsonDevice, Response::HTTP_OK, ['accept' => 'json'], true);
    }


}
