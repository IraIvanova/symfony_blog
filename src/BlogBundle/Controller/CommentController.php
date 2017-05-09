<?php

namespace BlogBundle\Controller;

use BlogBundle\Entity\Blog;
use BlogBundle\Entity\Comment;
use BlogBundle\Form\CommentType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class CommentController extends Controller
{

    protected function getBlog($blog_id)
    {
        $em = $this->getDoctrine()
            ->getManager();

        $blog = $em->getRepository('BlogBundle:Blog')->find($blog_id);

        if (!$blog) {
            throw $this->createNotFoundException('Unable to find Blog post.');
        }

        return $blog;
    }

    /**
     * @Template()
     */
    public function addAction(Request $request, $blog_id)
    {
        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        $user = $this->getUser();

        $comment->setBlogUser($user);
        $blog = $this->getBlog($blog_id);
        $comment->setBlog($blog);

        if ($form->isSubmitted()) {

            $manager = $this->getDoctrine()->getManager();
            if($user == null){
                throw new \InvalidArgumentException('Чтобы оставить комментарий необходимо авторизоваться! ');
            }
            $manager->persist($comment);
            $manager->flush();

            return $this->redirect($this->generateUrl('blog.single_blog', array(
                'id' => $blog_id)));
        }
        return ['comment' => $comment,
            'form' => $form->createView()
        ];

    }

    /**
     * @Template()
     */
    public function editAction(Request $request, $blog_id, $id)
    {
        $comment= $this->getDoctrine()->getManager()->getRepository('BlogBundle:Comment')->find($id);
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        $user = $this->getUser();

        $comment->setBlogUser($user);
        $blog = $this->getBlog($blog_id);
        $comment->setBlog($blog);

        if ($form->isSubmitted()) {

            $manager = $this->getDoctrine()->getManager();

            $manager->persist($comment);
            $manager->flush();

            return $this->redirect($this->generateUrl('blog.single_blog', array(
                'id' => $blog_id)));
        }
        return ['comment' => $comment,
            'form' => $form->createView()
        ];

    }

    public function deleteAction($blog_id, $id)
    {
        $comment= $this->getDoctrine()->getManager()->getRepository('BlogBundle:Comment')->find($id);
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($comment);
        $manager->flush();

        return $this->redirectToRoute('blog_admin.comment_list', array('blog_id'=>$blog_id));

    }

    /**
     * @Template()
     */
    public function testAction($limit = 10)
    {
//        $dql = 'select com from BlogBundle:Comment com';
//
//        $latestComments = $this->getDoctrine()->getManager()->createQuery($dql)->setMaxResults(10)->getResult();



       return ['latestComments'=>$latestComments ];
    }

}


