<?php
namespace App\Security;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Guard\PasswordAuthenticatedInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\JsonResponse;
Use Symfony\Component\BrowserKit\Response;
use Symfony\Component\Routing\Annotation\Route;

// This page is used to authenticate the user and redirect him to specific pages based on user roles.
class LoginFormAuthenticator extends AbstractFormLoginAuthenticator {
    use TargetPathTrait;
    public const LOGIN_ROUTE = 'app_login';
    private $entityManager;
    private $urlGenerator;
    private $csrfTokenManager;
    private $passwordEncoder;
    private $user_session;
    private $error_message;
	
    public function __construct(EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator, CsrfTokenManagerInterface $csrfTokenManager, UserPasswordEncoderInterface $passwordEncoder) {
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->passwordEncoder = $passwordEncoder;
    }
	
    // Below function is used to redirect user to login after the form post and passess the control to getCredentials function.
    public function supports(Request $request) {
        return self::LOGIN_ROUTE === $request->attributes->get('_route') && $request->isMethod('POST');
    }
	
    // Below function is used get the user credetials
    public function getCredentials(Request $request) {
        $credentials = ['email' => $request->request->get('email'), 'password' => $request->request->get('password'), 'csrf_token' => $request->request->get('_csrf_token'), ];
        $request->getSession()->set(Security::LAST_USERNAME, $credentials['email']);
        return $credentials;
    }
	
    // Below function is used get the user based on the credentials entered by the user. and gives the control to next function
    public function getUser($credentials, UserProviderInterface $userProvider) {
        $token = new CsrfToken('authenticate', $credentials['csrf_token']);
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $credentials['email']]);
        if (!$user) {
            throw new CustomUserMessageAuthenticationException('Email could not be found.');
        }
        return $user;
    }
	
    // Below function is used to validate the password of the user.
    public function checkCredentials($credentials, UserInterface $user) {
        return $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
    }
	
    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function getPassword($credentials): ? string {
            return $credentials['password'];
        }
		
    /**
    * Used to perform the actions uon successfull user athentication, setting the user credentials in session and redirection based on user roles.
    */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey) {
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $request->getSession()->get(Security::LAST_USERNAME) ]);
            $roles = $user->getRoles();
			$request->getSession()->set('session_id',session_create_id());
            $request->getSession()->set('role', $roles['0']);
            $request->getSession()->set('user_id', $user->getId());
            $request->getSession()->set('first_name', $user->getFirstName());
            $request->getSession()->set('last_name', $user->getLastName());
            $request->getSession()->set('username', $user->getUsername());
            if ($request->getSession()->get('role') == '1') {
                return new RedirectResponse($this->urlGenerator->generate('app_product_admin_list'));
            } else {
                return new RedirectResponse($this->urlGenerator->generate('app_product_list'));
            }
        }
		
    /**
    * Fucntion used to set the URL after failure of authentication.
    */
    protected function getLoginUrl() {
            return $this->urlGenerator->generate(self::LOGIN_ROUTE);
        }
		
    public function logout() {
        }
    }
    