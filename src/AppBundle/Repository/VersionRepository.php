<?php

namespace AppBundle\Repository;

/**
 * VersionRepository.
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class VersionRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * Find all versions available for the given user.
     * They'll be put in a RSS feed.
     *
     * @param int $userId
     *
     * @return array
     */
    public function findForUser($userId)
    {
        return $this->createQueryBuilder('v')
            ->select('v.tagName', 'v.createdAt', 'v.body', 'r.fullName', 'r.ownerAvatar', 'r.ownerAvatar', 'r.homepage', 'r.language', 'r.description')
            ->leftJoin('v.repo', 'r')
            ->leftJoin('r.stars', 's')
            ->where('s.user = :userId')->setParameter('userId', $userId)
            ->orderBy('v.createdAt', 'desc')
            ->setMaxResults(20)
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * Retrieve latest version of each repo for a user_id with pagination.
     *
     * @param int $userId User ID
     * @param int $offset
     * @param int $length
     *
     * @return array
     */
    public function findLastVersionForEachRepoForUser($userId, $offset = 0, $length = 30)
    {
        $query = 'SELECT v1.tagName, v1.name, v1.createdAt, r.fullName, r.description, r.ownerAvatar, v1.prerelease ' . $this->getBaseQueryForLastVersionForEachRepoForUser();

        return $this->getEntityManager()->createQuery($query)
            ->setFirstResult($offset)
            ->setMaxResults($length)
            ->setParameter('userId', $userId)
            ->getArrayResult();
    }

    /**
     * Return total lines for latest version of each repo for a user_id with pagination.
     *
     * @param int $userId User ID
     *
     * @return int
     */
    public function countLastVersionForEachRepoForUser($userId)
    {
        $query = 'SELECT count(v1.id) ' . $this->getBaseQueryForLastVersionForEachRepoForUser();

        return (int) $this->getEntityManager()->createQuery($query)
            ->setParameter('userId', $userId)
            ->getSingleScalarResult();
    }

    /**
     * DQL query to retrieve last version of each repo starred by a user.
     * We use DQL because it was to complex to use a query builder.
     *
     * @return string
     */
    private function getBaseQueryForLastVersionForEachRepoForUser()
    {
        return "FROM AppBundle\Entity\Version v1
            LEFT JOIN AppBundle\Entity\Version v2 WITH ( v1.repo = v2.repo AND v1.createdAt < v2.createdAt )
            LEFT JOIN AppBundle\Entity\Star s WITH s.repo = v1.repo
            LEFT JOIN AppBundle\Entity\Repo r WITH r.id = s.repo
            WHERE v2.repo IS NULL
            AND s.user = :userId
            ORDER BY v1.createdAt DESC";
    }
}
