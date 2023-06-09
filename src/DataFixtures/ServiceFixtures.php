<?php

namespace App\DataFixtures;

use App\Enum\ServiceTypeEnum;
use App\Entity\ServiceTranslation;
use App\Entity\Service;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ServiceFixtures extends Fixture

{
    public function load(ObjectManager $manager): void
    {
        $apartment = new Service();
        $apartment->setName('Magyar apartman név 1');
        $apartment->setSlug('magyar-apartman-nev-1');
        $apartment->setBody('Magyar apartman hosszú szöveges leírás 1');
        $apartment->setLead('Magyar apartman leírás 1');
        $apartment->setCoverImage('1.jpg');
        $apartment->setCoverImageCollection('1.jpg');
        $apartment->setReservationType('INDEPENDENT');
        $apartment->setBeds(4);
        $apartment->setExtraBeds(2);
        $apartment->setPrice(15000);
        $apartment->setAvaibleSameTime(1);
        $apartment->setMinDay(2);
        $apartment->setCompanyName('Apartman Kft');
        $apartment->setCompanyPriority(1);
        $apartment->setServiceType(ServiceTypeEnum::APARTMENT);
        $apartment->addTranslation(new ServiceTranslation('hu', 'name', 'Magyar apartman név 1'));
        $apartment->addTranslation(new ServiceTranslation('hu', 'body', 'Magyar apartman hosszú szöveges leírás 1'));
        $apartment->addTranslation(new ServiceTranslation('hu', 'lead', 'Magyar apartman leírás 1'));
        $apartment->addTranslation(new ServiceTranslation('hu', 'slug', 'magyar-apartman-nev-1'));
        $apartment->addTranslation(new ServiceTranslation('de', 'name', 'Deutscher Wohnungsname 1'));
        $apartment->addTranslation(new ServiceTranslation('de', 'body', 'Deutsche Wohnungsbeschreibung Langtext 1'));
        $apartment->addTranslation(new ServiceTranslation('de', 'lead', 'Deutsche Wohnungsbeschreibung 1'));
        $apartment->addTranslation(new ServiceTranslation('de', 'slug', 'deutscher-wohnungsname-1'));
        $apartment->addTranslation(new ServiceTranslation('en', 'name', 'English apartment name 1'));
        $apartment->addTranslation(new ServiceTranslation('en', 'body', 'English apartment long text description 1'));
        $apartment->addTranslation(new ServiceTranslation('en', 'lead', 'English apartment description 1'));
        $apartment->addTranslation(new ServiceTranslation('en', 'slug', 'english-apartment-name-1'));
        $manager->persist($apartment);

        $apartment = new Service();
        $apartment->setName('Magyar apartman név 2');
        $apartment->setSlug('magyar-apartman-nev-2');
        $apartment->setBody('Magyar apartman hosszú szöveges leírás 2');
        $apartment->setLead('Magyar apartman leírás 2');
        $apartment->setCoverImage('2.jpg');
        $apartment->setCoverImageCollection('2.jpg');
        $apartment->setReservationType('NOT_INDEPENDENT');
        $apartment->setBeds(5);
        $apartment->setExtraBeds(0);
        $apartment->setPrice(17000);
        $apartment->setAvaibleSameTime(1);
        $apartment->setMinDay(2);
        $apartment->setCompanyName('Apartman Kft');
        $apartment->setCompanyPriority(1);
        $apartment->setServiceType(ServiceTypeEnum::APARTMENT);
        $apartment->addTranslation(new ServiceTranslation('hu', 'name', 'Magyar apartman név 2'));
        $apartment->addTranslation(new ServiceTranslation('hu', 'body', 'Magyar apartman hosszú szöveges leírás 2'));
        $apartment->addTranslation(new ServiceTranslation('hu', 'lead', 'Magyar apartman leírás 2'));
        $apartment->addTranslation(new ServiceTranslation('hu', 'slug', 'magyar-apartman-nev-2'));
        $apartment->addTranslation(new ServiceTranslation('de', 'name', 'Deutscher Wohnungsname 2'));
        $apartment->addTranslation(new ServiceTranslation('de', 'body', 'Deutsche Wohnungsbeschreibung Langtext 2'));
        $apartment->addTranslation(new ServiceTranslation('de', 'lead', 'Deutsche Wohnungsbeschreibung 2'));
        $apartment->addTranslation(new ServiceTranslation('de', 'slug', 'deutscher-wohnungsname-2'));
        $apartment->addTranslation(new ServiceTranslation('en', 'name', 'English apartment name 2'));
        $apartment->addTranslation(new ServiceTranslation('en', 'body', 'English apartment long text description 2'));
        $apartment->addTranslation(new ServiceTranslation('en', 'lead', 'English apartment description 2'));
        $apartment->addTranslation(new ServiceTranslation('en', 'slug', 'english-apartment-name-2'));
        $manager->persist($apartment);

        $apartment = new Service();
        $apartment->setName('Magyar apartman név 3');
        $apartment->setSlug('magyar-apartman-nev-3');
        $apartment->setBody('Magyar apartman hosszú szöveges leírás 3');
        $apartment->setLead('Magyar apartman leírás 3');
        $apartment->setCoverImage('3.jpg');
        $apartment->setCoverImageCollection('3.jpg');
        $apartment->setReservationType('CANT_BOOK');
        $apartment->setBeds(1);
        $apartment->setExtraBeds(0);
        $apartment->setPrice(10000);
        $apartment->setAvaibleSameTime(1);
        $apartment->setMinDay(2);
        $apartment->setCompanyName('Apartman Kft');
        $apartment->setCompanyPriority(1);
        $apartment->setServiceType(ServiceTypeEnum::APARTMENT);
        $apartment->addTranslation(new ServiceTranslation('hu', 'name', 'Magyar apartman név 3'));
        $apartment->addTranslation(new ServiceTranslation('hu', 'body', 'Magyar apartman hosszú szöveges leírás 3'));
        $apartment->addTranslation(new ServiceTranslation('hu', 'lead', 'Magyar apartman leírás 3'));
        $apartment->addTranslation(new ServiceTranslation('hu', 'slug', 'magyar-apartman-nev-3'));
        $apartment->addTranslation(new ServiceTranslation('de', 'name', 'Deutscher Wohnungsname 3'));
        $apartment->addTranslation(new ServiceTranslation('de', 'body', 'Deutsche Wohnungsbeschreibung Langtext 3'));
        $apartment->addTranslation(new ServiceTranslation('de', 'lead', 'Deutsche Wohnungsbeschreibung 3'));
        $apartment->addTranslation(new ServiceTranslation('de', 'slug', 'deutscher-wohnungsname-3'));
        $apartment->addTranslation(new ServiceTranslation('en', 'name', 'English apartment name 3'));
        $apartment->addTranslation(new ServiceTranslation('en', 'body', 'English apartment long text description 3'));
        $apartment->addTranslation(new ServiceTranslation('en', 'lead', 'English apartment description 3'));
        $apartment->addTranslation(new ServiceTranslation('en', 'slug', 'english-apartment-name-3'));
        $manager->persist($apartment);

        $bicycle = new Service();
        $bicycle->setName('Magyar kerékpár név 1');
        $bicycle->setSlug('magyar-kerekpar-nev-1');
        $bicycle->setBody('Magyar kerékpár hosszú szöveges leírás 1');
        $bicycle->setLead('Magyar kerékpár leírás 1');
        $bicycle->setCoverImage('6.jpg');
        $bicycle->setCoverImageCollection('6.jpg');
        $bicycle->setReservationType('INDEPENDENT');
        $bicycle->setAvaibleSameTime(1);
        $bicycle->setMinDay(1);
        $bicycle->setHalfDayPrice(500);
        $bicycle->setFullDayPrice(1000);
        $bicycle->setCompanyName('Apartman Kft');
        $bicycle->setCompanyPriority(1);
        $bicycle->setServiceType(ServiceTypeEnum::EBIKE);
        $bicycle->addTranslation(new ServiceTranslation('hu', 'name', 'Magyar kerékpár név 1'));
        $bicycle->addTranslation(new ServiceTranslation('hu', 'body', 'Magyar kerékpár hosszú szöveges leírás 1'));
        $bicycle->addTranslation(new ServiceTranslation('hu', 'lead', 'Magyar kerékpár leírás 1'));
        $bicycle->addTranslation(new ServiceTranslation('hu', 'slug', 'magyar-kerekpar-nev-1'));
        $bicycle->addTranslation(new ServiceTranslation('de', 'name', 'Deutscher Fahrradname 1'));
        $bicycle->addTranslation(new ServiceTranslation('de', 'body', 'Deutsche Fahrradbeschreibung Langtext 1'));
        $bicycle->addTranslation(new ServiceTranslation('de', 'lead', 'Deutsche Fahrradbeschreibung 1'));
        $bicycle->addTranslation(new ServiceTranslation('de', 'slug', 'deutscher-fahrradname-1'));
        $bicycle->addTranslation(new ServiceTranslation('en', 'name', 'English bicycle name 1'));
        $bicycle->addTranslation(new ServiceTranslation('en', 'body', 'English bicycle long text description 1'));
        $bicycle->addTranslation(new ServiceTranslation('en', 'lead', 'English bicycle description 1'));
        $bicycle->addTranslation(new ServiceTranslation('en', 'slug', 'english-bicycle-name-1'));
        $manager->persist($bicycle);

        $bicycle = new Service();
        $bicycle->setName('Magyar kerékpár név 2');
        $bicycle->setSlug('magyar-kerekpar-nev-2');
        $bicycle->setBody('Magyar kerékpár hosszú szöveges leírás 2');
        $bicycle->setLead('Magyar kerékpár leírás 2');
        $bicycle->setCoverImage('7.jpg');
        $bicycle->setCoverImageCollection('7.jpg');
        $bicycle->setReservationType('INDEPENDENT');
        $bicycle->setAvaibleSameTime(1);
        $bicycle->setMinDay(1);
        $bicycle->setHalfDayPrice(500);
        $bicycle->setFullDayPrice(1000);
        $bicycle->setCompanyName('Apartman Kft');
        $bicycle->setCompanyPriority(1);
        $bicycle->setServiceType(ServiceTypeEnum::EBIKE);
        $bicycle->addTranslation(new ServiceTranslation('hu', 'name', 'Magyar kerékpár név 2'));
        $bicycle->addTranslation(new ServiceTranslation('hu', 'body', 'Magyar kerékpár hosszú szöveges leírás 2'));
        $bicycle->addTranslation(new ServiceTranslation('hu', 'lead', 'Magyar kerékpár leírás 2'));
        $bicycle->addTranslation(new ServiceTranslation('hu', 'slug', 'magyar-kerekpar-nev-2'));
        $bicycle->addTranslation(new ServiceTranslation('de', 'name', 'Deutscher Fahrradname 2'));
        $bicycle->addTranslation(new ServiceTranslation('de', 'body', 'Deutsche Fahrradbeschreibung Langtext 2'));
        $bicycle->addTranslation(new ServiceTranslation('de', 'lead', 'Deutsche Fahrradbeschreibung 2'));
        $bicycle->addTranslation(new ServiceTranslation('de', 'slug', 'deutscher-fahrradname-2'));
        $bicycle->addTranslation(new ServiceTranslation('en', 'name', 'English bicycle name 2'));
        $bicycle->addTranslation(new ServiceTranslation('en', 'body', 'English bicycle long text description 2'));
        $bicycle->addTranslation(new ServiceTranslation('en', 'lead', 'English bicycle description 2'));
        $bicycle->addTranslation(new ServiceTranslation('en', 'slug', 'english-bicycle-name-2'));
        $manager->persist($bicycle);

        $ship = new Service();
        $ship->setName('Magyar hajó név 1');
        $ship->setSlug('magyar-hajo-nev-1');
        $ship->setBody('Magyar hajó hosszú szöveges leírás 1');
        $ship->setLead('Magyar hajó leírás 1');
        $ship->setCoverImage('4.jpg');
        $ship->setCoverImageCollection('4.jpg');
        $ship->setReservationType('INDEPENDENT');
        $ship->setAvaibleSameTime(1);
        $ship->setMinDay(1);
        $ship->setPrice(20000);
        $ship->setAssurance(10000);
        $ship->setPrice(20000);
        $ship->setCleaningCharge(5000);
        $ship->setCaptainType('MANDATORY');
        $ship->setCaptainPrice(10000);
        $ship->setCompanyName('Hajó Kft');
        $ship->setCompanyPriority(2);
        $ship->setServiceType(ServiceTypeEnum::SHIP);
        $ship->addTranslation(new ServiceTranslation('hu', 'name', 'Magyar hajó név 1'));
        $ship->addTranslation(new ServiceTranslation('hu', 'body', 'Magyar hajó hosszú szöveges leírás 1'));
        $ship->addTranslation(new ServiceTranslation('hu', 'lead', 'Magyar hajó leírás 1'));
        $ship->addTranslation(new ServiceTranslation('hu', 'slug', 'magyar-hajo-nev-1'));
        $ship->addTranslation(new ServiceTranslation('de', 'name', 'Deutscher Schiffname 1'));
        $ship->addTranslation(new ServiceTranslation('de', 'body', 'Deutsche Schiffbeschreibung Langtext 1'));
        $ship->addTranslation(new ServiceTranslation('de', 'lead', 'Deutsche Schiffbeschreibung 1'));
        $ship->addTranslation(new ServiceTranslation('de', 'slug', 'deutscher-schiffname-1'));
        $ship->addTranslation(new ServiceTranslation('en', 'name', 'English ship name 1'));
        $ship->addTranslation(new ServiceTranslation('en', 'body', 'English ship long text description 1'));
        $ship->addTranslation(new ServiceTranslation('en', 'lead', 'English ship description 1'));
        $ship->addTranslation(new ServiceTranslation('en', 'slug', 'english-ship-name-1'));
        $manager->persist($ship);

        $ship = new Service();
        $ship->setName('Magyar hajó név 2');
        $ship->setSlug('magyar-hajo-nev-2');
        $ship->setBody('Magyar hajó hosszú szöveges leírás 2');
        $ship->setLead('Magyar hajó leírás 2');
        $ship->setCoverImage('5.jpg');
        $ship->setCoverImageCollection('5.jpg');
        $ship->setReservationType('INDEPENDENT');
        $ship->setAvaibleSameTime(1);
        $ship->setMinDay(1);
        $ship->setPrice(25000);
        $ship->setAssurance(10000);
        $ship->setPrice(25000);
        $ship->setCleaningCharge(5000);
        $ship->setCaptainType('OPTIONAL_WITH_LICENCE');
        $ship->setCaptainPrice(10000);
        $ship->setCompanyName('Hajó Kft');
        $ship->setCompanyPriority(2);
        $ship->setServiceType(ServiceTypeEnum::SHIP);
        $ship->addTranslation(new ServiceTranslation('hu', 'name', 'Magyar hajó név 2'));
        $ship->addTranslation(new ServiceTranslation('hu', 'body', 'Magyar hajó hosszú szöveges leírás 2'));
        $ship->addTranslation(new ServiceTranslation('hu', 'lead', 'Magyar hajó leírás 2'));
        $ship->addTranslation(new ServiceTranslation('hu', 'slug', 'magyar-hajo-nev-2'));
        $ship->addTranslation(new ServiceTranslation('de', 'name', 'Deutscher Schiffname 2'));
        $ship->addTranslation(new ServiceTranslation('de', 'body', 'Deutsche Schiffbeschreibung Langtext 2'));
        $ship->addTranslation(new ServiceTranslation('de', 'lead', 'Deutsche Schiffbeschreibung 2'));
        $ship->addTranslation(new ServiceTranslation('de', 'slug', 'deutscher-schiffname-2'));
        $ship->addTranslation(new ServiceTranslation('en', 'name', 'English ship name 2'));
        $ship->addTranslation(new ServiceTranslation('en', 'body', 'English ship long text description 2'));
        $ship->addTranslation(new ServiceTranslation('en', 'lead', 'English ship description 2'));
        $ship->addTranslation(new ServiceTranslation('en', 'slug', 'english-ship-name-2'));
        $manager->persist($ship);

        $manager->flush();

        $toRemoveElements = $manager->getRepository(ServiceTranslation::class)->findUnneccessaryTranslations();
        foreach ($toRemoveElements as $element) {
            $manager->remove($element);
            $manager->flush();
        }
    }
}
