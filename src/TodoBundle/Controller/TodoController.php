<?php
namespace TodoBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use TodoBundle\Entity\Todo;
use TodoBundle\Form\AddType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TodoController extends Controller
{
    /**
     * @Route("/", name="list")
     */
    public function listAction()
    {
        $list = $this->getDoctrine()->getRepository('TodoBundle:Todo')->findBy([], ['addedDate' => 'DESC']);
        return $this->render('list.html.twig', [
            'list' => $list
        ]);
    }

    /**
     * @Route("/add", name="add")
     */
    public function addAction(Request $request)
    {
        $todo = new Todo();
        $form = $this->createForm(AddType::class, $todo);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($data);
            $em->flush();
            return $this->redirect($this->generateUrl('list'));
        }
        return $this->render('add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function editAction($id)
    {
        return $this->render('edit.html.twig');
    }
}