<?php 
namespace Topxia\Service\Courseware\Dao\Impl;

use Topxia\Service\Common\BaseDao;
use Topxia\Service\Courseware\Dao\CoursewareDao;

class CoursewareDaoImpl extends BaseDao implements CoursewareDao
{
    protected $table = 'courseware';

    protected $serializeFields = array(
        'relatedKnowledgeIds' => 'json',
        'tagIds' => 'json',
        'knowledgeIds' => 'json'
    );

    public function getCourseware($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = ? LIMIT 1";
        $courseware = $this->getConnection()->fetchAssoc($sql,array($id)) ? : null;
        return $courseware ? $this->createSerializer()->unserialize($courseware, $this->serializeFields) : null;
    }

    public function searchCoursewares($conditions, $orderBy, $start, $limit)
    {
        $this->filterStartLimit($start, $limit);
        $builder = $this->_createSearchQueryBuilder($conditions)
            ->select('*')
            ->orderBy($orderBy[0], $orderBy[1])
            ->setFirstResult($start)
            ->setMaxResults($limit);

        return $builder->execute()->fetchAll() ? : array(); 
    }

    public function searchCoursewaresCount($conditions)
    {
        $builder = $this->_createSearchQueryBuilder($conditions)
            ->select('COUNT(id)');
        return $builder->execute()->fetchColumn(0);
    }
    
    public function addCourseware($courseware)
    {
        $courseware = $this->createSerializer()->serialize($courseware,$this->serializeFields);
        $affected = $this->getConnection()->insert($this->table,$courseware);

        if ($affected < 0) {
            throw $this->createDaoException('insert Courseware error.');
        }

        return $this->getCourseware($this->getConnection()->lastInsertId());
    }

    public function updateCourseware($id,$courseware)
    {
        $article = $this->createSerializer()->serialize($courseware, $this->serializeFields);
        $affected = $this->getConnection()->update($this->table, $article, array('id' => $id));

        if ($affected < 0) {
            throw $this->createDaoException('update Courseware error.');
        }

        return $this->getCourseware($id);
    }

    public function deleteCourseware($id)
    {
        return $this->getConnection()->delete($this->table, array('id' => $id));
    }

    private function _createSearchQueryBuilder($conditions)
    {
        if (!empty($conditions['title'])) {
            $conditions['titleLike'] = "%{$conditions['title']}%";
            unset($conditions['title']);
        }

        if (!empty($conditions['tagIds'])) {
            $tagIds = $conditions['tagIds'];
            $conditions['tagsLike'] = "%";
            if (!empty($tagIds[0])) {
                foreach ($tagIds as $tagId) {
                    $id = "\"".$tagId."\"";
                    $conditions['tagsLike'] .= "{$id}*";
                }
            }
            $conditions['tagsLike'] = rtrim(trim($conditions['tagsLike']), '*' );
            $conditions['tagsLike'] .= "%";
            unset($conditions['tagIds']);
        }
        $builder = $this->createDynamicQueryBuilder($conditions)
            ->from($this->table, $this->table)
            ->andWhere('type = :type')
            ->andWhere('title LIKE :titleLike')
            ->andWhere('userId = :userId')
            ->andWhere('categoryId = :categoryId')
            ->andWhere('tagIds LIKE :tagsLike')
            ->andWhere('createdTime >= :startTime')
            ->andWhere('createdTime <= :endTime');
        if(isset($conditions['knowledgeIds'])) {
            if (!empty($conditions['knowledgeIds']) && !empty($conditions['knowledgeIds'][0])) {
                foreach ($conditions['knowledgeIds'] as $key => $knowledgeId) {
                    $id = "\"".$knowledgeId."\"";
                    if ($key > 0 ) {
                        $conditions['knowledgeIdsSql'] .= "OR knowledgeIds LIKE '%$id%'";
                    } else {
                        $conditions['knowledgeIdsSql'] = " knowledgeIds LIKE '%$id%' ";
                    }
                }
                $builder->andStaticWhere($conditions['knowledgeIdsSql']);
            }
            unset($conditions['knowledgeIds']);
        }
        return $builder;
    }
}