<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Repository;

use App\Domain\Layout\Page;
use App\Infrastructure\External\Strapi\Http\StrapiApiClient;
use App\Infrastructure\External\Strapi\Model\ContentType\PageContentType;
use App\Infrastructure\External\Strapi\Model\Factory\StrapiPageFactory;
use App\Infrastructure\Repository\StrapiPageRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class StrapiPageRepositoryTest extends TestCase
{
    private StrapiApiClient&MockObject $apiClient;
    private StrapiPageFactory&MockObject $pageFactory;
    private StrapiPageRepository $repository;

    protected function setUp(): void
    {
        $this->apiClient = $this->createMock(StrapiApiClient::class);
        $this->pageFactory = $this->createMock(StrapiPageFactory::class);
        $this->repository = new StrapiPageRepository($this->apiClient, $this->pageFactory);
    }

    public function testGetByPathReturnsPageWhenFound(): void
    {
        $path = '/about';
        $pageContentType = new PageContentType();
        $page = new Page('About', '/about', '<p>About content</p>');

        $this->apiClient->expects($this->once())
            ->method('getPage')
            ->with($path)
            ->willReturn($pageContentType);

        $this->pageFactory->expects($this->once())
            ->method('createFromModel')
            ->with($pageContentType)
            ->willReturn($page);

        $result = $this->repository->getByPath($path);

        $this->assertSame($page, $result);
    }

    public function testGetByPathReturnsNullWhenNotFound(): void
    {
        $path = '/non-existent';

        $this->apiClient->expects($this->once())
            ->method('getPage')
            ->with($path)
            ->willReturn(null);

        $this->pageFactory->expects($this->never())
            ->method('createFromModel');

        $result = $this->repository->getByPath($path);

        $this->assertNull($result);
    }
}

