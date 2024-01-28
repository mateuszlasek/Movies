<?php

namespace App\DataFixtures;

use App\Entity\Movie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class MovieFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $movie = new Movie();
        $movie->setTitle('The Dark Knight');
        $movie->setReleaseYear(2008);
        $movie->setDescription('This is the description of the Dark Knight');
        $movie->setImagePath("https://cdn.pixabay.com/photo/2018/08/23/17/01/batman-3626211_1280.jpg");

        //Add data to pivot table
        $movie->addActor($this->getReference('actor_1'));
        $movie->addActor($this->getReference('actor_2'));

        $manager->persist($movie);

        $movie2 = new Movie();
        $movie2->setTitle('Avengers: Endgame');
        $movie2->setReleaseYear(2019);
        $movie2->setDescription('This is the description of the Avengers: Endgame');
        $movie2->setImagePath("https://cdn.pixabay.com/photo/2015/03/14/16/02/lego-673337_1280.jpg");

        //Add data to pivot table
        $movie2->addActor($this->getReference('actor_3'));
        $movie2->addActor($this->getReference('actor_4'));

        $manager->persist($movie2);
        $manager->flush();

    
    }
}
