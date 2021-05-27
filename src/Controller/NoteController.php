<?php

namespace App\Controller;

use App\Entity\Note;
use App\Repository\NoteRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

class NoteController extends AbstractController
{
    /**
     * @Route ("/note", name="note", methods={"POST"})
     * @OA\Post (
     *     tags={"Note"}
     * )
     *
     */
    public function showNote(){

    }
}
