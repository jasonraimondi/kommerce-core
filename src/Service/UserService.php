<?php
namespace inklabs\kommerce\Service;

use DateTime;
use inklabs\kommerce\Entity\Pagination;
use inklabs\kommerce\Entity\User;
use inklabs\kommerce\Entity\UserLogin;
use inklabs\kommerce\Entity\UserLoginResultType;
use inklabs\kommerce\Entity\UserToken;
use inklabs\kommerce\Entity\UserTokenType;
use inklabs\kommerce\Exception\EntityNotFoundException;
use inklabs\kommerce\EntityRepository\UserLoginRepositoryInterface;
use inklabs\kommerce\EntityRepository\UserRepositoryInterface;
use inklabs\kommerce\EntityRepository\UserTokenRepositoryInterface;
use inklabs\kommerce\Event\ResetPasswordEvent;
use inklabs\kommerce\Exception\UserLoginException;
use inklabs\kommerce\Lib\Event\EventDispatcherInterface;
use inklabs\kommerce\Lib\UserPasswordValidator;
use inklabs\kommerce\Lib\UuidInterface;

class UserService implements UserServiceInterface
{
    use EntityValidationTrait;

    protected $userSessionKey = 'user';

    /** @var UserRepositoryInterface */
    private $userRepository;

    /** @var UserLoginRepositoryInterface */
    private $userLoginRepository;

    /** @var UserTokenRepositoryInterface */
    private $userTokenRepository;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    public function __construct(
        UserRepositoryInterface $userRepository,
        UserLoginRepositoryInterface $userLoginRepository,
        UserTokenRepositoryInterface $userTokenRepository,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->userRepository = $userRepository;
        $this->userLoginRepository = $userLoginRepository;
        $this->userTokenRepository = $userTokenRepository;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function create(User & $user)
    {
        $this->throwValidationErrors($user);
        $this->userRepository->create($user);
    }

    public function createUserToken(UserToken & $userToken)
    {
        $this->throwValidationErrors($userToken);
        $this->userTokenRepository->create($userToken);
        $this->eventDispatcher->dispatch($userToken->releaseEvents());
    }

    public function update(User & $user)
    {
        $this->throwValidationErrors($user);
        $this->userRepository->update($user);
        $this->eventDispatcher->dispatch($user->releaseEvents());
    }

    public function login($email, $password, $remoteIp)
    {
        $user = $this->getUserOrAssertAndRecordLoginFailure($email, $remoteIp);

        if (! $user->verifyPassword($password)) {
            $this->recordLogin($email, $remoteIp, UserLoginResultType::fail(), $user);
            throw UserLoginException::invalidPassword();
        }

        $this->recordLogin($email, $remoteIp, UserLoginResultType::success(), $user);

        return $user;
    }

    public function loginWithToken($email, $token, $remoteIp)
    {
        $user = $this->getUserOrAssertAndRecordLoginFailure($email, $remoteIp);

        try {
            $userToken = $this->userTokenRepository->findLatestOneByUserId($user->getId());
        } catch (EntityNotFoundException $e) {
            $this->recordLogin($email, $remoteIp, UserLoginResultType::fail(), $user);
            throw UserLoginException::tokenNotFound();
        }

        if (! $userToken->verifyToken($token)) {
            $this->recordLogin($email, $remoteIp, UserLoginResultType::fail(), $user);
            throw UserLoginException::tokenNotValid();
        }

        if (! $userToken->verifyTokenDateValid()) {
            $this->recordLogin($email, $remoteIp, UserLoginResultType::fail(), $user);
            throw UserLoginException::tokenExpired();
        }

        $this->recordLogin($email, $remoteIp, UserLoginResultType::success(), $user);

        return $user;
    }


    /**
     * @param string $email
     * @param string $ip4
     * @param UserLoginResultType $result
     * @param User $user
     */
    protected function recordLogin($email, $ip4, UserLoginResultType $result, User $user = null)
    {
        $userLogin = new UserLogin($result, $email, $ip4, $user);
        $this->userLoginRepository->create($userLogin);
    }

    public function findOneById(UuidInterface $id)
    {
        return $this->userRepository->findOneById($id);
    }

    public function findOneByEmail($email)
    {
        return $this->userRepository->findOneByemail($email);
    }

    public function getAllUsers($queryString = null, Pagination & $pagination = null)
    {
        return $this->userRepository->getAllUsers($queryString, $pagination);
    }

    public function getAllUsersByIds($userIds, Pagination & $pagination = null)
    {
        return $this->userRepository->getAllUsersByIds($userIds, $pagination);
    }

    public function requestPasswordResetToken($email, $userAgent, $ip4)
    {
        $user = $this->userRepository->findOneByEmail($email);

        $userToken = UserToken::createResetPasswordToken(
            $user,
            UserToken::getRandomToken(),
            $userAgent,
            $ip4
        );

        $this->createUserToken($userToken);
    }

    public function changePassword(UuidInterface $userId, $password)
    {
        $user = $this->userRepository->findOneById($userId);

        $userPasswordValidator = new UserPasswordValidator;
        $userPasswordValidator->assertPasswordValid($user, $password);

        $user->setPassword($password);

        $this->update($user);
    }

    /**
     * @param string $email
     * @param string $remoteIp
     * @return User
     * @throws UserLoginException
     */
    private function getUserOrAssertAndRecordLoginFailure($email, $remoteIp)
    {
        try {
            $user = $this->userRepository->findOneByEmail($email);
        } catch (EntityNotFoundException $e) {
            $this->recordLogin($email, $remoteIp, UserLoginResultType::fail());
            throw UserLoginException::userNotFound();
        }

        if (! $user->getStatus()->isActive()) {
            $this->recordLogin($email, $remoteIp, UserLoginResultType::fail());
            throw UserLoginException::userNotActive();
        }

        return $user;
    }
}
