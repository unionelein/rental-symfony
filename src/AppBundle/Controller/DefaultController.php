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
    const COUNT_FOR_PAGE = 12;

    /**
     * @Route("/{page}", name="mainpage", defaults={"page" = 1}, requirements={"page"="\d+"})
     */
    public function mainAction(Request $request, $page)
    {
        if ($page < 1) {
            throw $this->createNotFoundException('404');
        }

        $em = $this->getDoctrine()->getManager();
        $products = $em->getRepository(Product::class)->findAllProducts();
        $categories = $em->getRepository(Category::class)->getCategories();

        $countOfPages = ceil(count($products)/self::COUNT_FOR_PAGE);
        $products = array_slice($products, ($page-1)*self::COUNT_FOR_PAGE, self::COUNT_FOR_PAGE);

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                "products" => $this->renderView('@App/products.html.twig', [
                    'products' => $products,
                    'type' => 'all',
                    'countOfPages' => $countOfPages,
                    'currentPage' => $page
                ])
            ]);
        } else {
            return $this->render('@App/main.html.twig', [
                'products' => $products,
                'categories' => $categories,
                'type' => 'all',
                'countOfPages' => $countOfPages,
                'currentPage' => $page
            ]);
        }
    }

    /**
     * @Route("/{typeName}/{page}", name="homepage", defaults={"page" = 1}, requirements={"page"="\d+","typeName"="(arenda|pokypka)"})
     */
    public function pageAction(Request $request, $typeName, $page)
    {
        if ($page < 1) {
            throw $this->createNotFoundException('404');
        }

        $type = $this->get('app_manager')->getTypeByName($typeName);

        if ($type === null) {
            throw $this->createNotFoundException('404');
        }

        $em = $this->getDoctrine()->getManager();

        $typeName = array_search($type, AppManager::TYPE);
        $products = $em->getRepository(Product::class)->findAllProducts($type);
        $categories = $em->getRepository(Category::class)->getCategoriesByType($type);

        foreach ($categories as $key => $category) {
            $categories[$key]["type"] = AppManager::TYPE_NAME[$category["type"]];
        }

        $countOfPages = ceil(count($products)/self::COUNT_FOR_PAGE);
        $products = array_slice($products, ($page-1)*self::COUNT_FOR_PAGE, self::COUNT_FOR_PAGE);

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                "products" => $this->renderView('@App/products.html.twig', [
                    'products' => $products,
                    'type' => $typeName,
                    'countOfPages' => $countOfPages,
                    'currentPage' => $page
                ])
            ]);
        } else {
            return $this->render('@App/main.html.twig', [
                'products' => $products,
                'categories' => $categories,
                'type' => $typeName,
                'countOfPages' => $countOfPages,
                'currentPage' => $page
            ]);
        }
    }

    /**
     * @Route("/{categorySlug}/{page}", name="products_by_category", defaults={"categorySlug" = null, "page" = 1}, requirements={"page"="\d+"})
     *
     * @param Request $request
     * @param $categorySlug
     * @return string
     */
    public function getProductsByCategoryAction(Request $request, $categorySlug, $page)
    {
        if ($page < 1) {
            throw $this->createNotFoundException('404');
        }

        $em = $this->getDoctrine()->getManager();

        $products = $em->getRepository(Product::class)->findProductsByCategoryAndType($categorySlug);
        $categories = $em->getRepository(Category::class)->getCategories();

        $countOfPages = ceil(count($products)/self::COUNT_FOR_PAGE);
        $products = array_slice($products, ($page-1)*self::COUNT_FOR_PAGE, self::COUNT_FOR_PAGE);

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                "products" => $this->renderView('@App/products.html.twig', [
                    'products' => $products,
                    'type' => 'all',
                    'countOfPages' => $countOfPages,
                    'currentPage' => $page
                ])
            ]);
        } else {
            return $this->render('@App/main.html.twig', [
                'products' => $products,
                'categories' => $categories,
                'currentCategory' => $categorySlug,
                'type' => 'all',
                'countOfPages' => $countOfPages,
                'currentPage' => $page
            ]);
        }
    }

    /**
     * @Route("{typeName}/{categorySlug}/{page}", name="products_by_category_and_type", defaults={"categorySlug" = null, "page" = 1}, requirements={"page"="\d+","typeName"="(arenda|pokypka)"})
     *
     * @param Request $request
     * @param $categorySlug
     * @return string
     */
    public function getProductsByCategoryAndTypeAction(Request $request, $typeName, $categorySlug, $page)
    {
        if ($page < 1) {
            throw $this->createNotFoundException('404');
        }

        $type = $this->get('app_manager')->getTypeByName($typeName);

        if ($type === null) {
            throw $this->createNotFoundException('404');
        }

        $em = $this->getDoctrine()->getManager();

        $typeName = array_search($type, AppManager::TYPE);

        $products = $em->getRepository(Product::class)->findProductsByCategoryAndType($categorySlug, $type);
        $categories = $em->getRepository(Category::class)->getCategoriesByType($type);

        foreach ($categories as $key => $category) {
            $categories[$key]["type"] = AppManager::TYPE_NAME[$category["type"]];
        }

        $countOfPages = ceil(count($products)/self::COUNT_FOR_PAGE);
        $products = array_slice($products, ($page-1)*self::COUNT_FOR_PAGE, self::COUNT_FOR_PAGE);

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                "products" => $this->renderView('@App/products.html.twig', [
                    'products' => $products,
                    'type' => $typeName,
                    'countOfPages' => $countOfPages,
                    'currentPage' => $page
                ])
            ]);
        } else {
            return $this->render('@App/main.html.twig', [
                'products' => $products,
                'categories' => $categories,
                'currentCategory' => $categorySlug,
                'type' => $typeName,
                'countOfPages' => $countOfPages,
                'currentPage' => $page
            ]);
        }
    }

    /**
     * @Route("/{categorySlug}/{productSlug}", name="product")
     *
     * @param  Request $request
     * @param  $categorySlug
     * @param  $productSlug
     * @return Response
     */
    public function productAction(Request $request, $categorySlug, $productSlug)
    {
        $em = $this->getDoctrine()->getManager();

        $product = $em->getRepository(Product::class)
            ->findProductByCategorySlugAndProductSlug($categorySlug, $productSlug);
        $categories = $em->getRepository(Category::class)->getCategories();

        if (!$product) {
            throw $this->createNotFoundException('404');
        }

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                "products" => $this->renderView('@App/product.html.twig', [
                    'product' => $product,
                    'type' => 'all'
                ])
            ]);
        } else {
            return $this->render('@App/main.html.twig', [
                'product' => $product,
                'categories' => $categories,
                'type' => 'all',
                'page' => 'product',
                'currentCategory' => $categorySlug
            ]);
        }
    }
}
