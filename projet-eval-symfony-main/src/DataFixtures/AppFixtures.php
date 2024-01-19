<?php

namespace App\DataFixtures;

use App\Entity\Membre;
use App\Entity\Vehicule;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;
    private ParameterBagInterface $parameterBag;

    public function __construct(UserPasswordHasherInterface $passwordHasher,
                                ParameterBagInterface       $parameterBag)
    {
        $this->passwordHasher = $passwordHasher;
        $this->parameterBag = $parameterBag;
    }

    public function load(ObjectManager $manager): void
    {
        //load 1 admin membre.
        $this->loadAdminToDatabase($manager);

        //load 5 regular membres.
        $this->loadRegularMembreToDatabase($manager);

        //load 6 vehicules.
        $this->loadVehiculesToDatabase($manager);
    }

    private function loadAdminToDatabase(ObjectManager $manager): void
    {
        $adminMembre = new Membre();

        $adminMembre->setPrenom("M'hamed");
        $adminMembre->setNom("LEHBAB");
        $adminMembre->setEmail("m.l@example.com");
        $adminMembre->setPseudo("Admin001");

        // homme ou femme
        $adminMembre->setCivilite("homme");
        $adminMembre->setDateEnregistrement(new \DateTime());
        $adminMembre->setMdp($this->passwordHasher->hashPassword($adminMembre, "admin"));

        // make this membre admin
        $adminMembre->setAdminRole();

        $manager->persist($adminMembre);
        $manager->flush();
    }

    private function loadRegularMembreToDatabase(ObjectManager $manager): void
    {
        $faker = Factory::create();
        for ($i = 0; $i < 5; $i++) {
            $membre = new Membre();

            $membre->setPrenom($faker->firstName);
            $membre->setNom($faker->lastName);
            $membre->setEmail($faker->email);
            $membre->setPseudo($faker->userName);

            // homme ou femme
            if ($i % 2 == 0) {
                $membre->setCivilite("homme");
            } else {
                $membre->setCivilite("femme");
            }

            $membre->setDateEnregistrement(new \DateTime());
            $membre->setMdp($faker->password);
            $membre->setMdp($this->passwordHasher->hashPassword($membre, "notadmin"));

            $manager->persist($membre);
        }

        $manager->flush();
    }

    private function loadVehiculesToDatabase(ObjectManager $manager): void
    {
        $marques = ['AMG', 'Ferrari', 'Tesla', 'Lotus', 'Porsche', 'Bmw'];
        $modeles = ['GT', 'F12', 'Model X', 'LF1', '911', 'M4'];
        $titres = ['Occasion', 'New', 'Occasion', 'New', 'Occasion', 'New'];
        $prixJournaliers = [1300, 2700, 5800, 4500, 4100, 6000];
        $descriptions = [
            "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis ultrices eros id erat tempor, et efficitur ex cursus. Proin porta mi quis augue lacinia, sit amet lacinia sem tincidunt.",
            "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis ultrices eros id erat tempor, et efficitur ex cursus. Proin porta mi quis augue lacinia, sit amet lacinia sem tincidunt.",
            "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis ultrices eros id erat tempor, et efficitur ex cursus. Proin porta mi quis augue lacinia, sit amet lacinia sem tincidunt.",
            "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis ultrices eros id erat tempor, et efficitur ex cursus. Proin porta mi quis augue lacinia, sit amet lacinia sem tincidunt.",
            "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis ultrices eros id erat tempor, et efficitur ex cursus. Proin porta mi quis augue lacinia, sit amet lacinia sem tincidunt.",
            "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis ultrices eros id erat tempor, et efficitur ex cursus. Proin porta mi quis augue lacinia, sit amet lacinia sem tincidunt.",
        ];

        // Get the public directory path
        $projectDirectory = $this->parameterBag->get('kernel.project_dir');

        // Define the path to the images folder
        $imagesFolderPath = $projectDirectory . '/public/vehicules_directory';

        // Get the list of files in the folder
        $imageFiles = scandir($imagesFolderPath);

        // Filter out "." and ".." entries
        $imageFiles = array_diff($imageFiles, ['.', '..']);

        for ($i = 0; $i < 6; $i++) {
            $vehicule = new Vehicule();

            $vehicule->setDateEnregistrement(new \DateTime());
            $vehicule->setMarque($marques[$i]);
            $vehicule->setModele($modeles[$i]);
            $vehicule->setTitre($titres[$i]);
            $vehicule->setPrixJournalier($prixJournaliers[$i]);
            $vehicule->setDescription($descriptions[$i]);
            $vehicule->setPhoto($imageFiles[$i + 2]);

            $manager->persist($vehicule);
        }

        $manager->flush();
    }
}
