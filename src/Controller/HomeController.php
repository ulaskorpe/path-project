<?php

namespace App\Controller;

use App\Entity\Customer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    #[Route('/', name: 'path_home')]
    public function index()
    {


        // stores an attribute in the session for later reuse

        $session = $this->requestStack->getSession();
      //  $session->set('admin_id', null);
        if(empty($session->get('admin_id'))){
            return $this->render('login.html.twig', []);
        }else{
            $repo = $this->getDoctrine()->getRepository(Customer::class);
            $customer = $repo->find($session->get('admin_id'));

            return $this->render('index.html.twig', ['customer'=>$customer]);
        }

    }

    /**
     * @Route("/login-post",name="login_post")
     * @return Response
     */


    public function loginPost(Request $request){
        $repo = $this->getDoctrine()->getRepository(Customer::class);
        $customer = $repo->findOneBy(['username'=>$request->get('username') , 'password'=>md5($request->get('password'))]);
        if(!empty($customer)){
            $session = $this->requestStack->getSession();
            $session->set('admin_id', $customer->getId());
            return new  Response("ok");
        }else{
            return new  Response('none');
        }



       // return new Response("ok");
          //  return new Response($request->get('username'));
          }




}
