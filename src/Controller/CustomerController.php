<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\User;
use App\Repository\CustomerRepository;
use App\Service\ErrorValidate;
use App\Service\VersioningService;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Psr\Cache\InvalidArgumentException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

#[Route('/api/customers')]
class CustomerController extends AbstractController
{

    /**
     * List information related to your users.
     * @throws InvalidArgumentException
     */
    #[Route('', name: 'customers', methods: ['GET'])]
    #[OA\Response(response: 200, description: 'Successful response', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: new Model(type: Customer::class, groups: ['getCustomers']))))]
    #[OA\Parameter(name: 'page', description: 'The page of the result', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 1))]
    #[OA\Parameter(name: 'limit', description: 'Number of elements per page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 3))]
    #[OA\Tag(name: 'Customers')]
    public function getCustomerList(
        CustomerRepository     $customerRepository,
        SerializerInterface    $serializer,
        Request                $request,
        TagAwareCacheInterface $cache,
        VersioningService      $versioningService
    ): JsonResponse {
        /** @var User $user */
        $user = $this->getUser();
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 3);
        $idCache = 'getAllCustomers-' . $user->getId() . '-' . $page . '-' . $limit;

        $customerList = $cache->get($idCache, function (ItemInterface $item) use ($customerRepository, $page, $limit, $user) {
            $item->tag('customersCache');
            return $customerRepository->findAllWithPagination($page, $limit, $user);
        });

        $version = $versioningService->getVersion();
        $context = SerializationContext::create()->setGroups(['getCustomers'])->setVersion($version);
        $jsonCustomerList = $serializer->serialize($customerList, 'json', $context);
        return new JsonResponse($jsonCustomerList, Response::HTTP_OK, [], true);
    }


    /**
     * List information related to one user specific.
     */
    #[Route('/{id}', name: 'detailCustomers', methods: ['GET'])]
    #[OA\Response(response: 200, description: 'Successful response', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: new Model(type: Customer::class, groups: ['getCustomers']))))]
    #[OA\Tag(name: 'Customers')]
    public function getDetailCustomer(Customer $customer, SerializerInterface $serializer, VersioningService $versioningService): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        if ($customer->getUser()->getId() !== $user->getId()) {
            throw new HttpException(403, 'You dont have a User with this ID');
        }

        $version = $versioningService->getVersion();
        $context = SerializationContext::create()->setGroups(['getCustomers'])->setVersion($version);
        $jsonDetailCustomer = $serializer->serialize($customer, 'json', $context);
        return new JsonResponse($jsonDetailCustomer, Response::HTTP_OK, [], true);
    }


    /**
     * Delete one user specific.
     * @throws InvalidArgumentException
     */
    #[Route('/{id}', name: 'deleteCustomers', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN', message: 'You do not have sufficient rights to delete a customer')]
    #[OA\Response(response: 204, description: 'Your customer has been deleted')]
    #[OA\Tag(name: 'Customers')]
    public function deleteCustomer(Customer $customer, EntityManagerInterface $manager, TagAwareCacheInterface $cache): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        if ($customer->getUser()->getId() !== $user->getId()) {
            throw new HttpException(403, 'You dont have a User with this ID');
        }

        $manager->remove($customer);
        $manager->flush();
        $cache->invalidateTags(['customersCache']);
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }


    /**
     * Create one user.
     * @throws InvalidArgumentException
     */
    #[Route('', name: 'createCustomers', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN', message: 'You do not have sufficient rights to create a customer')]
    #[OA\Response(response: 201, description: 'Your customer has been created', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: new Model(type: Customer::class, groups: ['getCustomers']))))]
    #[OA\Tag(name: 'Customers')]
    public function createCustomer(
        SerializerInterface    $serializer,
        Request                $request,
        EntityManagerInterface $manager,
        UrlGeneratorInterface  $urlGenerator,
        ErrorValidate          $errorValidate,
        TagAwareCacheInterface $cache,
        VersioningService      $versioningService
    ): JsonResponse {
        /** @var Customer $customer */
        $customer = $serializer->deserialize($request->getContent(), Customer::class, 'json');
        $customer->setUser($this->getUser());
        $customer->setCreatedAt(new \DateTime());
        $errorValidate->check($customer);
        $manager->persist($customer);
        $manager->flush();
        $cache->invalidateTags(['customersCache']);
        $version = $versioningService->getVersion();
        $context = SerializationContext::create()->setGroups(['getCustomers'])->setVersion($version);
        $jsonCustomer = $serializer->serialize($customer, 'json', $context);
        $location = $urlGenerator->generate('detailCustomers', ['id' => $customer->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        return new JsonResponse($jsonCustomer, Response::HTTP_CREATED, ['location' => $location], true);
    }


    /**
     * Update one user specific.
     * @throws InvalidArgumentException
     */
    #[Route('/{id}', name: 'updateCustomers', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN', message: 'You do not have sufficient rights to modify a customer')]
    #[OA\Response(response: 204, description: 'Your customer has been updated')]
    #[OA\Tag(name: 'Customers')]
    public function updateCustomer(
        SerializerInterface    $serializer,
        Request                $request,
        EntityManagerInterface $manager,
        Customer               $currentCustomer,
        ErrorValidate          $errorValidate,
        TagAwareCacheInterface $cache
    ): JsonResponse {
        /** @var User $user */
        $user = $this->getUser();

        if ($currentCustomer->getUser()->getId() !== $user->getId()) {
            throw new HttpException(403, 'You dont have a User with this ID');
        }

        /** @var Customer $updateCustomer */
        $updateCustomer = $serializer->deserialize($request->getContent(), Customer::class, 'json');
        $errorValidate->check($updateCustomer);


        $currentCustomer
            ->setFirstname($updateCustomer->getFirstname())
            ->setLastname($updateCustomer->getLastname())
            ->setEmail($updateCustomer->getEmail())
            ->setGender($updateCustomer->getGender())
            ->setDateOfBirth($updateCustomer->getDateOfBirth())
            ->setPhoneNumber($updateCustomer->getPhoneNumber())
            ->setAddress($updateCustomer->getAddress())
            ->setCompany($updateCustomer->getCompany())
            ->setComment($updateCustomer->getComment())
            ->setCreatedAt($updateCustomer->getCreatedAt())
            ->setUpdatedAt(new \DateTime());

        $manager->persist($updateCustomer);
        $manager->flush();
        $cache->invalidateTags(['customersCache']);
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }


}
