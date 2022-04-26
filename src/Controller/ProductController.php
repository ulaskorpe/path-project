<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Product;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{


    protected $objectManager;




    /**
     * @Route("/product/list",name="product_list")
     * return Response
     */

    public function index(): Response
    {


        $products = $this->getDoctrine()->getRepository(Product::class);
        return $this->render('product/index.html.twig', [
            'products'=>$products->findAll()
        ]);
    }

    /**
     * @Route("/product/create",name="product_create")
     * @return JsonResponse
     */
    public function create(){
        $faker = Factory::create();
        $em = $this->getDoctrine()->getManager();
        $product = new Product();
        $product->setName( $faker->name)
            ->setDescription($faker->email)
            ->setPrice(rand(1,300)*10);
        $em->persist($product);
        $em->flush();
        return new  JsonResponse($product->getName());
    }

    /**
     * @Route("/product/{id}",name="product_show")
     * @return JsonResponse
     */

    public function show($id){

        $repo = $this->getDoctrine()->getRepository(Product::class);
        $product = $repo->find($id);
        return new  JsonResponse($product->getName());
    }


    /**
     * @Route("/product/update/{id}",name="product_update")
     * @return JsonResponse
     */

    public function update($id)
    {
        $faker = Factory::create();
        $em = $this->getDoctrine()->getManager();
        $repo = $this->getDoctrine()->getRepository(Product::class);
        $product = $repo->find($id);
        if (!empty($product)){
            $product->setName($faker->name);
            $product->setDescription($faker->address);
        $em->persist($product);
        $em->flush();
        return new  JsonResponse($product->getName());
        }else{
            return new JsonResponse('notfound');
        }
    }

    /**
     * @Route("/product/delete/{id}",name="product_delete")
     * @return JsonResponse
     */

    public function delete($id){
        $em = $this->getDoctrine()->getManager();
        $repo = $this->getDoctrine()->getRepository(Product::class);
        $product = $repo->find($id);
            $em->remove($product);
        $em->flush();
        return new  JsonResponse($product->getName());
    }
}
