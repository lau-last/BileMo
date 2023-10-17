<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\User;
use App\Repository\CustomerRepository;
use App\Service\ErrorValidate;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Psr\Cache\InvalidArgumentException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;


#[Route('/api/customers')]
class CustomerController extends AbstractController
{


    /**
     * @throws InvalidArgumentException
     */
    #[Route('', name: 'customers', methods: ['GET'])]
    public function getCustomerList(
        CustomerRepository     $customerRepository,
        SerializerInterface    $serializer,
        Request                $request,
        TagAwareCacheInterface $cache): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $userId = $user->getId();
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 3);
        $idCache = 'getAllCustomers-' . $userId . '-' . $page . '-' . $limit;

        $customerList = $cache->get($idCache, function (ItemInterface $item) use ($customerRepository, $page, $limit, $userId) {
            echo('Not in the cache');
            $item->tag('customersCache');
            return $customerRepository->findAllWithPagination($page, $limit, $userId);
        });

        $context = SerializationContext::create()->setGroups(['getCustomers']);
        $jsonCustomerList = $serializer->serialize($customerList, 'json', $context);
        return new JsonResponse($jsonCustomerList, Response::HTTP_OK, [], true);
    }


    #[Route('/{id}', name: 'detailCustomers', methods: ['GET'])]
    public function getDetailCustomer(Customer $customer, SerializerInterface $serializer): JsonResponse
    {
        $context = SerializationContext::create()->setGroups(['getCustomers']);
        $jsonDetailCustomer = $serializer->serialize($customer, 'json', $context);
        return new JsonResponse($jsonDetailCustomer, Response::HTTP_OK, [], true);
    }


    /**
     * @throws InvalidArgumentException
     */
    #[Route('/{id}', name: 'deleteCustomers', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN', message: 'You do not have sufficient rights to delete a customer')]
    public function deleteCustomer(Customer $customer, EntityManagerInterface $manager, TagAwareCacheInterface $cache): JsonResponse
    {
        $manager->remove($customer);
        $manager->flush();
        $cache->invalidateTags(['customersCache']);
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }


    /**
     * @throws InvalidArgumentException
     */
    #[Route('', name: 'createCustomers', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN', message: 'You do not have sufficient rights to create a customer')]
    public function createCustomer(
        SerializerInterface    $serializer,
        Request                $request,
        EntityManagerInterface $manager,
        UrlGeneratorInterface  $urlGenerator,
        ErrorValidate          $errorValidate,
        TagAwareCacheInterface $cache): JsonResponse
    {
        $customer = $serializer->deserialize($request->getContent(), Customer::class, 'json');
        $errorValidate->check($customer);
        $manager->persist($customer);
        $manager->flush();
        $cache->invalidateTags(['customersCache']);
        $context = SerializationContext::create()->setGroups(['getCustomers']);
        $jsonCustomer = $serializer->serialize($customer, 'json', $context);
        $location = $urlGenerator->generate('detailCustomers', ['id' => $customer->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        return new JsonResponse($jsonCustomer, Response::HTTP_CREATED, ['location' => $location], true);
    }


    /**
     * @throws InvalidArgumentException
     */
    #[Route('/{id}', name: 'updateCustomers', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN', message: 'You do not have sufficient rights to modify a customer')]
    public function updateCustomer(
        SerializerInterface    $serializer,
        Request                $request,
        EntityManagerInterface $manager,
        Customer               $currentCustomer,
        ErrorValidate          $errorValidate,
        TagAwareCacheInterface $cache): JsonResponse
    {
        /** @var Customer $updateCustomer */
        $updateCustomer = $serializer->deserialize($request->getContent(), Customer::class, 'json');

        $currentCustomer
            ->setFirstname($updateCustomer->getFirstname())
            ->setLastname($updateCustomer->getLastname())
            ->setEmail($updateCustomer->getEmail())
            ->setGender($updateCustomer->getGender())
            ->setDateOfBirth($updateCustomer->getDateOfBirth())
            ->setPhoneNumber($updateCustomer->getPhoneNumber())
            ->setAddress($updateCustomer->getAddress())
            ->setCompany($updateCustomer->getCompany())
            ->setUpdatedAt(new \DateTime());

        $errorValidate->check($updateCustomer);
        $manager->persist($updateCustomer);
        $manager->flush();
        $cache->invalidateTags(['customersCache']);
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }


}