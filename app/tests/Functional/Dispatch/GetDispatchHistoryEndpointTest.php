<?php

declare(strict_types=1);

namespace App\Tests\Functional\Dispatch;

use App\Courier\Domain\Entity\Courier;
use App\Dispatch\Domain\Entity\Dispatch;
use App\Dispatch\Domain\Entity\DispatchHistory;
use App\Package\Domain\Entity\ShipmentPackage;
use App\Shared\Infrastructure\Security\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class GetDispatchHistoryEndpointTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::ensureKernelShutdown();

        $this->client = static::createClient();
        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $this->recreateSchema();
        $this->loadFixtures();
    }

    protected function tearDown(): void
    {
        if (isset($this->entityManager) && $this->entityManager->isOpen()) {
            $this->entityManager->close();
        }

        parent::tearDown();
    }

    public function testItReturnsDispatchHistoryJson(): void
    {
        $this->client->request('POST', '/api/login_check', [
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        self::assertResponseIsSuccessful();

        $loginResponse = json_decode(
            (string) $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        self::assertTrue($loginResponse['success']);
        self::assertArrayHasKey('token', $loginResponse['data']);

        $token = $loginResponse['data']['token'];
        $dispatchId = $this->entityManager->getRepository(Dispatch::class)->findOneBy([])?->getId();

        self::assertNotNull($dispatchId);

        $this->client->request(
            'GET',
            sprintf('/api/dispatches/%s/history', $dispatchId),
            server: [
                'HTTP_AUTHORIZATION' => sprintf('Bearer %s', $token),
            ]
        );

        self::assertResponseIsSuccessful();

        $response = json_decode(
            (string) $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        self::assertTrue($response['success']);
        self::assertNull($response['message']);
        self::assertSame($dispatchId, $response['data']['dispatchId']);
        self::assertCount(1, $response['data']['history']);
        self::assertSame('pending', $response['data']['history'][0]['previousStatus']);
        self::assertSame('assigned', $response['data']['history'][0]['newStatus']);
        self::assertNotEmpty($response['data']['history'][0]['changedAt']);
    }

    private function recreateSchema(): void
    {
        $metadata = [
            $this->entityManager->getClassMetadata(User::class),
            $this->entityManager->getClassMetadata(Courier::class),
            $this->entityManager->getClassMetadata(ShipmentPackage::class),
            $this->entityManager->getClassMetadata(Dispatch::class),
            $this->entityManager->getClassMetadata(DispatchHistory::class),
        ];

        $tool = new SchemaTool($this->entityManager);
        $tool->dropSchema($metadata);
        $tool->createSchema($metadata);
    }

    private function loadFixtures(): void
    {
        $passwordHasher = self::getContainer()->get(UserPasswordHasherInterface::class);

        $user = new User('admin@example.com', ['ROLE_ADMIN']);
        $user->setPassword($passwordHasher->hashPassword($user, 'password'));

        $package = new ShipmentPackage(
            'TRK-TEST-001',
            'John Doe',
            'Main street 123',
            1.5,
            'Test package'
        );

        $dispatch = new Dispatch(
            $package,
            'DSP-TEST-001',
            'Warehouse 1',
            'Customer address',
            'Functional test'
        );

        $history = new DispatchHistory($dispatch, 'pending', 'assigned');

        $this->entityManager->persist($user);
        $this->entityManager->persist($package);
        $this->entityManager->persist($dispatch);
        $this->entityManager->persist($history);
        $this->entityManager->flush();
    }
}
