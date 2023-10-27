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
    ): JsonResponse
    {
//        I retrieve the user who uses the API
        /** @var User $user */
        $user = $this->getUser();
//        I get the url parameters
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 3);
//        I name the cache
        $idCache = 'getAllCustomers-' . $user->getId() . '-' . $page . '-' . $limit;
//        I get a list
        $customerList = $cache->get($idCache, function (ItemInterface $item) use ($customerRepository, $page, $limit, $user) {
            $item->tag('customersCache');
            return $customerRepository->findAllWithPagination($page, $limit, $user);
        });
//        I get a version
        $version = $versioningService->getVersion();
//        I get serialize context (group and version)
        $context = SerializationContext::create()->setGroups(['getCustomers'])->setVersion($version);
//        I serialize the list
        $jsonCustomerList = $serializer->serialize($customerList, 'json', $context);
//        I return a response
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
//        I retrieve the user who uses the API
        /** @var User $user */
        $user = $this->getUser();
//        I check that he is not requesting a client who is not his
        if ($customer->getUser()->getId() !== $user->getId()) {
            throw new HttpException(403, 'You dont have a User with this ID');
        }
//        I get a version
        $version = $versioningService->getVersion();
//        I get serialize context (group and version)
        $context = SerializationContext::create()->setGroups(['getCustomers'])->setVersion($version);
//        I serialize the request
        $jsonDetailCustomer = $serializer->serialize($customer, 'json', $context);
//        I return a response
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
//        I retrieve the user who uses the API
        /** @var User $user */
        $user = $this->getUser();
//        I check that he is not requesting a client who is not his
        if ($customer->getUser()->getId() !== $user->getId()) {
            throw new HttpException(403, 'You dont have a User with this ID');
        }
//        I delete
        $manager->remove($customer);
//        I am recording
        $manager->flush();
//        I delete the cache
        $cache->invalidateTags(['customersCache']);
//        I return a response
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
    ): JsonResponse
    {
//        I get the body and deserialize it
        /** @var Customer $customer */
        $customer = $serializer->deserialize($request->getContent(), Customer::class, 'json');
//        I credit him with the customer relationship he created.
        $customer->setUser($this->getUser());
//        I create a creation date
        $customer->setCreatedAt(new \DateTime());
//        I check that there are no errors
        $errorValidate->check($customer);
//        I enter it into the database
        $manager->persist($customer);
//        I record
        $manager->flush();
//        I delete the cache
        $cache->invalidateTags(['customersCache']);
//        I get a version
        $version = $versioningService->getVersion();
//        I get serialize context (group and version)
        $context = SerializationContext::create()->setGroups(['getCustomers'])->setVersion($version);
//        I serialize the request
        $jsonCustomer = $serializer->serialize($customer, 'json', $context);
//        I create an url of the created object
        $location = $urlGenerator->generate('detailCustomers', ['id' => $customer->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
//        I return a response
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
    ): JsonResponse
    {
//        I retrieve the user who uses the API
        /** @var User $user */
        $user = $this->getUser();
//        I check that he is not requesting a client who is not his
        if ($currentCustomer->getUser()->getId() !== $user->getId()) {
            throw new HttpException(403, 'You dont have a User with this ID');
        }
//        I deserialize the body which contains the updater object
        /** @var Customer $updateCustomer */
        $updateCustomer = $serializer->deserialize($request->getContent(), Customer::class, 'json');
//        I check that there are no errors
        $errorValidate->check($updateCustomer);
//        I set the new values
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
//        I enter it into the database
        $manager->persist($updateCustomer);
//        I record
        $manager->flush();
//        I delete the cache
        $cache->invalidateTags(['customersCache']);
//        I return a response
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }


}
