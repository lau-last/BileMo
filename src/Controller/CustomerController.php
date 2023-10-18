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
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;

/**
 * @OA\Response(
 *     response=200,
 *     description="Retourne la liste des livres",
 *     @OA\JsonContent(
 *        type="array",
 *        @OA\Items(ref=@Model(type=Customer::class, groups={"getCustomers"}))
 *     )
 * )
 * @OA\Parameter(
 *     name="page",
 *     in="query",
 *     description="La page que l'on veut récupérer",
 *     @OA\Schema(type="int")
 * )
 * @OA\Parameter(
 *     name="limit",
 *     in="query",
 *     description="Le nombre d'éléments que l'on veut récupérer",
 *     @OA\Schema(type="int")
 * )
 * @OA\Tag(name="Customers")
 */

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
        TagAwareCacheInterface $cache,
        VersioningService      $versioningService): JsonResponse
    {
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


    #[Route('/{id}', name: 'detailCustomers', methods: ['GET'])]
    public function getDetailCustomer(Customer $customer, SerializerInterface $serializer, VersioningService $versioningService): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        if ($customer->getUser()->getId() !== $user->getId()){
            Throw new HttpException(403, 'You dont have a User with this ID');
        }
        $version = $versioningService->getVersion();
        $context = SerializationContext::create()->setGroups(['getCustomers'])->setVersion($version);
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
        /** @var User $user */
        $user = $this->getUser();
        if ($customer->getUser()->getId() !== $user->getId()){
            Throw new HttpException(403, 'You dont have a User with this ID');
        }
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
        TagAwareCacheInterface $cache,
        VersioningService      $versioningService): JsonResponse
    {
        /** @var Customer $customer */
        $customer = $serializer->deserialize($request->getContent(), Customer::class, 'json');
        $customer->setUser($this->getUser());
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
        /** @var User $user */
        $user = $this->getUser();
        if ($currentCustomer->getUser()->getId() !== $user->getId()){
            Throw new HttpException(403, 'You dont have a User with this ID');
        }

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
            ->setComment($updateCustomer->getComment())
            ->setUpdatedAt(new \DateTime());

        $errorValidate->check($updateCustomer);
        $manager->persist($updateCustomer);
        $manager->flush();
        $cache->invalidateTags(['customersCache']);
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }


}
