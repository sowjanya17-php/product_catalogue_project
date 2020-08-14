<?php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Entity\Product;
use App\Entity\Comment;
use App\Entity\Cart;
use App\Form\ProductType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\RouterInterface;
class ProductController extends AbstractController {
	
	
    // Function to add product from Admin Panel
    
    /**
     * @Route("/product/admin/new", name="product/admin/new")
     */
    public function new (Request $request, SluggerInterface $slugger) {
        $entityManager = $this->getDoctrine()->getManager();
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $brochureFile = $form->get('brochure')->getData();
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $brochureFile->guessExtension();
                try {
                    $brochureFile->move($this->getParameter('brochures_directory'), $newFilename);
                }
                catch(FileException $e) {
                }
                $product->setFilename($newFilename);
                $product->setDateAdded(new \DateTime());
                $product->setUpdatedDate(new \DateTime());
                $entityManager->persist($product);
                $entityManager->flush();
            }
            $this->addFlash('prod_add_sucess', 'Product Added Successfully!');
            return $this->redirect('/product/admin/list/');
        }
        return $this->render('product/new.html.twig', ['form' => $form->createView(), ]);
    }
	
    // Function to list the product in  frontEnd to users
    
    /**
     * @Route("/product/list", name="list")
     */
    public function list() {
        $entityManager = $this->getDoctrine()->getManager();
        // to fetch all the products form databasse
        $products = $entityManager->getRepository(Product::class)->findAll();
        return $this->render('product/list.html.twig', ['products' => $products, 'roleId' => '1']);
    }
	
    // Function to display product details for frontend users
    
    /**
     * @Route("/product/details/{id}", name="details")
     */
    public function details(int $id, Request $request) {
        $comment = new Comment();
		$cart =  new Cart();
        $entityManager = $this->getDoctrine()->getManager();
        //Fetching the product based on product Id
        $product = $entityManager->getRepository(Product::class)->find($id);
        //Fetching all the approved comments added by users
        $comments = $entityManager->getRepository(Comment::class)->findBy(['product_id' => $id, 'approved' => '1']);
        if ($request->attributes->get('_route') === 'details' && $request->isMethod('POST')) {
			//dd($request->request->get('product_comment'));
			if($request->request->get('product_comment')){
            $comment->setCreatedBy($request->getSession()->get('first_name') . " " . $request->getSession()->get('last_name'));
            $comment->setDateAdded(new \DateTime());
            $comment->setApproved('0');
            $comment->setApprovedBy('0');
            $comment->setProductId($id);
            $comment->setComment($request->request->get('product_comment'));
            $entityManager->persist($comment);
            $entityManager->flush();
            $this->addFlash('prod_comments_sucess', 'Comments Added Successfully! You can see comments only after admin approval');
            return $this->render('product/details.html.twig', ['message' => 'You can see comments after admin approval', 'product' => $product, 'comments' => $comments]);
			}else{
			$cart_count =  $entityManager->getRepository(Cart::class)->findBy(['session_id'=>$request->getSession()->get('session_id'),'product_id'=>$product->getId()]);
			if(count($cart_count) == 0)
			{
				$cart->setProductId($product->getId());
				$cart->setProductName($product->getName());
				$cart->setQuantity('1');
				$cart->setPrice($product->getPrice());
				$cart->setTotal($product->getPrice());
				if($request->getSession()->get('TotalPrice'))
				{
					 $totalPrice = ($request->getSession()->get('TotalPrice')+$product->getPrice());
					 $request->getSession()->set('TotalPrice',$totalPrice);
				}else{
					$request->getSession()->set('TotalPrice',$product->getPrice());
				}
				$cart->setDescription($product->getDescription());
				$cart->setSessionId($request->getSession()->get('session_id'));
				$cart->setImageName($product->getFileName());
				$entityManager->persist($cart);
				$entityManager->flush();
			}

			return $this->redirect($this->generateUrl('app_cart_cart'));
			}
        }
        return $this->render('product/details.html.twig', ['product' => $product, 'message' => "", 'comments' => $comments]);
    }
	
    // Function used to list all the products to Admin users
    
    /**
     * @Route("/product/admin/list", name="list_admin")
     */
    public function list_admin(Request $request) {
        $entityManager = $this->getDoctrine()->getManager();
        $products = $entityManager->getRepository(Product::class)->findAll();
        return $this->render('product/admin/list.html.twig', ['products' => $products]);
    }
	
    // Function used to edit the single product
    
    /**
     * @Route("/product/admin/edit/{id}", name="edit_admin")
     */
    public function edit_admin(Request $request, int $id, SluggerInterface $slugger) {
        $entityManager = $this->getDoctrine()->getManager();
        $editproduct = $entityManager->getRepository(Product::class)->find($id);
        $form = $this->createForm(ProductType::class, $editproduct);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $brochureFile = $form->get('brochure')->getData();
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $brochureFile->guessExtension();
                try {
                    $brochureFile->move($this->getParameter('brochures_directory'), $newFilename);
                }
                catch(FileException $e) {
                }
                $editproduct->setFilename($newFilename);
                $editproduct->setUpdatedDate(new \DateTime());
            }
            $entityManager->persist($editproduct);
            $entityManager->flush();
            $this->addFlash('prod_add_sucess', 'Product Updated Successfully!');
            return $this->redirect('/product/admin/list/');
        }
        return $this->render('product/admin/edit.html.twig', ['form' => $form->createView(), 'image' => $editproduct->getFileName() ]);
    }
	
    // Function used to display product details for admin users
    
    /**
     * @Route("/product/admin/details/{id}", name="details_admin")
     */
    public function details_admin(int $id, Request $request) {
        $comment = new Comment();
        $message = "";
        $entityManager = $this->getDoctrine()->getManager();
        $product = $entityManager->getRepository(Product::class)->find($id);
        $comments = $entityManager->getRepository(Comment::class)->findBy(['product_id' => $id]);
        if ($request->attributes->get('_route') === 'details_admin' && $request->isMethod('POST')) {
            if ($request->request->get('submit_value') == "approve" || $request->request->get('submit_value') == "reject") {
                $comment_single_row = $entityManager->getRepository(Comment::class)->findOneBy(['id' => $request->request->get('updated_comment_id') ]);
                if ($request->request->get('submit_value') == "approve") {
                    $comment_single_row->setApproved("1");
                    $this->addFlash('comments_approval', 'Comment Approved Successfully!');
                } else {
                    $comment_single_row->setApproved("2");
                    $this->addFlash('comments_approval', 'Comment Rejected Successfully!');
                }
                $entityManager->persist($comment_single_row);
                $result = $entityManager->flush();
                $comments = $entityManager->getRepository(Comment::class)->findBy(['product_id' => $id]);
                return $this->render('product/admin/details.html.twig', ['product' => $product, 'user_comments' => $comments]);
            }
        }
        return $this->render('product/admin/details.html.twig', ['product' => $product, 'message' => "", 'user_comments' => $comments]);
    }
	
    // Function to delete specific product by using product id
    
    /**
     * @Route("/product/admin/delete", name="product_admin_delete")
     */
    public function delete(Request $request, SluggerInterface $slugger) {
        $entityManager = $this->getDoctrine()->getManager();
        if ($request->query->get('product_id')) {
            $product_id = $request->query->get('product_id');
            $prod_del = $entityManager->getRepository(Product::class)->findOneBy(['id' => $product_id]);
            $entityManager->remove($prod_del);
            $entityManager->flush();
            $this->addFlash('success', 'Product delete Successfully!');
            $products = $entityManager->getRepository(Product::class)->findAll();
            $this->addFlash('prod_add_sucess', 'Product Deleted Successfully!');
            return $this->redirect($this->generateUrl('app_product_admin_list'));
        }
    }
}
