<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Entity\Review;
use App\Form\MovieFormType;
use App\Form\ReviewFormType;
use App\Repository\MovieRepository;
use App\Repository\ReviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Psr\Log\LoggerInterface; // Importuj klasę LoggerInterface


class MoviesController extends AbstractController
{
    private $em;
    private $movieRepository;
    private $reviewRepository;
    public function __construct(MovieRepository $movieRepository, ReviewRepository $reviewRepository, EntityManagerInterface $em)
    {
        $this->movieRepository = $movieRepository;
        $this->reviewRepository = $reviewRepository;
        $this->em = $em;
    }

   
    #[Route('/movies', methods:['GET'], name: 'movies')]
    public function index(): Response
    {
        $movies = $this->movieRepository->findAll();
        
        return $this->render('movies/index.html.twig', [
            'movies' => $movies
        ]);
    }

    #[Route('/movies/create', name: 'create_movie')]
    public function create(Request $request): Response
    {
        $movie = new Movie();
        $form = $this->createForm(MovieFormType::class, $movie);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $newMovie = $form->getData();

            $imagePath = $form->get('imagePath')->getData();
            if($imagePath){
                $newFileName = uniqid() . '.' . $imagePath->guessExtension();

                try{
                    $imagePath->move(
                        $this->getParameter('kernel.project_dir') . '/public/uploads', 
                        $newFileName
                    );
                } catch (FileException $e) {
                    return new Response($e->getMessage());
                }

                $newMovie->setImagePath('/uploads/' . $newFileName);
            }

            $this->em->persist($newMovie);
            $this->em->flush();

            return $this->redirectToRoute('movies');
        }

        return $this->render('movies/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    
    #[Route('/movies/edit/{id}', name: 'edit_movie')]
    public function edit($id, Request $request): Response
    {
        $movie = $this->movieRepository->find($id);
        $form = $this->createForm(MovieFormType::class, $movie);

        $form->handleRequest($request);
        $imagePath = $form->get('imagePath')->getData();

        if($form->isSubmitted() && $form->isValid()){
            if($imagePath) {
               if($movie->getImagePath() !== null) {
                    if(file_exists($this->getParameter('kernel.project_dir') . $movie->getImagePath()
                    )){
                        $this->getParameter('kernel.project_dir') . $movie->getImagePath();

                        $newFileName = uniqid() . '.' . $imagePath->guessExtension();

                        try{
                            $imagePath->move(
                                $this->getParameter('kernel.project_dir') . '/public/uploads', 
                                $newFileName
                            );
                        } catch (FileException $e) {
                            return new Response($e->getMessage());
                        }

                        $movie->setImagePath('/uploads/' . $newFileName);
                        $this->em->flush();

                        return $this->redirectToRoute('movies');
                    }
               }
            } else {
                $movie->setTitle($form->get('title')->getData());
                $movie->setReleaseYear($form->get('releaseYear')->getData());
                $movie->setDescription($form->get('description')->getData());

                $this->em->flush();
                return $this->redirectToRoute('movies');
            }
        }

        return $this->render('movies/edit.html.twig', [
            'movie' => $movie,
            'form' => $form->createView()
        ]);
    }

    #[Route('/movies/delete/{id}', methods:['GET', 'DELETE'],name: 'delete_movie')]
    public function delete($id): Response
    {
        $movie = $this->movieRepository->find($id);
        $this->em->remove($movie);
        $this->em->flush();

        return $this->redirectToRoute('movies');
    }

    #[Route('/movies/{id}', name: 'movie')]
    public function show(Request $request, $id): Response
    {

        
        
        $movie = $this->movieRepository->find($id);
        $reviews = $this->reviewRepository->findBy(['movie_id' => $id]);

        $reviewForm= $this->createForm(ReviewFormType::class);

        $newReview = new Review();
        $reviewForm->handleRequest($request);

        if ($reviewForm->isSubmitted()) {

            $newReview = $reviewForm->getData();

            $username = $this->getUser()->getUserIdentifier();
            $newReview->setUsername((string)$username);
            $movie_id = $id;
            $newReview->setMovieId($movie_id);

            $this->em->persist($newReview);
            $this->em->flush();

            return $this->redirect('/movies/' . $id);
        }

       

        return $this->render('movies/show.html.twig', [
            'movie' => $movie,
            'ratingForm' => $reviewForm->createView(),
            'reviews' => $reviews,
        
        ]);
    }
}