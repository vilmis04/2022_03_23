<?php

declare(strict_types=1);

class Storage {
    private static $storageFile = './storage.json';

    public static function getFromStorage(): array {

        if (file_exists(self::$storageFile)) {
            $memory = json_decode(file_get_contents(self::$storageFile), true);
        } else {
            $memory = [];
            self::updateStorage($memory);
        }
        return $memory;
    }
    public static function updateStorage(array $data): void
    {
        file_put_contents(self::$storageFile, json_encode($data, JSON_PRETTY_PRINT));
    }
}

class Library
{
    public function addBook(string $author, string $title): void
    {
        $books = Storage::getFromStorage();
        $books[] = [
            'author' => $author,
            'title' => $title,
            'isCheckedOut' => false,
            'checkedOutTo' => ''
        ];
        Storage::updateStorage($books);
    }

    public function removeBook(string $author, string $title): void
    {

        $books = Storage::getFromStorage();
        $remove = function (int $key, array $books): array {
            array_splice($books, $key, 1);
            return $books;
        };
        $this->changeBookStatus($author, $title, $books, $remove);
    }

    public function checkOutBook(string $author, string $title, string $user): void
    {
        $books = Storage::getFromStorage();
        $checkOut = function (int $key, array $books, array $book, string $user): array {
            $book['isCheckedOut'] = true;
            $book['checkedOutTo'] = $user;
            $books[$key] = $book;
            return $books;
        };
        $this->changeBookStatus($author, $title, $books, $checkOut, $user);
    }

    private function changeBookStatus(string $author, string $title, array $books, callable $callback, $user = NULL)
    {
        $bookFound = false;
        foreach ($books as $key => $book) {
            if ($book['author'] === $author && $book['title'] === $title) {
                $bookFound = true;
                if ($book['isCheckedOut']) {
                    echo "Book is checked out!";
                } else {
                    Storage::updateStorage($callback($key, $books, $book, $user));
                }
            }
        }
        if (!$bookFound) {
            echo 'Book not found!';
        }
    }

    public function findBooks(string $term, string $method): void
    {
        $books = Storage::getFromStorage();
        $regex = '/' . trim($term) . '/i';
        $bookFound = false;

        foreach ($books as $key => $book) {
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