Lines for testing:

--- adding books:

$inventory->addBook('writer', 'Eye-cathing');
$inventory->addBook('writer2', 'captivating');

--- Check out book:

$inventory->checkOutBook('writer', 'Eye-cathing', 'is mine now');

--- Remove book:

$inventory->removeBook('writer', 'Eye-cathing');
$inventory->removeBook('writer2', 'captivating');

--- Search books:

$inventory->findBooks('iter', 'author');
$inventory->findBooks('ier', 'author');
$inventory->findBooks('2', 'author');

$inventory->findBooks('eye', 'title');
$inventory->findBooks('c', 'title');
$inventory->findBooks('  vat', 'title');