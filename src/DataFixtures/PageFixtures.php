<?php

namespace App\DataFixtures;

use App\Entity\Page;
use App\Entity\PageTranslation;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class PageFixtures extends Fixture

{
    public function load(ObjectManager $manager): void
    {
        $page = new Page();
        $page->setTitle('Magyar aszf title');
        $page->setSlug('magyar-aszf');
        $page->setBody('Magyar aszf body');
        $page->addTranslation(new PageTranslation('hu', 'title', 'Magyar aszf title'));
        $page->addTranslation(new PageTranslation('hu', 'body', 'Magyar aszf body'));
        $page->addTranslation(new PageTranslation('hu', 'slug', 'magyar-aszf'));
        $page->addTranslation(new PageTranslation('de', 'title', 'Deutscher aszf title'));
        $page->addTranslation(new PageTranslation('de', 'body', 'Deutsche aszf body'));
        $page->addTranslation(new PageTranslation('de', 'slug', 'deutscher-aszf'));
        $page->addTranslation(new PageTranslation('en', 'title', 'English aszf title'));
        $page->addTranslation(new PageTranslation('en', 'body', 'English aszf body'));
        $page->addTranslation(new PageTranslation('en', 'slug', 'english-aszf'));
        $manager->persist($page);

        $manager->flush();

        $toRemoveElements = $manager->getRepository(PageTranslation::class)->findUnneccessaryTranslations();
        foreach ($toRemoveElements as $element) {
            $manager->remove($element);
            $manager->flush();
        }
    }
}
