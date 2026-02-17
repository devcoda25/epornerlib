<?php

declare(strict_types=1);

namespace EpornerLib\Tests\Unit;

use EpornerLib\Constants\API;
use EpornerLib\Exceptions\ValidationException;
use EpornerLib\Parameters\SearchParams;
use PHPUnit\Framework\TestCase;

class SearchParamsTest extends TestCase
{
    public function testDefaultValues(): void
    {
        $params = new SearchParams();

        $this->assertEquals(API::DEFAULT_QUERY, $params->query);
        $this->assertEquals(API::DEFAULT_PER_PAGE, $params->perPage);
        $this->assertEquals(API::DEFAULT_PAGE, $params->page);
        $this->assertEquals(API::DEFAULT_THUMBSIZE, $params->thumbsize);
        $this->assertEquals(API::DEFAULT_ORDER, $params->order);
        $this->assertEquals(API::DEFAULT_GAY, $params->gay);
        $this->assertEquals(API::DEFAULT_LQ, $params->lq);
        $this->assertEquals(API::DEFAULT_FORMAT, $params->format);
    }

    public function testCustomValues(): void
    {
        $params = new SearchParams(
            query: 'teen',
            perPage: 100,
            page: 2,
            thumbsize: 'big',
            order: 'most-popular',
            gay: 1,
            lq: 0,
            format: 'json'
        );

        $this->assertEquals('teen', $params->query);
        $this->assertEquals(100, $params->perPage);
        $this->assertEquals(2, $params->page);
        $this->assertEquals('big', $params->thumbsize);
        $this->assertEquals('most-popular', $params->order);
        $this->assertEquals(1, $params->gay);
        $this->assertEquals(0, $params->lq);
        $this->assertEquals('json', $params->format);
    }

    public function testToArray(): void
    {
        $params = new SearchParams(
            query: 'anal',
            perPage: 50,
            page: 1,
            thumbsize: 'medium',
            order: 'latest'
        );

        $result = $params->toArray();

        $this->assertEquals('anal', $result['query']);
        $this->assertEquals(50, $result['per_page']);
        $this->assertEquals(1, $result['page']);
        $this->assertEquals('medium', $result['thumbsize']);
        $this->assertEquals('latest', $result['order']);
    }

    public function testValidateWithValidParams(): void
    {
        $params = new SearchParams();

        // Should not throw
        $params->validate();

        $this->assertTrue(true);
    }

    public function testValidateWithInvalidThumbSize(): void
    {
        $params = new SearchParams();
        $params->thumbsize = 'invalid';

        $this->expectException(ValidationException::class);

        $params->validate();
    }

    public function testValidateWithInvalidOrder(): void
    {
        $params = new SearchParams();
        $params->order = 'invalid';

        $this->expectException(ValidationException::class);

        $params->validate();
    }

    public function testValidateWithInvalidPerPage(): void
    {
        $params = new SearchParams();
        $params->perPage = 0;

        $this->expectException(ValidationException::class);

        $params->validate();
    }

    public function testValidateWithInvalidPage(): void
    {
        $params = new SearchParams();
        $params->page = 0;

        $this->expectException(ValidationException::class);

        $params->validate();
    }

    public function testSetQuery(): void
    {
        $params = new SearchParams();
        $result = $params->setQuery('test');

        $this->assertEquals('test', $params->query);
        $this->assertInstanceOf(SearchParams::class, $result);
    }

    public function testNextPage(): void
    {
        $params = new SearchParams(page: 1);
        $nextParams = $params->nextPage();

        $this->assertEquals(2, $nextParams->page);
    }

    public function testWithPage(): void
    {
        $params = new SearchParams(page: 1);
        $newParams = $params->withPage(5);

        $this->assertEquals(5, $newParams->page);
        // Original should be unchanged
        $this->assertEquals(1, $params->page);
    }

    public function testFromArray(): void
    {
        $data = [
            'query' => 'test',
            'per_page' => 50,
            'page' => 2,
            'thumbsize' => 'big',
            'order' => 'most-popular',
            'gay' => 1,
            'lq' => 0,
            'format' => 'xml',
        ];

        $params = SearchParams::fromArray($data);

        $this->assertEquals('test', $params->query);
        $this->assertEquals(50, $params->perPage);
        $this->assertEquals(2, $params->page);
        $this->assertEquals('big', $params->thumbsize);
        $this->assertEquals('most-popular', $params->order);
        $this->assertEquals(1, $params->gay);
        $this->assertEquals(0, $params->lq);
        $this->assertEquals('xml', $params->format);
    }
}
