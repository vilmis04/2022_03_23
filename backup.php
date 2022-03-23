<?php
declare(strict_types = 1);

class Library {

    public array $books;
    // public array $books = [];

    public function __construct() {
        $storageFile = './storage.json';
        if (file_exists($storageFile)) {
            $this->books = json_decode(file_get_contents($storageFile), true);
        } else {
            $this->books = [];
            $this->updateStorage();
        }
    }

    public function addBook (string $author, string $title): void {
        $this->books[] = [
            'author' => $author,
            'title' => $title,
            'isCheckedOut' => false,
            'checkedOutTo' => ''
        ];

        $this->updateStorage();
    }

    public function removeBook (string $author, string $title): void {
        $bookFound = false;
       
        foreach($this->books as $book) {
            if ($book->author === $author && $book->title === $title) {
                $bookFound = true;
                array_splice($this->books,array_search($book,$this->books));
                $this->updateStorage();
            }
        }
        if (!$bookFound) {
            echo 'Book not found!';
        }
    }

    public function checkOutBook(string $author, string $title, string $user): void {

        // $checkOut = function (Book $book) use($user): void {
        //     if ($book->isCheckedOut) {
        //         echo "Book is already checked out!";
        //         return;
        //     }
        //     $book->checkOut($user);
        // };
        // $checkOut = function (array $book) use($user): void {
        //     if ($book['isCheckedOut']) {
        //         echo "Book is already checked out!";
        //         return;
        //     }
        //     $book['isCheckedOut'] = true;
        //     $book['checkedOutTo'] = $user;
        // };

        // $this->filterBooks($author, $title, $checkOut);
        $bookFound = false;
        foreach($this->books as $book) {
            if ($book['author'] === $author && $book['title'] === $title) {
                $bookFound = true;
                if ($book['isCheckedOut']) {
                    echo "Book is already checked out!";
                    return;
                }
                $book['isCheckedOut'] = true;
                $book['checkedOutTo'] = $user;
                $this->updateStorage();
            }
        }

        if (!$bookFound) {
            echo 'Book not found!';
        }
        
    }

    // private function filterBooks(string $author, string $title, $callback=NULL): void {
        // $bookFound = false;
       
        // foreach($this->books as $book) {
        //     if ($book->author === $author && $book->title === $title) {
        //         $bookFound = true;
        //         if ($callback !== NULL) {
        //             $callback($book);
        //         }
        //     }
        // }
    
        // foreach($this->books as $book) {
        //     if ($book['author'] === $author && $book['title'] === $title) {
        //         $bookFound = true;
        //         if ($callback !== NULL) {
        //             $callback($book);
        //         }
        //     }
        // }

        // if (!$bookFound) {
        //     echo 'Book not found!';
        // }
    // }

    private function updateStorage (): void {
        $storageFile = './storage.json';
        // file_put_contents($storageFile, json_encode($this->books, JSON_FORCE_OBJECT));
        file_put_contents($storageFile, json_encode($this->books, JSON_PRETTY_PRINT));
    }


}

// class Book {
//     public string $author;
//     public string $title;
//     public bool $isCheckedOut;
//     public string $chekedOutTo;

//     public function __construct(string $author, string $title)
//     {
//         $this->author = $author;
//         $this->title = $title;
//         $this->isCheckedOut = false;
//         $this->checkedOutTo = '';
//     }

    // public function checkOut (string $user): void {
    //     $this->isCheckedOut = true;
    //     $this->checkedOutTo = $user;
    // }

// }

$inventory = new Library();
// print_r($inventory->books);

// $inventory->addBook('writer', 'Eye-cathing');
$inventory->addBook('writer2', 'captivating');

// print_r($inventory->books);

// $inventory->checkOutBook('writer', 'Eye-cathing', 'is mine now');
// print_r($inventory->books);

// $inventory->removeBook('writer', 'Eye-cathing');
// print_r($inventory->books);