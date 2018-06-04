<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Upload;
use AppBundle\Form\UploadType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\Post;
class DefaultController extends Controller
{
    /**
     *@ApiDoc(
     *      resource=true,
     *     description="Get one single post",
     *      input={
     *          "class"="AppBundle\Form\UploadType",
     *      },
     *     section="posts"
     * )
     * @param Request $request the request object
     * @Post("/upload",name="show_post")
     *
     */
    public function postUploadAction(Request $request)
    {
        $s='http://localhost/PFADEmerde/UploadFiles/web/Upload/';
        $fileSystem = new Filesystem();
        $em=$this->getDoctrine()->getManager();//gerer bd
        $post=new Upload();
        $form=$this->createForm(UploadType::class,$post);
        $form->handleRequest($request);

        if($form->isSubmitted())
        {
            $data=$form->getData();
            //var_dump($data);
            $files=$data->getPhoto();
            $i=1;
            //var_dump($files);
            //var_dump($files->guessExtension());
            foreach ($files as $file){
                $fileName[]= $i.'.'.$file->guessExtension();
                $i++;
            }
            $fileName= $i.'.'.$files->guessExtension();
            $post->setPhoto($fileName);

            $em->persist($post);
            $em->flush();
            //create file
            try {
                $fileSystem->mkdir($this->getParameter('photo_directory').$post->getId());
            } catch (IOExceptionInterface $exception) {
                echo "An error occurred while creating your directory at ".$exception->getPath();
            }
            $i=1;


            $files->move(
                $this->getParameter('photo_directory').$post->getId(),
                $i.'.'.$files->guessExtension()

            );
            foreach ($files as $file){
                $file->move(
                    $this->getParameter('photo_directory').$post->getId(),
                    $i.'.'.$file->guessExtension()

                );
                $i++;
            }
            $s=$s.$post->getId().'/'.$i.'.jpeg';

        }
        // replace this example code with whatever you need
        //return new Response($this->renderView('default/index.html.twig',['form'=>$form->createView()]));
            //url image

        return new Response($s);
    }


}
