<?php

namespace App\DataFixtures;

use App\Domain\Book\Entity\Book;
use App\Domain\Book\ValueObject\BookId;
use App\Domain\Book\ValueObject\ISBN;
use App\Domain\Book\ValueObject\Title;
use App\Domain\User\Entity\User;
use App\Domain\User\ValueObject\Email;
use App\Domain\User\ValueObject\UserId;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{

    public function load(ObjectManager $manager): void
    {
       $admin = new User(
            id: UserId::generate(),
            email: new Email('admin@bibliotheque.fr'),
            firstName: 'Admin',
            lastName: 'Système',
            passwordHash: password_hash('admin123', PASSWORD_BCRYPT),
            role: User::ROLE_ADMIN
        );

        $manager->persist($admin);

        $user = new User(
            id: UserId::generate(),
            email: new Email('user@bibliotheque.fr'),
            firstName: 'Jean',
            lastName: 'Dupont',
            passwordHash: password_hash('user123', PASSWORD_BCRYPT),
            role: User::ROLE_USER
        );

        $manager->persist($user);


         $livres = [
           ['Clean Code',                        'Robert C. Martin',  '9780132350884', 3, 'Le guide de référence pour écrire du code propre et maintenable.',    'https://covers.openlibrary.org/b/isbn/9780321127426-L.jpg'],
            ['Domain-Driven Design',              'Eric Evans',        '9780321125217', 2, 'La référence du DDD : Bounded Context, Aggregates, Value Objects.','https://covers.openlibrary.org/b/isbn/9780321127426-L.jpg'],
            ['The Pragmatic Programmer',          'David Thomas',      '9780135957059', 4, 'Conseils pratiques pour devenir un meilleur développeur.', 'https://covers.openlibrary.org/b/isbn/9780321127426-L.jpg'],
            ['Refactoring',                       'Martin Fowler',     '9780201485677', 2, 'Comment améliorer le code existant étape par étape.', 'https://covers.openlibrary.org/b/isbn/9780321127426-L.jpg'],
            ['Patterns of Enterprise Application','Martin Fowler',     '9780321127426', 1, 'Les grands patterns d\'architecture des applications d\'entreprise.','https://covers.openlibrary.org/b/isbn/9780321127426-L.jpg'],
        ];

        foreach ($livres as [$titre, $auteur, $isbn, $copies, $description, $cover]) {
            $book = new Book(
               id: BookId::generate(),
                title: new Title($titre),
                author: $auteur,
                isbn: new ISBN($isbn),
                description: $description,
                totalCopies: $copies,
                coverUrl: $cover
            );
            $manager->persist($book);
        }


        $manager->flush();
    }
}
