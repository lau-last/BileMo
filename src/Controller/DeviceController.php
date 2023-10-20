<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\Device;
use App\Repository\DeviceRepository;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;


#[Route('/api/devices')]
class DeviceController extends AbstractController
{

    /**
     * Returns device information.
     */
    #[Route('', name: 'devices', methods: ['GET'])]
    #[OA\Response(response: 200, description: 'Successful response', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: new Model(type: Device::class))))]
    #[OA\Parameter(name: 'page', description: 'The page of the result', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 1))]
    #[OA\Parameter(name: 'limit', description: 'Number of elements per page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 3))]
    #[OA\Tag(name: 'Devices')]
    public function getDeviceList(DeviceRepository $deviceRepository, SerializerInterface $serializer, Request $request): JsonResponse
    {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 3);
        $deviceList = $deviceRepository->findAllWithPagination($page, $limit);
        $jsonDeviceList = $serializer->serialize($deviceList, 'json');
        return new JsonResponse($jsonDeviceList, Response::HTTP_OK, [], true);
    }

    /**
     * Returns information about a device.
     */
    #[Route('/{id}', name: 'detailDevices', methods: ['GET'])]
    #[OA\Response(response: 200, description: 'Successful response', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: new Model(type: Device::class))))]
    #[OA\Tag(name: 'Devices')]
    public function getDetailDevice(Device $device, SerializerInterface $serializer): JsonResponse
    {
        $jsonDevice = $serializer->serialize($device, 'json');
        return new JsonResponse($jsonDevice, Response::HTTP_OK, ['accept' => 'json'], true);
    }


}
