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
        $categories = $em->getRepository(Category::class)->getCategoriesByType($type);

        return $this->render('@App/main.html.twig', [
            'products' => $products,
            'categories' => $categories,
            'type' => $typeName
        ]);
    }

    /**
     * @Route("/{typeName}/{categorySlug}", name="products_by_category", defaults={"categorySlug" = null})
     *
     * @param Request $request
     * @param $typeName
     * @param $categorySlug
     * @return string
     */
    public function getProductsByCategoryAction(Request $request, $typeName, $categorySlug)
    {
        $type = $this->get('app_manager')->getTypeByName($typeName);

        if ($type === null) {
            throw $this->createNotFoundException('404');
        }

        $em = $this->getDoctrine()->getManager();

        $typeName = array_search($type, AppManager::TYPE);

        $products = $em->getRepository(Product::class)->findProductsByCategoryAndType($categorySlug, $type);
        $categories = $em->getRepository(Category::class)->getCategoriesByType($type);

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
     * @Route("/{typeName}/{categorySlug}/{productSlug}", name="product")
     *
     * @param  Request $request
     * @param  $typeName
     * @param  $categorySlug
     * @param  $productSlug
     * @return Response
     */
    public function productAction(Request $request, $typeName, $categorySlug, $productSlug)
    {
        $type = $this->get('app_manager')->getTypeByName($typeName);

        if ($type === null) {
            throw $this->createNotFoundException('404');
        }

        $em = $this->getDoctrine()->getManager();

        $typeName = array_search($type, AppManager::TYPE);
        $product = $em->getRepository(Product::class)
            ->findProductByCategorySlugAndProductSlug($categorySlug, $productSlug);
        $categories = $em->getRepository(Category::class)->getCategoriesByType($type);

        if (!$product) {
            throw $this->createNotFoundException('404');
        }

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                "products" => $this->renderView('@App/product.html.twig', [
                    'product' => $product,
                    'type' => $typeName
                ])
            ]);
        } else {
            return $this->render('@App/main.html.twig', [
                'product' => $product,
                'categories' => $categories,
                'type' => $typeName,
                'page' => 'product'
            ]);
        }
    }
}
