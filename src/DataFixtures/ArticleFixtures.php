<?php

namespace App\DataFixtures;

use App\Entity\Articles;
use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;


class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
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
                    ->setCreatedAT(new \DateTime())
                    ->setImage("https://picsum.photos/seed/picsum/200/300")
                    ->setCategory($category);
            
            $manager->persist($article);
        }

       }

        $manager->flush();
    }
}
