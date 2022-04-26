<?php

namespace App\DataFixtures;

use App\Entity\Company;
use App\Entity\Customer;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{

   private function fixName($x)
    {
        $x = trim(strtolower($x));

        //$uzanti=$this->uzantiBul($x);
        //$x=substr($x,0,(strlen($x)-(strlen($uzanti)+1)));
        $x = (str_replace("Þ", "s", $x));
        $x = (str_replace("þ", "s", $x));
        $x = (str_replace("Ý", "i", $x));
        $x = (str_replace("ý", "i", $x));
        $x = (str_replace("Ç", "c", $x));
        $x = (str_replace("ç", "c", $x));
        $x = (str_replace("ð", "g", $x));
        $x = (str_replace("Ð", "g", $x));
        $x = (str_replace("ü", "u", $x));
        $x = (str_replace("Ü", "u", $x));
        $x = (str_replace("ö", "o", $x));
        $x = (str_replace("Ö", "o", $x));
        $x = (str_replace(".", "_", $x));
        $x = (str_replace(",", "_", $x));
        $x = (str_replace("!", "_", $x));
        $x = (str_replace("?", "_", $x));
        $x = (str_replace("/", " ", $x));
        $x = (str_replace(" ", "_", $x));
        $x = (str_replace("@", "_", $x));
        //  $x=(str_replace(" ","_",$x));
        // $x=(str_replace("_","",$x));
        $xz = "";
        for ($i = 0; $i < (strlen($x)); $i++) {
            $ord = ord(substr($x, $i, 1));
            if ((($ord >= 48) && ($ord <= 57)) || (($ord >= 65) && ($ord <= 90)) || (($ord >= 97) && ($ord <= 122)) || ($ord !=249) || ($ord!=250)) {
                $xz .= substr($x, $i, 1);
            } else {
                $xz .= "";
            }
        }

        $xz = (empty($xz)) ? "_" : $xz;
        // $xz=$xz.".".$uzanti;
        return $xz;
    }



    public function load(ObjectManager $manager): void
    {
            $faker = Factory::create();
            $product_array = ['PlayStation3','Samsung Galaxy A30','Yamaha Dragstar XVS','Xiaomi Note8','Honda Shadow 750'];

            foreach ($product_array as $item){

                $txt="";
                for($i=0;$i<rand(10,20);$i++){
                    $txt.=$faker->word." ";
                     }

                 $product = new Product();
                 $product->setName($item)
                     ->setDescription(trim($txt))
                    ->setPrice(rand(100,1000)*10);
                 $manager->persist($product);

            }
            for($i=0;$i<3;$i++){
                $name  = $faker->name;
                $customer = new Customer();

                $customer->setName($name)
                    ->setUsername($this->fixName($name))
                    ->setPassword(md5('123123'));
                $manager->persist($customer);
            }
            $company = new Company();
            $company->setName('ABC Company');
            $manager->persist($company);

        $manager->flush();
    }
}
