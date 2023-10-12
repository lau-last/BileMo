<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Repository\CustomerRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/customers')]
class CustomerController extends AbstractController
{

    #[Route('', name: 'customers', methods: ['GET'])]
    public function getCustomerList(CustomerRepository $customerRepository, SerializerInterface $serializer): JsonResponse
    {
        $customerList = $customerRepository->findAll();
        $jsonCustomerList = $serializer->serialize($customerList, 'json', ['groups' => 'getCustomers']);
        return new JsonResponse($jsonCustomerList, Response::HTTP_OK, [], true);
    }


    #[Route('/{id}', name: 'detailCustomers', methods: ['GET'])]
    public function getDetailCustomer(Customer $customer, SerializerInterface $serializer): JsonResponse
    {
        $jsonDetailCustomer = $serializer->serialize($customer, 'json', ['groups' => 'getCustomers']);
        return new JsonResponse($jsonDetailCustomer, Response::HTTP_OK, [], true);
    }


    #[Route('/{id}', name: 'deleteCustomers', methods: ['DELETE'])]
    public function deleteCustomer(Customer $customer, EntityManagerInterface $manager): JsonResponse
    {
        $manager->remove($customer);
        $manager->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }


    #[Route('', name: 'createCustomers', methods: ['POST'])]
    public function createCustomer(
        SerializerInterface    $serializer,
        Request                $request,
        EntityManagerInterface $manager,
        UrlGeneratorInterface  $urlGenerator,
        UserRepository         $userRepository): JsonResponse
    {
        $customer = $serializer->deserialize($request->getContent(), Customer::class, 'json');
        $content = $request->toArray();
        $idUser = $content['idUser'] ?? -1;
        $customer->setUser($userRepository->find($idUser));
        $manager->persist($customer);
        $manager->flush();
        $jsonCustomer = $serializer->serialize($customer, 'json', ['groups' => 'getCustomers']);
        $location = $urlGenerator->generate('detailCustomers', ['id' => $customer->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        return new JsonResponse($jsonCustomer, Response::HTTP_CREATED, ['location' => $location], true);
    }


    #[Route('/{id}', name: 'updateCustomers', methods: ['PUT'])]
    public function updateCustomer(
        SerializerInterface    $serializer,
        Request                $request,
        EntityManagerInterface $manager,
        UserRepository         $userRepository,
        Customer               $currentCustomer): JsonResponse
    {
        $updateCustomer = $serializer->deserialize($request->getContent(), Customer::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $currentCustomer]);
        $content = $request->toArray();
        $idUser = $content['idUser'] ?? -1;
        $currentCustomer->setUser($userRepository->find($idUser));
        $manager->persist($updateCustomer);
        $manager->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }


}
