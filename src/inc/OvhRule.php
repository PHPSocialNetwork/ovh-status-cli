<?php
/**
 *
 * @license MIT License (MIT)
 *
 * For full copyright and license information, please see the docs/CREDITS.txt file.
 *
 * @author Georges.L (Geolim4) <contact@geolim4.com>
 *
 */

class OvhRule
{
    /**
     * @var string
     */
    protected $ruleName;

    /**
     * @var array of array
     */
    protected $keywords = [];

    /**
     * @var array of array
     */
    protected $types = ['incident'];

    /**
     * @var array of array
     */
    protected $categories = [];

    /**
     * @var string
     */
    protected $criticality = 'critical';

    /**
     * @var array of array
     */
    protected $projects = [];

    /**
     * @return string
     */
    public function getRuleName()
    {
        return $this->ruleName;
    }

    /**
     * @param string $ruleName
     */
    public function setRuleName($ruleName)
    {
        $this->ruleName = $ruleName;
    }

    /**
     * @return array
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * @param array $keywords
     * @return OvhRule
     * @throws InvalidArgumentException
     */
    public function setKeywords($keywords)
    {
        if (is_array($keywords)) {
            $this->keywords = $keywords;
        } else if (is_string($keywords)) {
            if (strpos($keywords, ',') !== false) {
                $this->keywords = explode(',', $keywords);
            } else {
                $this->keywords = [$keywords];
            }

        } else {
            throw new InvalidArgumentException('Unexpected argument type hint');
        }

        foreach ($this->keywords as &$keyword) {
            $keyword = Helper::getNormalizedString($keyword);
        }

        return $this;
    }

    /**
     * @param $keyword
     * @return bool
     */
    public function isKeywordMatchingString($keyword)
    {
        $keywordsPatterns = $this->getKeywords();

        if (count($keywordsPatterns) === 0 || in_array('all', $keywordsPatterns, true)) {
            return true;
        }

        foreach ($keywordsPatterns as $pattern) {
            if (Helper::wildcardMatch($pattern, Helper::getNormalizedString($keyword))) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array
     */
    public function getTypes()
    {
        return $this->types;
    }

    /**
     * @param array $types
     * @return OvhRule
     * @throws InvalidArgumentException
     */
    public function setTypes($types)
    {
        if (is_array($types)) {
            $this->types = $types;
        } else if (is_string($types)) {
            if (strpos($types, ',') !== false) {
                $this->types = explode(',', $types);
            } else {
                $this->types = [$types];
            }

        } else {
            throw new InvalidArgumentException('Unexpected argument type hint');
        }

        foreach ($this->types as &$type) {
            $type = Helper::getNormalizedString($type);
        }

        return $this;
    }

    /**
     * @param $type
     * @return bool
     */
    public function isTypeMatchingString($type)
    {
        $typesPatterns = $this->getTypes();

        if (count($typesPatterns) === 0 || in_array('all', $typesPatterns, true)) {
            return true;
        }

        foreach ($typesPatterns as $pattern) {
            if ($pattern === Helper::getNormalizedString($type)) {
                return true;
            }
        }

        return false;
    }


    /**
     * @return array
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @param array $keywords
     * @return OvhRule
     */
    public function setCategories($keywords)
    {
        if (is_array($keywords)) {
            $this->categories = $keywords;
        } else if (is_string($keywords)) {
            if (strpos($keywords, ',') !== false) {
                $this->categories = explode(',', $keywords);
            } else {
                $this->categories = [$keywords];
            }

        } else {
            throw new InvalidArgumentException('Unexpected argument type hint');
        }

        foreach ($this->categories as &$category) {
            $category = Helper::getNormalizedString($category);
        }

        return $this;
    }

    /**
     * @param $keyword
     * @return bool
     */
    public function isCategoryMatchingString($keyword)
    {
        $categoriesPatterns = $this->getCategories();

        if (count($categoriesPatterns) === 0 || in_array('all', $categoriesPatterns, true)) {
            return true;
        }

        foreach ($categoriesPatterns as $pattern) {
            if (Helper::wildcardMatch($pattern, Helper::getNormalizedString($keyword))) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return string
     */
    public function getCriticality()
    {
        return $this->criticality;
    }

    /**
     * @param string $criticality
     * @return OvhRule
     */
    public function setCriticality($criticality)
    {
        $criticality = Helper::getNormalizedString($criticality);

        switch ($criticality) {
            case 'disaster':
            case 'critical':
            case 'warning':
                $this->criticality = $criticality;
                break;
            default:
                $this->criticality = 'critical';
                break;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getProjects()
    {
        return $this->projects;
    }

    /**
     * @param array $projects
     * @return OvhRule
     * @throws InvalidArgumentException
     */
    public function setProjects($projects)
    {
        if (is_array($projects)) {
            $this->projects = $projects;
        } else if (is_string($projects)) {
            if (strpos($projects, ',') !== false) {
                $this->projects = explode(',', $projects);
            } else {
                $this->projects = [$projects];
            }

        } else {
            throw new InvalidArgumentException('Unexpected argument type hint');
        }

        foreach ($this->projects as &$project) {
            $project = Helper::getNormalizedString($project);
        }

        return $this;
    }

    /**
     * @param $project
     * @return bool
     */
    public function isProjectMatchingString($project)
    {
        $projectsPatterns = $this->getProjects();

        if (in_array('all', $projectsPatterns, true)) {
            return true;
        }

        foreach ($projectsPatterns as $pattern) {
            if (Helper::wildcardMatch($pattern, Helper::getNormalizedString($project))) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $criteria
     * @return bool
     */
    public function criteriaExists($criteria)
    {
        return property_exists($this, $criteria);
    }

    /**
     *
     */
    public function printKoMatchingRule(OvhTask $OvhTask)
    {
        $truncateLenght = 40;
        echo '[' . strtoupper($this->getCriticality()) . '] Matching rule ***'
          . $this->ruleName
          . '*** for OVH task #'
          . $OvhTask->getTaskId()
          . ' at '
          . $OvhTask->getTaskUrl()
          . ' with summary '
          . '"'
          . mb_substr($OvhTask->getTaskSummary(), 0, $truncateLenght) . (mb_strlen($OvhTask->getTaskSummary()) > $truncateLenght ? '[...]' : '')
          . '"'
          . "\n";
    }

    /**
     *
     */
    public function printOkMatchingRule(OvhTask $OvhTask)
    {
        $truncateLenght = 40;
        echo '[CLOSED] Matching rule ***'
          . $this->ruleName
          . '*** for OVH task #'
          . $OvhTask->getTaskId()
          . ' at '
          . $OvhTask->getTaskUrl()
          . ' with summary '
          . '"'
          . mb_substr($OvhTask->getTaskSummary(), 0, $truncateLenght) . (mb_strlen($OvhTask->getTaskSummary()) > $truncateLenght ? '[...]' : '')
          . '"'
          . "\n";
    }

    /**
     *
     */
    public function printPlannedMatchingRule(OvhTask $OvhTask)
    {
        $truncateLenght = 40;
        echo '[PLANNED] Matching rule ***'
          . $this->ruleName
          . '*** for OVH task #'
          . $OvhTask->getTaskId()
          . ' at '
          . $OvhTask->getTaskUrl()
          . ' with summary '
          . '"'
          . mb_substr($OvhTask->getTaskSummary(), 0, $truncateLenght) . (mb_strlen($OvhTask->getTaskSummary()) > $truncateLenght ? '[...]' : '')
          . '"'
          . "\n";
    }

    /**
     * @return \OvhRule
     */
    public static function getOvhRuleFromArray(array $array)
    {
        $OvhRule = new self;

        foreach ($array as $criteria => $value) {
            // list($criteria, $value) = explode(':', $item);
            if ($OvhRule->criteriaExists($criteria)) {
                $OvhRule->{'set' . strtolower(ucfirst($criteria))}($value);
            }
        }

        return $OvhRule;
    }
}