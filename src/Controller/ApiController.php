<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Product;
use Carbon\Carbon;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class ApiController extends AbstractController
{

    private $requestStack;

    private $signature;

    private  $em;

    public function __construct(RequestStack $requestStack,EntityManagerInterface $doctrine)
    {
        $this->requestStack = $requestStack;
        $this->signature = base64_encode('path.api');
        $this->em = $doctrine;
    }

    #[Route('/api', name: 'app_api')]
    public function index(): Response
    {
        return $this->render('api/index.html.twig', [
            'controller_name' => 'ApiController',
        ]);
    }




    /**
     * @Route("api/get-jwt-token",name="get_jwt")
     * @return JsonResponse
     */


    public function getJwt(Request $request){
        $repo = $this->getDoctrine()->getRepository(Customer::class);
        $customer = $repo->findOneBy(['username'=>$request->get('username') , 'password'=>md5($request->get('password'))]);

        if(!empty($customer)){
            $em = $this->getDoctrine()->getManager();
            $jwt =  base64_encode(rand(100,999).date('YmdHis'));
            $expires = Carbon::now()->addHour();
            $customer->setJwt($jwt)->setJwtExpires($expires);
            $em->persist($customer);
            $em->flush();
            return new JsonResponse(['expires'=> $expires,'jwt-token'=>$jwt.".". $this->signature]);
        }else{
            return new JsonResponse(['error'=> 'not Found'],404);
        }


        // return new Response("ok");

    }


    /**
     * @Route("api/get-products",name="get_products")
     * @return JsonResponse
     */
    public function getProducts(Request $request){
        $array = explode(".",$request->headers->get('jwt-token'));

        $repo = $this->getDoctrine()->getRepository(Customer::class);
        $customer = $repo->findOneBy(['jwt'=>$array[0]]);

        if(!empty($customer)){
            $products = $this->getDoctrine()->getRepository(Product::class);
            $return_array=array();
            $i=0;
            foreach ($products->findAll() as $item){
                $return_array[$i]['product_id']=$item->getId();
                $return_array[$i]['name']=$item->getName();
                $return_array[$i]['description']=$item->getDescription();
                $return_array[$i]['price']=$item->getPrice();
                $i++;
            }

            return new JsonResponse(['products'=>$return_array]);
        }else{
            return new JsonResponse(['error'=> 'not Found'],404);
        }


        // return new Response("ok");

    }


    private function itemArray($order_id){
        $repo = $this->getDoctrine()->getRepository(OrderItem::class);
        $items = $repo->findBy(['order_id'=>$order_id]);


        $price = 0;
        $item_array = array();
        $i=0;
        foreach ($items as $item){
            $product=$this->em->find(Product::class,$item->getProductId());
            $item_array[$i]['product'] =$product->getName();
            $item_array[$i]['price'] =$product->getPrice();
            $item_array[$i]['quantity'] =$item->getQuantity();
            $price+=$product->getPrice()*$item->getQuantity();
            $i++;
        }
        $repo = $this->getDoctrine()->getRepository(Order::class);
        $order = $repo->find($order_id);

           $order->setPrice($price);
            $this->em->persist($order);
            $this->em->flush();

        return array('price'=>$price,'products'=>$item_array);
    }

    /**
     * @Route("api/place-order",name="place-order")
     * @return JsonResponse
     */
    public function placeOrder(Request $request){
        $array = explode(".",$request->headers->get('jwt-token'));

        $repo = $this->getDoctrine()->getRepository(Customer::class);
        $customer = $repo->findOneBy(['jwt'=>$array[0]]);

        if(!empty($customer)){
            $expires = Carbon::parse($customer->getJwtExpires())->format('Y-m-d H:i:s');
            if($expires<=Carbon::now()){
                return new JsonResponse(['error'=>'JWT Expired'],403);
            }
            $sql='SELECT * FROM orders WHERE customer_id=:customer_id AND shipping_date>=:date_now';
            $statement = $this->em->getConnection()->prepare($sql);
            $statement->bindValue('customer_id',$customer->getId());
            $statement->bindValue('date_now',Carbon::now()->format('Y-m-d H:i'));
            $order=$statement->executeQuery()->fetchOne();

            if(empty($order)){
                if(empty($request->get('address'))){
                    $faker = Factory::create();
                    $address= $faker->address;
                }else{

                    $address=$request->get('address');
                }


                $shipping_date=Carbon::now()->addDays(3)->format('Y-m-d H:i');
                $order = new Order();
                $order->setCustomerId($customer->getId())
                    ->setShippingDate(Carbon::parse($shipping_date))
                    ->setOrderCode(rand(1000,9999))
                    ->setAddress($address)
                    ->setPrice(100);
                $this->em->persist($order);
                $this->em->flush();
                $order = $order->getId();
            }

            $repo = $this->getDoctrine()->getRepository(OrderItem::class);
            $orderItem = $repo->findOneBy(['order_id'=>$order , 'product_id'=>$request->get('product_id')]);
            if(empty($orderItem)){
                $repo = $this->getDoctrine()->getRepository(Product::class);
                $product = $repo->find($request->get('product_id'));
                if(empty($product)){
                    return new JsonResponse(['error'=>'Product not found'],404);
                }else{
                $orderItem = new OrderItem();
                $orderItem->setOrderId($order)
                    ->setProductId($request->get('product_id'))
                    ->setQuantity($request->get('quantity'));
                }

            }else{
                $orderItem->setQuantity($request->get('quantity'));
            }
            $this->em->persist($orderItem);
            $this->em->flush();

            $return_array = $this->itemArray($order);


         //  return new JsonResponse($return_array,200);
            return new JsonResponse(['products'=>$return_array['products'],'price'=>$return_array['price'] ],200);
        }else{
            return new JsonResponse(['error'=> 'not Found'],404);
        }


        // return new Response("ok");

    }

    /**
     * @Route("api/update-order",name="update-order")
     * @return JsonResponse
     */
    public function updateOrder(Request $request){
        $array = explode(".",$request->headers->get('jwt-token'));

        $repo = $this->getDoctrine()->getRepository(Customer::class);
        $customer = $repo->findOneBy(['jwt'=>$array[0]]);

        if(!empty($customer)){
            $expires = Carbon::parse($customer->getJwtExpires())->format('Y-m-d H:i:s');
            if($expires<=Carbon::now()){
                return new JsonResponse(['error'=>'JWT Expired'],403);
            }

            $order_code = (!empty($request->get('order_code')))?$request->get('order_code'):'';
            $sql='SELECT * FROM orders WHERE customer_id=:customer_id  AND order_code=:order_code';
            $statement = $this->em->getConnection()->prepare($sql);
            $statement->bindValue('customer_id',$customer->getId());
            $statement->bindValue('order_code',$order_code);
            $order=$statement->executeQuery()->fetchOne();
            $quantity = (!empty($request->get('quantity')))?$request->get('quantity'):0;
            if(empty($order)){
                return new JsonResponse(['error'=>'Order Not Found'],404);
            }

            $repo = $this->getDoctrine()->getRepository(Order::class);
            $order_ = $repo->find($order);
            $expires = Carbon::parse($order_->getShippingDate())->format('Y-m-d H:i:s');
            if($expires<=Carbon::now()){
                return new JsonResponse(['error'=>'Shipping Date Expired'],403);
            }

            $repo = $this->getDoctrine()->getRepository(OrderItem::class);
            $orderItem = $repo->findOneBy(['order_id'=>$order , 'product_id'=>$request->get('product_id')]);
            if(empty($orderItem)){
                $repo = $this->getDoctrine()->getRepository(Product::class);
                $product = $repo->find($request->get('product_id'));
                if(empty($product)){
                    return new JsonResponse(['error'=>'Product not found'],404);
                }else{
                    if($quantity>0){
                    $orderItem = new OrderItem();
                    $orderItem->setOrderId($order)
                        ->setProductId($request->get('product_id'))
                        ->setQuantity($quantity);
                    $this->em->persist($orderItem);
                    $this->em->flush();
                    }
                }


            }else{

                if($quantity==0){
                    $this->em->remove($orderItem);
                    $this->em->flush();
                }else{
                    $orderItem->setQuantity($request->get('quantity'));
                    $this->em->persist($orderItem);
                    $this->em->flush();
                }


            }


            $return_array = $this->itemArray($order);


            //  return new JsonResponse($return_array,200);
            return new JsonResponse(['products'=>$return_array['products'],'price'=>$return_array['price'] ],200);
        }else{
            return new JsonResponse(['error'=> 'not Found'],404);
        }


        // return new Response("ok");

    }


    /**
     * @Route("api/order-detail",name="order-detail")
     * @return JsonResponse
     */
    public function orderDetail(Request $request){

        $array = explode(".",$request->headers->get('jwt-token'));
        $repo = $this->getDoctrine()->getRepository(Customer::class);
        $customer = $repo->findOneBy(['jwt'=>$array[0]]);

        if(!empty($customer)){
            $expires = Carbon::parse($customer->getJwtExpires())->format('Y-m-d H:i:s');
            if($expires<=Carbon::now()){
                return new JsonResponse(['error'=>'JWT Expired'],403);
            }

            $order_code = (!empty($request->get('order_code')))?$request->get('order_code'):'';
            $sql='SELECT * FROM orders WHERE customer_id=:customer_id  AND order_code=:order_code';
            $statement = $this->em->getConnection()->prepare($sql);
            $statement->bindValue('customer_id',$customer->getId());
            $statement->bindValue('order_code',$order_code);
            $order=$statement->executeQuery()->fetchOne();

            if(empty($order)){
                return new JsonResponse(['error'=>'Order Not Found'],404);
            }

            $repo = $this->getDoctrine()->getRepository(Order::class);
             $order_ = $repo->find($order);

            $return_array = $this->itemArray($order);

            return new JsonResponse(['order_code'=>$order_->getOrderCode(),'shipping_date'=>Carbon::parse($order_->getShippingDate())->format('Y-m-d H:i')
                ,'products'=>$return_array['products'],'price'=>$return_array['price']],200);
        }else{
            return new JsonResponse(['error'=> 'not Found'],404);
        }

        // return new Response("ok");

    }


    /**
     * @Route("api/list-orders",name="list-orders")
     * @return JsonResponse
     */



    public function listOrders(Request $request){

        $array = explode(".",$request->headers->get('jwt-token'));
        $repo = $this->getDoctrine()->getRepository(Customer::class);
        $customer = $repo->findOneBy(['jwt'=>$array[0]]);

        if(!empty($customer)){
            $expires = Carbon::parse($customer->getJwtExpires())->format('Y-m-d H:i:s');
            if($expires<=Carbon::now()){
                return new JsonResponse(['error'=>'JWT Expired'],403);
            }


            $repo = $this->getDoctrine()->getRepository(Order::class);
            $orders = $repo->findBy(['customer_id'=>$customer->getId()]);

            $result_array = array();
            $i=0;
            foreach ($orders as $order){
                $item_array = $this->itemArray($order->getId());
                $result_array[$i]['order_code']=$order->getOrderCode();
                $result_array[$i]['shipping_date']=Carbon::parse($order->getOrderCode())->format('Y-m-d H:i');
                $result_array[$i]['price']=$order->getPrice();
                $result_array[$i]['products']=$item_array['products'];


            }

            return new JsonResponse($result_array);
          //  return new JsonResponse(['order_code'=>$order_->getOrderCode(),'shipping_date'=>Carbon::parse($order_->getShippingDate())->format('Y-m-d H:i'),'products'=>$return_array['products'],'price'=>$return_array['price']],200);
        }else{
            return new JsonResponse(['error'=> 'not Found'],404);
        }

        // return new Response("ok");

    }




}
