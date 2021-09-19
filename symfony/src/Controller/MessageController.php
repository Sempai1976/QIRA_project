<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\MessageType;
use App\Repository\MessageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MessageController extends AbstractController
{
    private $email_host = 'smtp.gmail.com';
    private $email_port = '587';
    private $email_encr = 'tls';
    private $email_user = '';
    private $email_pass = '';

    /**
     * @Route("/", name="home")
     * @param MessageRepository $msgRepository
     * @return Response
     */
    public function index(MessageRepository $msgRepository)
    {
        //$messages = $msgRepository->findAll();
        $em = $this->getDoctrine()->getManager();
        $messages = $em->getRepository(Message::class)->findBy([], ['id' => 'DESC']);

        return $this->render('message/index.html.twig', [
            'messages' => $messages
        ]);
    }

    /**
     * @Route("/add_message", name="add_message")
     * @param Request $request
     */
    public function add(Request $request)
    {
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message, [
            'form_name' => 'add_message'
        ]);
        $form->handleRequest($request);

        if ($request->isMethod('POST')) {
            if ($form->isSubmitted() && $form->isValid()) {

                $transport = (new \Swift_SmtpTransport($this->email_host, $this->email_port, $this->email_encr))
                    ->setUsername($this->email_user)
                    ->setPassword($this->email_pass);
                $mailer = new \Swift_Mailer($transport);

                $EmailNotifyType = 'success';
                $EmailNotifyMessage = 'Email sent successfully!';

                try {
                    $msg = (new \Swift_Message($message->getSubject()))
                        ->setFrom(array($this->email_user => "Qira Test Project"))
                        ->setTo($message->getEmail())
                        ->setContentType("text/html; charset=UTF-8")
                        ->setBody($message->getMessage(), 'text/html');
                    $mailer->send($msg);
                } catch (\Exception $e) {
                    $EmailNotifyType = 'error';
                    $EmailNotifyMessage = $e->getMessage();
                }

                $em = $this->getDoctrine()->getManager();
                $em->persist($message);
                $em->flush();

                $this->addFlash('success', 'Message saved!');
                $this->addFlash($EmailNotifyType, $EmailNotifyMessage);

                return $this->redirect($this->generateUrl('home'));
            }
        }

        return $this->render('message/add.html.twig', [
            'add_message' => $form->createView()
        ]);
    }

    /**
     * @Route("/delete_message/{id}", name="delete_message")
     */
    public function delete($id)
    {
        $em = $this->getDoctrine()->getManager();
        $msg = $em->getRepository(Message::class)->find($id);
        $em->remove($msg);
        $em->flush();

        $this->addFlash('success', 'Message deleted!');

        return $this->redirect($this->generateUrl('home'));
    }
}
