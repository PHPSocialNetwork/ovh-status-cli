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

class OvhTask
{
    /**
     * @var int
     */
    protected $taskId;

    /**
     * @var string
     */
    protected $taskUrl;

    /**
     * @var string
     */
    protected $taskProject;

    /**
     * @var string
     */
    protected $taskType;

    /**
     * @var string
     */
    protected $taskCategory;

    /**
     * @var string
     */
    protected $taskSummary;

    /**
     * @var \DateTime
     */
    protected $taskCreationDate;

    /**
     * @var string
     */
    protected $taskStatus;

    /**
     * @return int
     */
    public function getTaskId()
    {
        return $this->taskId;
    }

    /**
     * @param int $taskId
     * @return OvhTask
     */
    public function setTaskId($taskId)
    {
        $this->taskId = $taskId;

        return $this;
    }

    /**
     * @return string
     */
    public function getTaskUrl()
    {
        $clean_url = str_replace('&amp;', '&', trim(OVH_TRAVAUX_URL, '/') . $this->taskUrl);

        $parsed = parse_url($clean_url);
        $query = $parsed[ 'query' ];

        parse_str($query, $params);

        /**
         * Unset annoying parameters
         */
        unset($params[ 'PHPSESSID' ]);

        return OVH_TRAVAUX_URL . '/?' . http_build_query($params);
    }

    /**
     * @param string $taskUrl
     * @return OvhTask
     */
    public function setTaskUrl($taskUrl)
    {
        $this->taskUrl = $taskUrl;

        return $this;
    }

    /**
     * @return string
     */
    public function getTaskProject()
    {
        return $this->taskProject;
    }

    /**
     * @param string $taskProject
     * @return OvhTask
     */
    public function setTaskProject($taskProject)
    {
        $this->taskProject = $taskProject;

        return $this;
    }

    /**
     * @return string
     */
    public function getTaskType()
    {
        return $this->taskType;
    }

    /**
     * @param string $taskType
     * @return OvhTask
     */
    public function setTaskType($taskType)
    {
        $this->taskType = $taskType;

        return $this;
    }

    /**
     * @return string
     */
    public function getTaskCategory()
    {
        return $this->taskCategory;
    }

    /**
     * @param string $taskCategory
     * @return OvhTask
     */
    public function setTaskCategory($taskCategory)
    {
        $this->taskCategory = $taskCategory;

        return $this;
    }

    /**
     * @return string
     */
    public function getTaskSummary()
    {
        return $this->taskSummary;
    }

    /**
     * @param string $taskSummary
     * @return OvhTask
     */
    public function setTaskSummary($taskSummary)
    {
        $this->taskSummary = $taskSummary;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getTaskCreationDate()
    {
        return $this->taskCreationDate;
    }

    /**
     * @param \DateTime $taskCreationDate
     * @return OvhTask
     */
    public function setTaskCreationDate($taskCreationDate)
    {
        $this->taskCreationDate = $taskCreationDate;

        return $this;
    }

    /**
     * @return string
     */
    public function getTaskStatus()
    {
        return $this->taskStatus;
    }

    /**
     * @param string $taskStatus
     * @return OvhTask
     */
    public function setTaskStatus($taskStatus)
    {
        $taskStatus = Helper::getNormalizedString($taskStatus);

        switch ($taskStatus) {
            case 'planned':
            case 'closed':
            case 'in progress':
                $this->taskStatus = $taskStatus;
                break;
            default:
                $this->taskStatus = 'in progress';
                break;
        }

        return $this;
    }

    /**
     * @param simple_html_dom_node $element
     * @return $this|null
     */
    public static function getOvhTaskFromHtmlTableRowElement(simple_html_dom_node $element)
    {
        if (count($element->find('td.task_id'))) {
            $OvhTask = (new self)
              ->setTaskId((int) $element->find('td.task_id')[ 0 ]->plaintext)
              ->setTaskUrl((string) $element->find('td.task_id a')[ 0 ]->href)
              ->setTaskProject((string) $element->find('td.task_project')[ 0 ]->plaintext)
              ->setTaskType((string) $element->find('td.task_tasktype')[ 0 ]->plaintext)
              ->setTaskCategory((string) $element->find('td.task_category')[ 0 ]->plaintext)
              ->setTaskSummary((string) $element->find('td.task_summary')[ 0 ]->plaintext)
              ->setTaskCreationDate((new DateTime())->setTimestamp(strtotime($element->find('td.task_dateopened')[ 0 ]->plaintext)))
              ->setTaskStatus((string) $element->find('td.task_status')[ 0 ]->plaintext);

            return $OvhTask;
        }

        return null;
    }
}