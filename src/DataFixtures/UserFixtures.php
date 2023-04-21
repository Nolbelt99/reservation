<?php

namespace App\DataFixtures;

use App\Entity\User;
use DateTime;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class UserFixtures extends Fixture

{
    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail('admin@admin.hu');
        $user->setCreatedAt(new DateTime());
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword('$2y$13$UZ5doQes/A.otMO9XbuoFOh6VrfszpnFpRHTxabfvGTrxwWQajMQy'); //123456
        $user->setNewsletter(false);
        $user->setFirstName('Admin');
        $user->setLastName('Admin');
        $user->setPhone('123456');
        $user->setInvoiceAddressName('Admin Admin');
        $user->setInvoiceAddressZip('7300');
        $user->setInvoiceAddressCountry('hu');
        $user->setInvoiceAddressCity('Komló');
        $user->setInvoiceAddressStreetAndOther('Iskola u. 15');
        $user->setBirthDay(new DateTime('1989-01-01'));
        $user->setPasswordAvaibleUntil(new DateTime('2024-01-01'));
        $manager->persist($user);

        $user = new User();
        $user->setEmail('user@user.hu');
        $user->setCreatedAt(new DateTime());
        $user->setRoles([]);
        $user->setPassword('$2y$13$UZ5doQes/A.otMO9XbuoFOh6VrfszpnFpRHTxabfvGTrxwWQajMQy'); //123456
        $user->setNewsletter(false);
        $user->setFirstName('User');
        $user->setLastName('Admin');
        $user->setPhone('');
        $user->setInvoiceAddressName('');
        $user->setInvoiceAddressZip('');
        $user->setInvoiceAddressCountry('');
        $user->setInvoiceAddressCity('');
        $user->setInvoiceAddressStreetAndOther('');
        $user->setBirthDay(new DateTime());
        $user->setPasswordAvaibleUntil(new DateTime('2024-01-01'));
        $manager->persist($user);
        $manager->flush();
    }
}
