<?php

namespace App\Controller;

use App\Entity\Article;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\Get;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use FOS\RestBundle\Controller\FOSRestController;

class ArticleController extends FOSRestController
{
    /**
     * @Rest\Get(
     *      path = "/article/{id}",
     *      name = "article_show",
     *      requirements = {"id"="\d+"}
     *      )
     * @Rest\View
     */
    public function showAction(Article $article)
    {
        $data = $this->get('serializer')->serialize($article, 'json');

        $response = new Response($data);
        // $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Rest\Get(
     *      path = "/articles",
     *      name = "article_list",
     *      )
     * @Rest\View
     */
    public function listAction()
    {
        $articles = $this->getDoctrine()->getRepository(Article::class)->findAll();

        $data = $this->get('serializer')->serialize($articles, 'json');

        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Rest\Post(
     *      path = "/articles-create",
     *      name = "article_create"
     *      )
     * @Rest\View(StatusCode = 201)
     * @ParamConverter("article", converter="fos_rest.request_body")
     */
    public function createAction(Article $article, Request $request)
    {
        $data = $request->getContent();
        $article = $this->get('serializer')->deserialize($data, Article::class, 'json');

        $em = $this->getDoctrine()->getManager();
        $em->persist($article);
        $em->flush();

        return $this->view(
                $article,
                Response::HTTP_CREATED,
                [
                    'Location' => $this->generateUrl('article_show',
                    [
                        'id' => $article->getId(),
                        UrlGeneratorInterface::ABSOLUTE_URL
                    ])
                ]
            );
    }

    
}
