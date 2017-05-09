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

class DefaultController extends Controller
{

    /**
     * @Template()
     */
    public function indexAction()
    {
        return [];
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


    public function deleteAction(Request $request, Blog $blog)
    {

        $em = $this->getDoctrine()->getManager();
        $em->remove($blog);
        $em->flush();


        return $this->redirectToRoute("blog_admin.blog_list");
    }

}
