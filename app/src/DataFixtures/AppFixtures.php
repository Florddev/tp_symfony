<?php

namespace App\DataFixtures;

use App\Entity\Activity;
use App\Entity\Comment;
use App\Entity\Event;
use App\Entity\Place;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Factory;

class AppFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $users = [];
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setEmail($faker->email());
            $user->setPassword(
                $this->passwordHasher->hashPassword($user, 'password123')
            );
            $user->setVerified(true);
            if ($i === 0) {
                $user->setRoles(['ROLE_ADMIN']);
            }
            $users[] = $user;
            $manager->persist($user);
        }
        $manager->flush();

        $places = [];
        for ($i = 0; $i < 5; $i++) {
            $place = new Place();
            $place->setName($faker->company());
            $place->setAdresse($faker->streetAddress());
            $place->setCity($faker->city());
            $place->setCodePostal($faker->postcode());
            $place->setCapacity($faker->numberBetween(50, 1000));
            $place->setCreatedAt(new \DateTimeImmutable());
            $place->setUpdatedAt(new \DateTimeImmutable());
            $manager->persist($place);
            $places[] = $place;
        }
        $manager->flush();

        $events = [];
        for ($i = 0; $i < 20; $i++) {
            $event = new Event();
            $place = $faker->randomElement($places);

            $event->setTitle($faker->sentence(3));
            $event->setDescription($faker->paragraph());

            $startDate = $faker->dateTimeBetween('now', '+6 months');
            $endDate = clone $startDate;
            $endDate->modify('+' . $faker->numberBetween(1, 5) . ' days');

            $event->setDateDebut($startDate);
            $event->setDateFin($endDate);
            $event->setPlace($place);
            $event->setLieuId($place->getId());
            $event->setCreatedAt(new \DateTimeImmutable());
            $event->setUpdatedAt(new \DateTimeImmutable());
            $manager->persist($event);
            $events[] = $event;
        }
        $manager->flush();

        foreach ($events as $event) {
            $numActivities = $faker->numberBetween(1, 3);
            for ($i = 0; $i < $numActivities; $i++) {
                $activity = new Activity();
                $activity->setNom($faker->words(3, true));
                $activity->setDescription($faker->paragraph());

                $startTime = clone $event->getDateDebut();
                $startTime->modify('+' . $faker->numberBetween(0, 48) . ' hours');
                $endTime = clone $startTime;
                $endTime->modify('+' . $faker->numberBetween(1, 4) . ' hours');

                $activity->setHeureDebut($startTime);
                $activity->setHeureFin($endTime);
                $activity->setEvent($event);
                $activity->setEvenementId($event->getId());
                $activity->setCreatedAt(new \DateTimeImmutable());
                $activity->setUpdatedAt(new \DateTimeImmutable());
                $manager->persist($activity);
            }
        }
        $manager->flush();

        for ($i = 0; $i < 100; $i++) {
            $user = $faker->randomElement($users);
            $event = $faker->randomElement($events);

            $comment = new Comment();
            $comment->setContent($faker->paragraph());
            $comment->setUser($user);
            $comment->setEvent($event);
            $comment->setUserId($user->getId());
            $comment->setEventId($event->getId());
            $comment->setCreatedAt(new \DateTimeImmutable());
            $comment->setUpdatedAt(new \DateTimeImmutable());
            $manager->persist($comment);
        }
        $manager->flush();
    }
}