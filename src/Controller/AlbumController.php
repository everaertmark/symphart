<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use Doctrine\ORM\EntityManagerInterface;
use App\Serializer\FormErrorSerializer;
use App\Repository\AlbumRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use App\Entity\Album;
use App\Form\AlbumType;

class AlbumController extends AbstractController
{

    /**
     * @var FormErrorSerializer 
     */
    private $formErrorSerializer;

    /**
     * @var AlbumRepository
     */
    private $albumRepository;

    public function __construct(
        FormErrorSerializer $formErrorSerializer,
        AlbumRepository $albumRepository
    ) 
    {
        $this->formErrorSerializer = $formErrorSerializer;
        $this->albumRepository = $albumRepository;
    }

    /**
     * @Route("/album", name="album", methods={"POST"})
     */
    public function index(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $form = $this->createForm(AlbumType::class, new Album());

        $form->submit($data);

        if (false === $form->isValid() ) {
            return new JsonResponse(
                [
                    'status' => 'error',
                    'error' => $this->formErrorSerializer->convertFormToArray($form)
                ],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($form->getData());
        $entityManager->flush();

        return new JsonResponse(
            [
                'status' => 'ok'
            ],
            JsonResponse::HTTP_CREATED
        );
    }

    /**
     * @Route("/album/{id}", name="get_album", methods={"GET"}, requirements={"id"="\d+"})
     * @return object|void
     */
    public function get_album($id) {
        $album = $this->findAlbumById($id);

        return new JsonResponse(
            $album,
            JsonResponse::HTTP_OK
        );
    }

    /**
     * @Route("/albums", name="get_albums", methods={"GET"})
     * @return array
     */
    public function get_albums() {

        $albums = $this->albumRepository->findAll();

        return new JsonResponse(
            $albums,
            JsonResponse::HTTP_OK
        );
    }

    /**
     * @Route("/album/{id}", name="put_album", methods={"PUT"}, requirements={"id"="\d+"})
     */
    public function put(Request $request, $id) 
    {
        $data = json_decode($request->getContent(), true);

        $existingAlbum = $this->findAlbumById($id);

        $form = $this->createForm(AlbumType::class, $existingAlbum);

        //If PATCH, put second paramater as false
        $form->submit($data, true);

        if (false === $form->isValid() ) {
            return new JsonResponse(
                [
                    'status' => 'error',
                    'error' => $this->formErrorSerializer->convertFormToArray($form)
                ],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($form->getData());
        $entityManager->flush();

        return new JsonResponse(
            null,
            JsonResponse::HTTP_NO_CONTENT
        );
    }

    /**
     * @Route("/album/{id}", name="patch_album", methods={"PATCH"}, requirements={"id"="\d+"})
     */
    public function patch(Request $request, $id)
    {
        $data = json_decode($request->getContent(), true);

        $existingAlbum = $this->findAlbumById($id);

        $form = $this->createForm(AlbumType::class, $existingAlbum);

        //If PATCH, put second paramater as false
        $form->submit($data, false);

        if (false === $form->isValid() ) {
            return new JsonResponse(
                [
                    'status' => 'error',
                    'error' => $this->formErrorSerializer->convertFormToArray($form)
                ],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($form->getData());
        $entityManager->flush();

        return new JsonResponse(
            null,
            JsonResponse::HTTP_NO_CONTENT
        );
    }

    /**
     * @Route("/album/{id}", name="delete_album", methods={"DELETE"}, requirements={"id"="\d+"})
     * @param $id
     * @return JsonResponse|null
     */
    public function delete($id) {
        $existingAlbum = $this->findAlbumById($id);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($existingAlbum);
        $entityManager->flush();

        return new JsonResponse(
            null,
            JsonResponse::HTTP_NO_CONTENT
        );
    }

    /**
     * @param $id
     *
     * @return Album|null
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    private function findAlbumById($id) {
        $album = $this->albumRepository->find($id);

        if (null === $album) {
            throw new NotFoundHttpException;
        }

        return $album;
    }
}


// {
// 	"title": "Awesome new Album",
// 	"track_count": 7,
// 	"release_date": "2020-12-05T01:02:03+00:00"
// }


//https://codereviewvideos.com/course/beginners-guide-back-end-json-api-front-end-2018/video/setup-symfony-4-fosrestbundle