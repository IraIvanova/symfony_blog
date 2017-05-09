<?php

namespace AdminBundle\Controller;

use BlogBundle\Entity\Blog;
use BlogBundle\Form\BlogType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class BlogController extends Controller
{

    /**
     * @Template()
     */
    public function indexAction()
    {
        $dql= "select b from BlogBundle:Blog b order by b.id desc";
        $blogList = $this->getDoctrine()->getManager()->createQuery($dql)->getResult();
      //  $blogList = $this->getDoctrine()->getManager()->getRepository('BlogBundle:Blog')->findAll();


        return [
            'blogList' => $blogList
        ];
    }

    /**
     * @Template()
     */
    public function addAction(Request $request)
    {
        $blog = new Blog();
        $form = $this->createForm(BlogType::class, $blog);
        if ($request->isMethod('POST')) {

            $form->handleRequest($request);

            if ($form->isSubmitted()) {

                $filesAr = $request->files->get("blogbundle_blog");

                if (isset($filesAr["image"])) {
                    /** @var UploadedFile $photoFile */
                    $photoFile = $filesAr["image"];
                    $checkImgService = $this->get("myblog.image_check");
                    try {
                        $checkImgService->check($photoFile);
                    } catch (\InvalidArgumentException $ex) {
                        $this->addFlash("error", "Не верный тип картинки");
                        return $this->redirectToRoute('blog_admin.blog_create');
                    }
                    $photoFileName = rand(1000000, 9999999) . "." . $photoFile->getClientOriginalExtension();
                    $photoFile->move($this->get("kernel")->getRootDir() . "/../web/images/", $photoFileName);
                    $blog->setImage($photoFileName);

                    $author = $this->getUser();
                    $blog->setAuthor($author);
                    if (!$blog) {
                        throw $this->createNotFoundException('Unable to find Blog post.');
                    }
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($blog);
                    $em->flush();

                    return $this->redirectToRoute("blog_admin.blog_list");
                }
            }
        }
        return [
            'blog' => $blog,
            'form' => $form->createView(),
        ];
    }

    /**
     * @Template()
     */
    public function editAction(Request $request, $id)
    {
        $blog = $this->getDoctrine()->getManager()->getRepository('BlogBundle:Blog')->find($id);
        $form = $this->createForm(BlogType::class, $blog);
        if ($request->isMethod('POST')) {

            $form->handleRequest($request);

            if ($form->isSubmitted()) {

                $filesAr = $request->files->get("blogbundle_blog");

                if (isset($filesAr["image"])) {
                    /** @var UploadedFile $photoFile */
                    $photoFile = $filesAr["image"];
                    $checkImgService = $this->get("myblog.image_check");
                    try {
                        $checkImgService->check($photoFile);
                    } catch (\InvalidArgumentException $ex) {
                        $this->addFlash("error", "Не верный тип картинки");
                        return $this->redirectToRoute('blog_admin.blog_create');
                    }
                    $photoFileName = rand(1000000, 9999999) . "." . $photoFile->getClientOriginalExtension();
                    $photoFile->move($this->get("kernel")->getRootDir() . "/../web/images/", $photoFileName);
                    $blog->setImage($photoFileName);

                    $author = $this->getUser();
                    $blog->setAuthor($author);

                    $em = $this->getDoctrine()->getManager();
                    $em->persist($blog);
                    $em->flush();

                    return $this->redirectToRoute("blog_admin.blog_list");
                }
            }
        }
        return [
            'blog' => $blog,
            'form' => $form->createView()
        ];
    }


    public function deleteAction( Blog $blog)
    {

        $em = $this->getDoctrine()->getManager();
        $em->remove($blog);
        $em->flush();


        return $this->redirectToRoute("blog_admin.blog_list");
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
//        $comments = $em->getRepository('BlogBundle:Comment')
//            ->getCommentsForBlog($blog->getId());


        return [
            'blog' => $blog,
//            'comments' => $comments
        ];
    }

    /**
     * @Template()
     */
    public function commentCountAction($blog_id)
    {
        $dql = 'select count( com.id) from BlogBundle:Comment com where com.blog = :blog_id';

        $commentsCount = $this->getDoctrine()->getManager()->createQuery($dql)->setParameters(['blog_id'=>$blog_id])
            ->getSingleScalarResult();

       return ['commentsCount'=>$commentsCount];
    }

    /**
     * @Template()
     */
    public function commentListAction($blog_id)
    {
        $dql = 'select com from BlogBundle:Comment com where com.blog = :blog_id';

        $commentList = $this->getDoctrine()->getManager()->createQuery($dql)->setParameters(['blog_id'=>$blog_id])
            ->getResult();

        return ['commentList'=>$commentList];
    }


}
