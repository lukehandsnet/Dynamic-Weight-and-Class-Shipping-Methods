<?php

namespace DWCSM\Handlers;

class TimeBasedWeightHandler {
    private array $timeRules;
    private const DAYS_OF_WEEK = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];

    public function __construct() {
        $this->timeRules = get_option('dwcsm_time_rules', []);
    }

    public function getWeightLimits(string $methodId): array {
        $currentTime = current_time('timestamp');
        $currentDay = strtolower(date('l', $currentTime));
        $currentHour = (int)date('G', $currentTime);
        
        if (!isset($this->timeRules[$methodId])) {
            return [
                'min_weight' => 0,
                'max_weight' => PHP_FLOAT_MAX
            ];
        }

        $rules = $this->timeRules[$methodId];
        
        foreach ($rules as $rule) {
            if ($this->isRuleApplicable($rule, $currentDay, $currentHour)) {
                return [
                    'min_weight' => floatval($rule['min_weight'] ?? 0),
                    'max_weight' => floatval($rule['max_weight'] ?? PHP_FLOAT_MAX)
                ];
            }
        }

        return [
            'min_weight' => 0,
            'max_weight' => PHP_FLOAT_MAX
        ];
    }

    private function isRuleApplicable(array $rule, string $currentDay, int $currentHour): bool {
        // Check if day matches
        if (!empty($rule['days'])) {
            if (!in_array($currentDay, $rule['days'])) {
                return false;
            }
        }

        // Check if time matches
        if (isset($rule['start_hour']) && isset($rule['end_hour'])) {
            $startHour = (int)$rule['start_hour'];
            $endHour = (int)$rule['end_hour'];
            
            if ($endHour < $startHour) {
                // Handles overnight periods
                return $currentHour >= $startHour || $currentHour < $endHour;
            } else {
                return $currentHour >= $startHour && $currentHour < $endHour;
            }
        }

        return true;
    }

    public function saveRule(string $methodId, array $rule): void {
        if (!isset($this->timeRules[$methodId])) {
            $this->timeRules[$methodId] = [];
        }
        
        $this->timeRules[$methodId][] = $rule;
        update_option('dwcsm_time_rules', $this->timeRules);
    }

    public function getRules(string $methodId): array {
        return $this->timeRules[$methodId] ?? [];
    }

    public function deleteRule(string $methodId, int $ruleIndex): bool {
        if (isset($this->timeRules[$methodId][$ruleIndex])) {
            unset($this->timeRules[$methodId][$ruleIndex]);
            $this->timeRules[$methodId] = array_values($this->timeRules[$methodId]);
            update_option('dwcsm_time_rules', $this->timeRules);
            return true;
        }
        return false;
    }
}