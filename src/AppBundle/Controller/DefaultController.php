<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Post1;
use AppBundle\Form\PostType;
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
     *     parameters={
     *           {
     *          "name"="photo",
     *          "dataType"="file",
     *          "required"="true",
     *           "multiple"="true",
     *          "description"="image"
     *         }
     *     },
     *     section="posts"
     * )
     * @param Request $request the request object
     * @Post("/upload",name="show_post")
     *
     */
    public function postUploadAction(Request $request)
    {
        $fileSystem = new Filesystem();
        $em=$this->getDoctrine()->getManager();//gerer bd
        $post=new Post();
        $form=$this->createForm(PostType::class,$post);
        $form->handleRequest($request);
        var_dump($post);

        if($form->isSubmitted()&&$form->isValid())
        {
            var_dump("if");
            $data=$form->getData();
            $files=$data->getPhoto();
            $i=1;
            foreach ($files as $file){
                $fileName[]= $i.'.'.$file->guessExtension();
                $i++;
            }

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
            foreach ($files as $file){
                $file->move(
                    $this->getParameter('photo_directory').$post->getId(),
                    $i.'.'.$file->guessExtension()

                );
                $i++;
            }

        }
        // replace this example code with whatever you need
        return new Response($this->renderView('default/index.html.twig',['form'=>$form->createView()]));
    }

    private function generateUniqueFileName()
    {
        return md5(uniqid());
    }
}
