<?php
namespace TodoBundle\Controller;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use TodoBundle\Entity\Todo;
use TodoBundle\Form\TodoType;
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
        return $this->render('TodoBundle::list.html.twig', [
            'list' => $list
        ]);
    }

    /**
     * @Route("/add", name="add")
     */
    public function addAction(Request $request)
    {
        $todo = new Todo();
        $form = $this->createForm(TodoType::class, $todo);
        $form->add('add', SubmitType::class, [
            'label' => 'Add new TODO',
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($data);
            $em->flush();
            return $this->redirect($this->generateUrl('list'));
        }
        return $this->render('TodoBundle::add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", requirements={"id": "\d+"})
     */
    public function editAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $todo = $em->getRepository('TodoBundle:Todo')->find($id);
        if (!$todo) {
            throw $this->createNotFoundException('TODO does not exist!');
        }
        $form = $this->createForm(TodoType::class, $todo);
        $form->add('add', SubmitType::class, [
            'label' => 'Save this TODO',
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $em->persist($data);
            $em->flush();
            return $this->redirect($this->generateUrl('list'));
        }
        return $this->render('TodoBundle::edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route(
     *     "/edit/{id}/{action}",
     *     name="realised",
     *     requirements={
     *        "id": "\d+",
     *        "action": "realised|unrealised"
     *     }
     * )
     */
    public function realisedAction($id, $action)
    {
        $em = $this->getDoctrine()->getManager();
        $todo = $em->getRepository('TodoBundle:Todo')->find($id);
        if (!$todo) {
            throw $this->createNotFoundException('TODO does not exist!');
        }
        $todo->setRealised($action === 'realised');
        $em->flush();
        return $this->redirect($this->generateUrl('list'));
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $todo = $em->getRepository('TodoBundle:Todo')->find($id);
        if (!$todo) {
            throw $this->createNotFoundException('TODO does not exist!');
        }
        $em->remove($todo);
        $em->flush();
        return $this->redirect($this->generateUrl('list'));
    }
}