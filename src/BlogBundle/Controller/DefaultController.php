<?php

namespace BlogBundle\Controller;

use AdminBundle\Entity\Blog;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Template()
     */
    public function indexAction($page =1)
    {
        $dql= "select b from BlogBundle:Blog b order by b.id desc";
        $query = $this->getDoctrine()->getManager()->createQuery($dql);

        $pagination = $this->get('knp_paginator');
        $blogList = $pagination->paginate($query, $page, 3);
       // $blogList = $this->getDoctrine()->getManager()->getRepository('BlogBundle:Blog')->findAll();
        return [
          'blogList'=> $blogList
        ];
    }

    /**
     * @Template()
     */
    public function singleBlogAction($id)
    {
        $blog = $this->getDoctrine()->getManager()->getRepository('BlogBundle:Blog')->find($id);

        if (!$blog) {
            throw $this->createNotFoundException('Unable to find Blog post.');
        }

        $em = $this->getDoctrine()->getManager();
        $comments = $em->getRepository('BlogBundle:Comment')
            ->getCommentsForBlog($blog->getId());


        return [
            'blog' => $blog,
            'comments' => $comments
       ];
    }

    /**
     * @Template()
     */
      public function sidebarAction($limit = 10)
    {

        $dql = "select t.tags from BlogBundle:Blog t ";
        $blogTags= $this->getDoctrine()->getManager()->createQuery($dql)->getResult();


        $tags = array();
        foreach ($blogTags as $blogTag)
        {
            $tags = array_merge(explode(",", $blogTag['tags']), $tags);
        }
        foreach ($tags as &$tag)
        {
            $tag= trim($tag);
        }

        $tagsArray = array_unique($tags);

        $dql2 = "select c from BlogBundle:Comment c order by c.id desc";

        $latestComments = $this->getDoctrine()->getManager()->createQuery($dql2)->setMaxResults($limit)->getResult();

        return ["tagsArray" =>$tagsArray,
        'latestComments'=> $latestComments];
    }

    /**
     * @Template()
     */
    public function blogsByTagAction($tag)
    {
        $dql = "select b from BlogBundle:Blog b WHERE b.tags LIKE '%$tag%' order by b.id desc";
        $blogs = $this->getDoctrine()->getManager()->createQuery($dql)->getResult();

        return ['blogs'=>$blogs ];
    }





}
