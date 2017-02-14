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

use phpFastCache\CacheManager;

if(!defined('STDIN') && php_sapi_name() !== 'cli')
{
    die('This script MUST be executed from command line interface (CLI) !');
}

require __DIR__ . '/bootstrap.php';

$rules = parse_ini_file(CONFIG_DIR . 'rules.ini', true);
$cache = CacheManager::Files(['path' => CACHE_DIR]);
$domHtml = file_get_html(OVH_TRAVAUX_URL);
$matchingTasks = [];

foreach($domHtml->find('#tasklist_table tbody  tr') as $element)
{
    if(!empty($element))
    {
        $OvhTask = OvhTask::getOvhTaskFromHtmlTableRowElement($element);
        if($OvhTask !== null)
        {
            foreach ($rules as $ruleName => $ruleAry) {
                $rule = OvhRule::getOvhRuleFromArray($ruleAry);
                $rule->setRuleName($ruleName);

                /**
                 * Does the summary matches with our defined keyword(s) wildcard(s) ?
                 */
                if($rule->isKeywordMatchingString($OvhTask->getTaskSummary())){
                    /**
                     * Does the task type matches with our defined type(s) ?
                     */
                    if($rule->isTypeMatchingString($OvhTask->getTaskType())){
                        /**
                         * Does the project matches with our defined project(s) wildcard(s) ?
                         */
                        if($rule->isProjectMatchingString($OvhTask->getTaskProject())){

                            /**
                             * Does the category matches with our defined category(ies) wildcard(s) ?
                             */
                            if($rule->isCategoryMatchingString($OvhTask->getTaskCategory())){
                                /**
                                 * Does this task already matched with another rules ?
                                 */
                                if(!isset($matchingTasks[$OvhTask->getTaskId()])){
                                    $cacheItem = $cache->getItem('task-' . $OvhTask->getTaskId());
                                    switch($OvhTask->getTaskStatus())
                                    {
                                        case 'closed':
                                            /**
                                             * Check if the task has been already
                                             * recorded in our cache system else
                                             * ignore the task
                                             */
                                            if($cacheItem->isHit()){
                                                $rule->printOkMatchingRule($OvhTask);
                                                $cache->deleteItem($cacheItem->getKey());
                                            }
                                            break;
                                        case 'planned':
                                            if(!$cacheItem->get() || $cacheItem->get() < time() - DELAY_BETWEEN_PLANNED_ALERTS){
                                                $rule->printPlannedMatchingRule($OvhTask);
                                                $cacheItem->set(time())->expiresAfter(3600*24*30);
                                                $cache->saveDeferred($cacheItem);
                                                $matchingTasks[$OvhTask->getTaskId()] = $OvhTask;
                                            }
                                            break;
                                        case 'in progress':
                                            /**
                                             * Avoid spamming by sending
                                             * an alert each 15 minutes
                                             */
                                            if(!$cacheItem->get() || $cacheItem->get() < time() - DELAY_BETWEEN_IN_PROGRESS_ALERTS){
                                                $rule->printKoMatchingRule($OvhTask);
                                                $cacheItem->set(time())->expiresAfter(3600*24*30);
                                                $cache->saveDeferred($cacheItem);
                                                $matchingTasks[$OvhTask->getTaskId()] = $OvhTask;
                                            }
                                            break;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}

/**
 * Commit cache operations
 * on the disk
 */
$cache->commit();

/**
 * THE END :)
 */
if(!count($matchingTasks))
{
    echo "\n[OK] No rules were matching \n";
}
else
{
    echo "\n[KO] At least " . count($matchingTasks) . " rule(s) were matching \n";
}