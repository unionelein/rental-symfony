<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use AppBundle\Entity\Category;
use AppBundle\Services\AppManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/{typeName}", name="homepage", defaults={"typeName" = null})
     * @Method({"GET"})
     */
    public function pageAction($typeName)
    {
        $type = $this->get('app_manager')->getTypeByName($typeName);

        if ($type === null) {
            throw $this->createNotFoundException('404');
        }

        $em = $this->getDoctrine()->getManager();

        $typeName = array_search($type, AppManager::TYPE);
        $products = $em->getRepository(Product::class)->findBy(["type" => $type]);
        $categories = $em->getRepository(Category::class)->findBy(["type" => $type]);

        return $this->render('@App/main.html.twig', [
            'products' => $products,
            'categories' => $categories,
            'type' => $typeName
        ]);
    }

    /**
     * @Route("/{typeName}/{category}", name="products_by_category", defaults={"category" = null})
     *
     * @param Request $request
     * @param $typeName
     * @param $category
     * @return string
     */
    public function getProductsByCategoryAction(Request $request, $typeName, $category)
    {
        $type = $this->get('app_manager')->getTypeByName($typeName);

        if ($type === null) {
            throw $this->createNotFoundException('404');
        }

        $em = $this->getDoctrine()->getManager();

        $typeName = array_search($type, AppManager::TYPE);

        $products = $em->getRepository(Product::class)->findProductsByCategoryType($category, $type);
        $categories = $em->getRepository(Category::class)->findBy(["type" => $type]);;


        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                "products" => $this->renderView('@App/products.html.twig', [
                    'products' => $products,
                    'type' => $typeName
                ])
            ]);
        } else {
            return $this->render('@App/main.html.twig', [
                'products' => $products,
                'categories' => $categories,
                'type' => $typeName
            ]);
        }
    }

    /**
     * @Route("/{typeName}/{category}/{productSlug}", name="product")
     * @Method({"GET"})
     *
     * @param  $typeName
     * @param  $category
     * @param  $productSlug
     * @return Response
     */
    public function productAction($typeName, $category, $productSlug)
    {
        $type = $this->get('app_manager')->getTypeByName($typeName);

        if ($type === null) {
            throw $this->createNotFoundException('404');
        }

        $em = $this->getDoctrine()->getManager();

        $typeName = array_search($type, AppManager::TYPE);
        $product = $em->getRepository(Product::class)->findOneBy(["slug" => $productSlug]);
        $categories = $em->getRepository(Category::class)->findBy(["type" => $type]);;

        if (!$product) {
            throw $this->createNotFoundException('404');
        }

        return $this->render('@App/product.html.twig', [
            'product' => $product,
            'categories' => $categories,
            'type' => $typeName,
        ]);
    }
}
