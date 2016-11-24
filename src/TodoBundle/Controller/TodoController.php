<?php
namespace TodoBundle\Controller;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use TodoBundle\Entity\Todo;
use TodoBundle\Form\TodoType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TodoController extends Controller
{
    public function listAction()
    {
        $list = $this->getDoctrine()->getRepository('TodoBundle:Todo')->findBy([], ['addedDate' => 'DESC']);
        return $this->render('TodoBundle:Todo:list.html.twig', [
            'list' => $list
        ]);
    }

    public function addAction(Request $request)
    {
        $todo = new Todo();
        $form = $this->createForm(TodoType::class, $todo);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($todo);
            $em->flush();
            return $this->redirectToRoute('list');
        }
        return $this->render('TodoBundle:Todo:add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    public function editAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $todo = $em->getRepository('TodoBundle:Todo')->find($id);
        if (!$todo) {
            throw $this->createNotFoundException('TODO does not exist!');
        }
        $form = $this->createForm(TodoType::class, $todo);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($todo);
            $em->flush();
            return $this->redirectToRoute('list');
        }
        return $this->render('TodoBundle:Todo:edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    public function realisedAction($id, $action)
    {
        $em = $this->getDoctrine()->getManager();
        $todo = $em->getRepository('TodoBundle:Todo')->find($id);
        if (!$todo) {
            throw $this->createNotFoundException('TODO does not exist!');
        }
        $todo->setRealised($action === 'realised');
        $em->flush();
        return $this->redirectToRoute('list');
    }

    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $todo = $em->getRepository('TodoBundle:Todo')->find($id);
        if (!$todo) {
            throw $this->createNotFoundException('TODO does not exist!');
        }
        $em->remove($todo);
        $em->flush();
        return $this->redirectToRoute('list');
    }
}