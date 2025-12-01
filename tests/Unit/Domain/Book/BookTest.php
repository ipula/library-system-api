<?php

namespace Tests\Unit\Domain\Book;

use App\Domain\Book\Entities\Book;
use App\Domain\Book\Exceptions\InsufficientStockException;
use App\Domain\Book\Exceptions\InvalidStockAmountException;
use App\Domain\Shared\Exceptions\DomainException;
use Tests\TestCase;

class BookTest extends TestCase
{
    public function testBookIsAvailableWhenStockGreaterThanZero(): void
    {
        $book = Book::create(
            title: 'Test Book',
            author: 'Test Author',
            isbn: '1234567890',
            description: 'Test Description',
            genres: ['Test Genre01', 'Test Genre02', 'Test Genre03'],
            stock: 5,
        );

        $this->assertTrue($book->isAvailable());
    }

    public function testBookIsNotAvailableWhenStockIsZero(): void
    {
        $book = Book::create(
            title: 'Test Book',
            author: 'Test Author',
            isbn: '1234567890',
            description: 'Test Description',
            genres: ['Test Genre01', 'Test Genre02', 'Test Genre03'],
            stock: 0,
        );

        $this->assertFalse($book->isAvailable());
    }

    public function testDecreaseStockReducesStock()
    {
        $book = Book::create(
            title: 'Test Book',
            author: 'Test Author',
            isbn: '1234567890',
            description: 'Test Description',
            genres: ['Test Genre01', 'Test Genre02', 'Test Genre03'],
            stock: 3,
        );

        $book->decreaseStock(1);

        $this->assertSame(2, $book->getStock());
    }

    public function testDecreaseStockThrowsWhenNotEnoughStock()
    {
        $this->expectException(InsufficientStockException::class);
        $this->expectExceptionMessage('Not enough stock.');

        $book = Book::create(
            title: 'Test Book',
            author: 'Test Author',
            isbn: '1234567890',
            description: 'Test Description',
            genres: ['Test Genre01', 'Test Genre02', 'Test Genre03'],
            stock: 0,
        );

        $book->decreaseStock(1);
    }

    public function testDecreaseStockThrowsWhenInvalidStockAmount()
    {
        $this->expectException(InvalidStockAmountException::class);
        $this->expectExceptionMessage('Decrease amount must be positive');

        $book = Book::create(
            title: 'Test Book',
            author: 'Test Author',
            isbn: '1234567890',
            description: 'Test Description',
            genres: ['Test Genre01', 'Test Genre02', 'Test Genre03'],
            stock: 0,
        );

        $book->decreaseStock(-1);
    }

}
