<?php

namespace App\Domain\Book\Entities;

use App\Domain\Book\Exceptions\InsufficientStockException;
use App\Domain\Book\Exceptions\InvalidStockAmountException;

class Book
{
    public function __construct(
        private ?int $id,
        private string $title,
        private string $author,
        private string $isbn,
        private ?string $description,
        private array $genres,
        private int $stock = 0,
    ) {
        if ($this->stock < 0) {
            throw new \DomainException('Stock cannot be negative value.');
        }
    }

    public static function create(string $title,string $author, string $isbn, ?string $description, array $genres,int $stock = 0): self
    {
        return new self(
            id: null,
            title: $title,
            author: $author,
            isbn: $isbn,
            description: $description,
            genres:$genres,
            stock: $stock,
        );
    }

    public function getId(): ?int   { return $this->id; }
    public function setId(int $id): void { $this->id = $id; }
    public function setTitle(string $title): void { $this->title = $title; }
    public function setAuthor(string $author): void { $this->author = $author; }
    public function setIsbn(string $isbn): void { $this->isbn = $isbn; }
    public function setDescription(int $description): void { $this->description = $description; }
    public function setStock(int $stock): void { $this->stock = $stock; }
    public function setGenre(int $genres): void { $this->id = $genres; }

    public function increaseStock(int $amount): void
    {
        if ($amount < 0) {
            throw new InvalidStockAmountException('Increase amount must be positive.');
        }

        $this->stock += $amount;
    }

    public function decreaseStock(int $amount): void
    {
        if ($amount < 0) {
            throw new InvalidStockAmountException();
        }

        if ($this->stock - $amount < 0) {
            throw new InsufficientStockException('Not enough stock.');
        }

        $this->stock -= $amount;
    }

    public function isAvailable(): bool
    {
        return $this->stock > 0;
    }

    public function getGenres(): array
    {
        return $this->genres;
    }

    public function getDescription(): string|null
    {
        return $this->description;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getIsbn(): string
    {
        return $this->isbn;
    }
    public function getAuthor(): string
    {
        return $this->author;
    }
    public function getStock(): int
    {
        return $this->stock;
    }
}
