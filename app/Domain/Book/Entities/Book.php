<?php

namespace App\Domain\Book\Entities;

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

    public function increaseStock(int $amount): void
    {
        if ($amount < 0) {
            throw new \DomainException('Increase amount must be positive.');
        }

        $this->stock += $amount;
    }

    public function decreaseStock(int $amount): void
    {
        if ($amount < 0) {
            throw new \DomainException('Decrease amount must be positive.');
        }

        if ($this->stock - $amount < 0) {
            throw new \DomainException('Not enough stock.');
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
        return $this->isbn;
    }
    public function getStock(): int
    {
        return $this->stock;
    }
}
