<?php

declare(strict_types=1);

class Book {
    public string $author;
    public string $title;
    public bool $isCheckedOut;
    public string $chekedOutTo;

    public function __construct(string $author, string $title)
    {
        $this->author = $author;
        $this->title = $title;
        $this->isCheckedOut = false;
        $this->checkedOutTo = '';
    }
}

class Storage {
    private static $storageFile = './storage.json';

    public static function getFromStorage(): array
    {
        if (file_exists(self::$storageFile)) {
            $contents = json_decode(file_get_contents(self::$storageFile), true);
            $memory = [];
            foreach($contents as $object) {
                $memory[] = unserialize($object);
            }

        } else {
            $memory = [];
            self::updateStorage($memory);
        }
        return $memory;
    }

    public static function updateStorage(array $data): void
    {
        $memory = [];
        foreach($data as $object) {
            $memory[] = serialize($object);
        }
        file_put_contents(self::$storageFile, json_encode($memory, JSON_PRETTY_PRINT));
    }
}

class Library
{
    public function addBook(string $author, string $title): void
    {
        $books = Storage::getFromStorage();
        $books[] = new Book($author, $title);
        Storage::updateStorage($books);
    }

    public function removeBook(string $author, string $title): void
    {

        $remove = function (int $key, array $books): array
        {
            array_splice($books, $key, 1);
            return $books;
        };
        $this->changeBookStatus($author, $title, $remove);
    }

    public function checkOutBook(string $author, string $title, string $user): void
    {
        $checkOut = function (int $key, array $books, Book $book, string $user): array
        {
            $book->isCheckedOut = true;
            $book->checkedOutTo = $user;
            $books[$key] = $book;

            return $books;
        };
        $this->changeBookStatus($author, $title, $checkOut, $user);
    }

    private function changeBookStatus(string $author, string $title, callable $callback, $user = NULL)
    {
        $books = Storage::getFromStorage();
        $bookFound = false;
        foreach ($books as $key => $book) {
            if ($book->author === $author && $book->title === $title) {
                $bookFound = true;
                if ($book->isCheckedOut) {
                    LibraryReporter::printError('409');
                } else {
                    Storage::updateStorage($callback($key, $books, $book, $user));
                }
            }
        }
        if (!$bookFound) {
            LibraryReporter::printError('404');
        }
    }

    public function findBooks(string $term, string $method): void
    {
        $books = Storage::getFromStorage();
        $regex = '/' . trim($term) . '/i';
        $bookFound = false;

        foreach ($books as $book) {
            $searchMethod = $method === 'author' ? $book->author : $book->title;
            if (preg_match($regex, $searchMethod) === 1) {
                $bookFound = true;
                LibraryReporter::printSearchResult($book);
            }
        }
        if (!$bookFound) {
            LibraryReporter::printError('404');
        }
    }
}


class LibraryReporter {

    public static function printSearchResult(Book $book)
    {
        echo PHP_EOL . '---------' . PHP_EOL;
        echo 'Author: ' . $book->author . PHP_EOL;
        echo 'Title: ' . $book->title . PHP_EOL;
        echo 'Checked out?: ' . ($book->isCheckedOut ? 'Yes' : 'No') . PHP_EOL;
        if ($book->isCheckedOut) {
            echo 'Checked out to: ' . $book->checkedOutTo . PHP_EOL;
        }
    }

    public static function printError(string $errorCode)
    {
        switch ($errorCode) {
            case '404':
                echo 'No book found!';
                break;
            case '409':
                echo 'Book is checked out!';
                break;
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