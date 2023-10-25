<?php

namespace App\Controller;

use App\Entity\Device;
use App\Repository\DeviceRepository;
use App\Service\ErrorValidate;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use phpDocumentor\Reflection\Types\Integer;
use Psr\Cache\InvalidArgumentException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

#[Route('/api/devices')]
class DeviceController extends AbstractController
{

    /**
     * Returns device information.
     * @throws InvalidArgumentException
     */
    #[Route('', name: 'devices', methods: ['GET'])]
    #[OA\Response(response: 200, description: 'Successful response', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: new Model(type: Device::class))))]
    #[OA\Parameter(name: 'page', description: 'The page of the result', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 1))]
    #[OA\Parameter(name: 'limit', description: 'Number of elements per page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 3))]
    #[OA\Tag(name: 'Devices')]
    public function getDeviceList(
        DeviceRepository       $deviceRepository,
        SerializerInterface    $serializer,
        Request                $request,
        TagAwareCacheInterface $cache): JsonResponse
    {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 3);
        $idCache = 'getAllDevices-' . $page . '-' . $limit;

        $deviceList = $cache->get($idCache, function (ItemInterface $item) use ($deviceRepository, $page, $limit){
            $item->tag('devicesCache');
            return $deviceRepository->findAllWithPagination($page, $limit);
        });

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


    /**
     * Delete one device specific.
     * @throws InvalidArgumentException
     */
    #[Route('/{id}', name: 'deleteDevices', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN', message: 'You do not have sufficient rights to delete a device')]
    #[OA\Response(response: 204, description: 'Your device has been deleted')]
    #[OA\Tag(name: 'Devices')]
    public function deleteDevice(Device $device, EntityManagerInterface $manager, TagAwareCacheInterface $cache): JsonResponse
    {
        $manager->remove($device);
        $manager->flush();
        $cache->invalidateTags(['devicesCache']);
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }


    /**
     * Create one device.
     * @throws InvalidArgumentException
     */
    #[Route('', name: 'createDevices', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN', message: 'You do not have sufficient rights to create a device')]
    #[OA\Response(response: 201, description: 'Your device has been created', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: new Model(type: Device::class))))]
    #[OA\Tag(name: 'Devices')]
    public function createDevice(
        SerializerInterface    $serializer,
        Request                $request,
        EntityManagerInterface $manager,
        UrlGeneratorInterface  $urlGenerator,
        ErrorValidate          $errorValidate,
        TagAwareCacheInterface $cache): JsonResponse
    {
        /** @var Device $device */
        $device = $serializer->deserialize($request->getContent(), Device::class, 'json');
        $device->setCreatedAt(new \DateTime());

        $errorValidate->check($device);
        $errorValidate->checkVersionAndBuildNumber($device);
        
        $manager->persist($device);
        $manager->flush();
        $cache->invalidateTags(['devicesCache']);
        $jsonDevice = $serializer->serialize($device, 'json');
        $location = $urlGenerator->generate('detailDevices', ['id' => $device->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        return new JsonResponse($jsonDevice, Response::HTTP_CREATED, ['location' => $location], true);
    }


    /**
     * Update one device specific.
     * @throws InvalidArgumentException
     */
    #[Route('/{id}', name: 'updateDevices', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN', message: 'You do not have sufficient rights to modify a device')]
    #[OA\Response(response: 204, description: 'Your device has been updated')]
    #[OA\Tag(name: 'Devices')]
    public function updateDevice(
        SerializerInterface    $serializer,
        Request                $request,
        EntityManagerInterface $manager,
        Device                 $currentDevice,
        ErrorValidate          $errorValidate,
        TagAwareCacheInterface $cache): JsonResponse
    {
        /** @var Device $updateDevice */
        $updateDevice = $serializer->deserialize($request->getContent(), Device::class, 'json');

        $errorValidate->check($updateDevice);
        $errorValidate->checkVersionAndBuildNumber($updateDevice);

        $currentDevice
            ->setModelName($updateDevice->getModelName())
            ->setManufacturer($updateDevice->getManufacturer())
            ->setPlatform($updateDevice->getPlatform())
            ->setSerialNumber($updateDevice->getSerialNumber())
            ->setVersion($updateDevice->getVersion())
            ->setBuildNumber($updateDevice->getBuildNumber())
            ->setCreatedAt($updateDevice->getCreatedAt())
            ->setUpdatedAt(new \DateTime());

        $manager->persist($updateDevice);
        $manager->flush();
        $cache->invalidateTags(['devicesCache']);
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }



}
