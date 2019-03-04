<?php
    namespace App\Controller;

    use Symfony\Component\Form\Form;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\HttpFoundation\Request;

    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

    use App\Entity\BlogPost;


    use Symfony\Component\Form\Extension\Core\Type\TextType;
    use Symfony\Component\Form\Extension\Core\Type\TextareaType;
    use Symfony\Component\Form\Extension\Core\Type\SubmitType;


    class BlogController extends AbstractController{

        /**
         * @Route("/", name="mainPage")
         *
         * Loads the main page with a list of the blog posts and options
         */
        public function loadBlogPosts() {

            $blogPosts = $this->getDoctrine()->getRepository(BlogPost::class)->findAll();
            return $this->render('blogMainPage.html.twig', array('blogPosts' => $blogPosts));
        }



        /**
         * @Route("/newBlogPost")
         *
         * Loads a form to create a blog post and saves it to a database
         */
        public function createNewBlogPost(Request $request) {
            $blogPost = new BlogPost();

            $form = $this->createFormBuilder($blogPost)
                ->add('title', TextType::class)
                ->add('body', TextareaType::class)
                ->add('save', SubmitType::class, array('label' => 'Create'))
                ->getform();

            $form->handleRequest($request);

            if ($this->formIsAcceptedAndFlushed($form)){
                return $this->redirectToRoute('mainPage');
            }


            return $this->render('newBlogPost.html.twig', array('form' => $form->createView()));
        }

        /**
         * @Route("/editBlogPost/{id}")
         *
         * Edit the selected blog post
         */
        public function editBlogPost(Request $request, $id) {

            $blogPost = new BlogPost();
            $blogPost = $this->getDoctrine()->getRepository(BlogPost::class)->find($id);

            $form = $this->createFormBuilder($blogPost)
                ->add('title', TextType::class)
                ->add('body', TextareaType::class)
                ->add('save', SubmitType::class, array('label' => 'Confirm'))
                ->getform();

            $form->handleRequest($request);

            if ($this->formIsAcceptedAndFlushed($form)){
                return $this->redirectToRoute('mainPage');
            }


            return $this->render('editBlogPost.html.twig', array('form' => $form->createView())); 

        }
        

        /**
         * @Route("/blogPost/{id}")
         *
         * Retrieves the data of the selected blog post a shows it
         */
        public function displayBlogPost($id) {
            $blogPost = $this->getDoctrine()->getRepository(BlogPost::class)->find($id);
            return $this->render('displayBlogPost.html.twig', array('blogPost' => $blogPost));
        }




        /**
         * @Route("/deleteBlogPost/{id}")
         *
         * Deletes the selected blog post
         */
        public function deleteBlogPost($id) {
            $blogPost = $this->getDoctrine()->getRepository(BlogPost::class)->find($id);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($blogPost);
            $entityManager->flush();
            
            return $this->redirectToRoute('mainPage');
        }


        // private function for checking forms
        private function formIsAcceptedAndFlushed(Form $form) {
            if ($form->isSubmitted() && $form->isValid()){

                $blogPost = $form->getData();

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($blogPost);
                $entityManager->flush();

                return true;
            }
            return false;
        }
    }