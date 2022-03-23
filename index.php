<?php

declare(strict_types=1);

class Library
{

    public array $books;

    public function __construct()
    {
        $storageFile = './storage.json';
        if (file_exists($storageFile)) {
            $this->books = json_decode(file_get_contents($storageFile), true);
        } else {
            $this->books = [];
            $this->updateStorage();
        }
    }

    public function addBook(string $author, string $title): void
    {
        $this->books[] = [
            'author' => $author,
            'title' => $title,
            'isCheckedOut' => false,
            'checkedOutTo' => ''
        ];

        $this->updateStorage();
    }

    public function removeBook(string $author, string $title): void
    {
        $remove = function (int $key) {
            array_splice($this->books, $key, 1);
        };
        $this->changeBookStatus($author, $title, $remove);
    }

    public function checkOutBook(string $author, string $title, string $user): void
    {
        $checkOut = function (int $key, array $book, string $user): void {
            $book['isCheckedOut'] = true;
            $book['checkedOutTo'] = $user;
            $this->books[$key] = $book;
        };
        $this->changeBookStatus($author, $title, $checkOut, $user);
    }

    private function changeBookStatus(string $author, string $title, callable $callback, $user = NULL)
    {
        $bookFound = false;
        foreach ($this->books as $key => $book) {
            if ($book['author'] === $author && $book['title'] === $title) {
                $bookFound = true;
                if ($book['isCheckedOut']) {
                    echo "Book is already checked out!";
                } else {
                    $callback($key, $book = $book, $user);
                }
            }
        }
        $this->updateStorage();
        if (!$bookFound) {
            echo 'Book not found!';
        }
    }

    private function updateStorage(): void
    {
        $storageFile = './storage.json';
        file_put_contents($storageFile, json_encode($this->books, JSON_PRETTY_PRINT));
    }

    public function findBooks(string $term, string $method): void
    {
        $regex = '/' . trim($term) . '/i';
        $bookFound = false;

        foreach ($this->books as $key => $book) {
            if (preg_match($regex, $book[$method]) === 1) {
                $bookFound = true;
                $this->printResult($book);
            }
        }

        if (!$bookFound) {
            echo 'No books found!';
        }
    }

    private function printResult(array $book)
    {
        echo PHP_EOL . '---------' . PHP_EOL;
        echo 'Author: ' . $book['author'] . PHP_EOL;
        echo 'Title: ' . $book['title'] . PHP_EOL;
        echo 'Checked out?: ' . ($book['isCheckedOut'] ? 'Yes' : 'No') . PHP_EOL;
        if ($book['isCheckedOut']) {
            echo 'Checked out to: ' . $book['checkedOutTo'] . PHP_EOL;
        }
    }
}



$inventory = new Library();

//Lines for testing:

//--- adding books:

// $inventory->addBook('writer', 'Eye-cathing');
// $inventory->addBook('writer2', 'captivating');

//--- Check out book:

// $inventory->checkOutBook('writer', 'Eye-cathing', 'is mine now');

//--- Remove book:

// $inventory->removeBook('writer', 'Eye-cathing');
// $inventory->removeBook('writer2', 'captivating');

//--- Search books:

// $inventory->findBooks('iter', 'author');
// $inventory->findBooks('ier', 'author');
// $inventory->findBooks('2', 'author');

// $inventory->findBooks('eye', 'title');
// $inventory->findBooks('c', 'title');
// $inventory->findBooks('  vat', 'title');