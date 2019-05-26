<?php


namespace App\Services;


use App\Entities\Claim;
use App\Entities\Comment;
use App\Entities\References\ClaimType;
use App\Entities\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Illuminate\Support\Arr;

class ClaimService
{
    private $em;

    /**
     * ClaimService constructor.
     * @param $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Создать жалобу на комментарий
     * @param Comment $comment
     * @param User $user
     * @param int $claimTypeId
     * @return Claim
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function create(Comment $comment, User $user, int $claimTypeId)
    {
        $claim = new Claim;
        $claim->setClaimType($this->em->getReference(ClaimType::class, $claimTypeId));
        $claim->setUser($user);
        $claim->setComment($comment);

        $this->em->persist($claim);
        $this->em->flush();

        return $claim;
    }

    /**
     * Вывести массивом количества жалоб по типам
     * @param Comment $comment
     * @return array
     */
    public function getCommentClaimsCount(Comment $comment)
    {
        //массив, в которм ключ - id типа жалобы, а значение - количество жалоб этого типа
        $claimCountsMap = [];

        foreach ($comment->getClaims() as $claim) {
            //В массив добавляем ключ, соответствующий id типа жалобы,
            //значение (количество жалоб этого типа) инкрементится
            $claimCountsMap[$claim->getClaimType()->getId()] = Arr::get($claimCountsMap, $claim->getClaimType()->getId(), 0) + 1;
        }

        $resultArray = [];

        foreach ($claimCountsMap as $typeId => $claimsCount) {
            $resultArray []= ['claim_type_id' => $typeId, 'claims_count' => $claimsCount];
        }

        return $resultArray;
    }
}