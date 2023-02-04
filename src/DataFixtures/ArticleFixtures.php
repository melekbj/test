<?php

namespace App\DataFixtures;

use DateTime;
use Faker\Factory;
use App\Entity\Comment;
use App\Entity\Articles;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('en_EN');
       for ($i=1; $i <5 ; $i++) { 
        $category = new Category();
        $category->setTitle("road $i");
        $category->setDescription("roadDesc $i");

        $manager->persist($category);

        for ($j=1; $j <=2 ; $j++) { 
            $article = new Articles();
            $article->setTitle("name $j")
                    ->setContent("At its most basic, blogs can help you develop an online presence, prove yourself an expert in an industry, 
                    and attract more quality leads to all pages of your site.If you're contemplating creating a blog for your business, 
                    or simply want to know what one is, keep reading.")
                    ->setCreatedAT($faker->dateTimeBetween('-6 months'))
                    ->setImage("https://picsum.photos/seed/picsum/200/300")
                    ->setCategory($category);
            
            $manager->persist($article);

            $today = new DateTime();
            $difference = $today->diff($article->getCreatedAT());
            $days = $difference->days;
            $comment_maximumu = '-'. $days. 'days';

            for ($k=0; $k <= mt_rand(4,6) ; $k++) { 
                $comment= new Comment();
                $comment->setAuthor($faker->name)
                        ->setContent("nice blog yeeess !!")
                        ->setCreatedAt($faker->dateTimeBetween($comment_maximumu))
                        ->setArticle($article);
                
                $manager->persist($comment);
            }


        }

       }

        $manager->flush();
    }
}
