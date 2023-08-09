<?php

namespace App\Rules;

/**
 * Класс RuleApplier
 *
 * Этот класс отвечает за применение набора предопределенных правил к заданному запросу.
 * У каждой модели может быть свой набор правил, которые находятся в директории "Rules"
 * и организованы по имени модели. При вызове метода `applyRules` класс динамически определяет
 * соответствующие правила для модели и применяет их к запросу.
 *
 * Например, если модель называется "User", класс будет искать правила в директории "Rules/User"
 * и применять каждое из них к предоставленному запросу.
 *
 * @package App\Services
 */
class RuleApplier
{
    public function applyRules($query)
    {
        $modelClass = get_class($query->getModel());
        $ruleNamespace = 'App\\Rules\\' . class_basename($modelClass);

        foreach (glob(app_path('Rules/' . class_basename($modelClass) . '/*.php')) as $ruleFile) {
            $ruleClass = $ruleNamespace . '\\' . basename($ruleFile, '.php');
            if (class_exists($ruleClass)) {
                $rule = new $ruleClass;
                $query = $rule->apply($query);
            }
        }

        return $query;
    }
}
